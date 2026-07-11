<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SupplierPayment;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'branch', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->latest()->paginate(20);
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.purchase.index', compact('orders', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $branches = Branch::active()->get();
        $products = Product::active()->with('variants')->orderBy('name')->get();

        return view('admin.purchase.create', compact('suppliers', 'branches', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'branch_id' => 'required|exists:branches,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'branch_id' => $validated['branch_id'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $qty = $item['quantity'];
                $cost = $item['unit_cost'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity_ordered' => $qty,
                    'unit_cost' => $cost,
                    'total_cost' => $qty * $cost,
                ]);
            }

            $order->calculateTotals();

            return $order;
        });

        activity('purchase')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Created purchase order: ' . $order->reference_number);

        return redirect()->route('admin.purchase-orders.index')->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $order)
    {
        $order->load(['supplier', 'branch', 'items.product', 'items.productVariant', 'createdBy', 'payments', 'returns']);
        $activityLogs = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', \App\Models\PurchaseOrder::class)
            ->where('subject_id', $order->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.purchase.show', compact('order', 'activityLogs'));
    }

    public function approve(PurchaseOrder $order)
    {
        if ($order->status !== 'draft' && $order->status !== 'pending') {
            return back()->with('error', 'Only draft or pending orders can be approved.');
        }

        $order->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        activity('purchase')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Approved purchase order: ' . $order->reference_number);

        return back()->with('success', 'Purchase order approved.');
    }

    public function receive(Request $request, PurchaseOrder $order)
    {
        if (!in_array($order->status, ['approved', 'partial'])) {
            return back()->with('error', 'Only approved or partial orders can receive items.');
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $order) {
            foreach ($validated['items'] as $itemData) {
                $item = PurchaseOrderItem::findOrFail($itemData['id']);
                $receivedQty = min($itemData['quantity'], $item->quantity_pending);

                if ($receivedQty <= 0) continue;

                $item->increment('quantity_received', $receivedQty);

                // Update or create stock
                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $order->branch_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                    ],
                    ['quantity' => 0]
                );

                $qtyBefore = $stock->quantity;
                $stock->increment('quantity', $receivedQty);

                // Log stock movement
                StockMovement::create([
                    'branch_id' => $order->branch_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'type' => 'purchase',
                    'quantity' => $receivedQty,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $stock->quantity,
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $order->id,
                    'notes' => 'PO: ' . $order->reference_number,
                    'user_id' => auth()->id(),
                ]);
            }

            // Check if fully received
            $order->load('items');
            $allReceived = $order->items->every(fn($item) => $item->quantity_received >= $item->quantity_ordered);
            $anyReceived = $order->items->some(fn($item) => $item->quantity_received > 0);

            $newStatus = match(true) {
                $allReceived => 'received',
                $anyReceived => 'partial',
                default => $order->status,
            };

            $order->update([
                'status' => $newStatus,
                'received_date' => $newStatus === 'received' ? now() : $order->received_date,
            ]);
        });

        activity('purchase')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Received items for purchase order: ' . $order->reference_number);

        return back()->with('success', 'Items received and stock updated.');
    }

    public function cancel(PurchaseOrder $order)
    {
        if (in_array($order->status, ['received', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this order.');
        }

        $order->update(['status' => 'cancelled']);

        activity('purchase')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->log('Cancelled purchase order: ' . $order->reference_number);

        return back()->with('success', 'Purchase order cancelled.');
    }

    public function recordPayment(Request $request, PurchaseOrder $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,credit_card,other',
            'payment_date' => 'required|date',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validated['amount'] > $order->balance) {
            return back()->with('error', 'Payment amount exceeds the outstanding balance of $' . number_format($order->balance, 2));
        }

        DB::transaction(function () use ($validated, $order) {
            SupplierPayment::create([
                'supplier_id' => $order->supplier_id,
                'purchase_order_id' => $order->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $order->increment('paid_amount', $validated['amount']);
        });

        activity('purchase')
            ->performedOn($order)
            ->causedBy(auth()->user())
            ->withProperties(['amount' => $validated['amount']])
            ->log('Recorded payment of $' . number_format($validated['amount'], 2) . ' for PO: ' . $order->reference_number);

        return back()->with('success', 'Payment recorded successfully.');
    }
}
