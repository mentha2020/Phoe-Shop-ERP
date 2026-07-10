<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'branch', 'creator']);

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->latest('expense_date')->paginate(30);
        $categories = ExpenseCategory::active()->orderBy('name')->get();
        $branches = Branch::active()->get();
        $totalExpenses = $expenses->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'categories', 'branches', 'totalExpenses'));
    }

    public function create()
    {
        $categories = ExpenseCategory::active()->orderBy('name')->get();
        $branches = Branch::active()->get();

        return view('admin.expenses.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,transfer,e_wallet',
            'receipt_number' => 'nullable|string|max:100',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,quarterly,yearly',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        Expense::create($validated);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded successfully!');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'branch', 'creator']);

        return view('admin.expenses.show', compact('expense'));
    }

    public function updateStatus(Expense $expense, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $expense->update(['status' => $validated['status']]);

        return back()->with('success', 'Expense status updated to ' . ucfirst($validated['status']) . '.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted successfully.');
    }

    // Categories
    public function categories()
    {
        $categories = ExpenseCategory::withCount('expenses')->orderBy('name')->get();
        return view('admin.expenses.categories', compact('categories'));
    }

    public function categoryStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($validated);

        return back()->with('success', 'Category created successfully!');
    }

    public function categoryUpdate(Request $request, ExpenseCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200|unique:expense_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return back()->with('success', 'Category updated successfully!');
    }

    public function categoryDestroy(ExpenseCategory $category)
    {
        if ($category->expenses()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing expenses.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully!');
    }
}
