<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepairJob;
use App\Models\RepairPart;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $query = RepairJob::with(['customer', 'assignedTo', 'branch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('device_brand', 'like', "%{$search}%")
                  ->orWhere('device_model', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $repairs = $query->latest()->paginate(30);

        return view('admin.repairs.index', compact('repairs'));
    }

    public function create()
    {
        $customers = Customer::active()->orderBy('name')->get();
        $branches = Branch::active()->get();
        $technicians = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Technician', 'Super Admin', 'Admin']);
        })->get();
        $products = Product::active()->orderBy('name')->get();

        return view('admin.repairs.create', compact('customers', 'branches', 'technicians', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'device_type' => 'required|string|max:100',
            'device_brand' => 'required|string|max:100',
            'device_model' => 'required|string|max:100',
            'device_serial' => 'nullable|string|max:100',
            'device_password' => 'nullable|string|max:100',
            'issue_description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'estimated_completion' => 'nullable|date|after:today',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $repair = RepairJob::create([
            ...$validated,
            'status' => 'received',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.repairs.show', $repair->id)->with('success', 'Repair job created successfully!');
    }

    public function show(RepairJob $repair)
    {
        $repair->load(['customer', 'assignedTo', 'creator', 'branch', 'parts.product']);

        return view('admin.repairs.show', compact('repair'));
    }

    public function update(Request $request, RepairJob $repair)
    {
        $validated = $request->validate([
            'diagnosis' => 'nullable|string',
            'resolution' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'final_cost' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'estimated_completion' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $repair->update($validated);

        return back()->with('success', 'Repair job updated successfully!');
    }

    public function updateStatus(RepairJob $repair, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:diagnosed,in_progress,waiting_parts,completed,delivered,cancelled',
            'diagnosis' => 'nullable|string',
            'resolution' => 'nullable|string',
            'final_cost' => 'nullable|numeric|min:0',
        ]);

        $updateData = ['status' => $validated['status']];

        if (isset($validated['diagnosis'])) {
            $updateData['diagnosis'] = $validated['diagnosis'];
        }
        if (isset($validated['resolution'])) {
            $updateData['resolution'] = $validated['resolution'];
        }
        if (isset($validated['final_cost'])) {
            $updateData['final_cost'] = $validated['final_cost'];
        }

        if ($validated['status'] === 'completed') {
            $updateData['actual_completion'] = now()->toDateString();
        }
        if ($validated['status'] === 'delivered') {
            $updateData['delivered_at'] = now()->toDateString();
        }

        $repair->update($updateData);

        return back()->with('success', 'Repair status updated to ' . $repair->status_label . '.');
    }

    public function addPart(Request $request, RepairJob $repair)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'part_name' => 'required|string|max:200',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        RepairPart::create([
            ...$validated,
            'repair_job_id' => $repair->id,
        ]);

        return back()->with('success', 'Part added successfully!');
    }

    public function removePart(RepairPart $part)
    {
        $part->delete();

        return back()->with('success', 'Part removed.');
    }

    public function recordDeposit(Request $request, RepairJob $repair)
    {
        $validated = $request->validate([
            'deposit_amount' => 'required|numeric|min:0.01',
        ]);

        $repair->increment('deposit_amount', $validated['deposit_amount']);

        return back()->with('success', 'Deposit recorded successfully!');
    }
}
