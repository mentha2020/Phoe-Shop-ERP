<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with(['branch', 'product', 'productVariant']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'low_stock' => $query->lowStock(),
                'out_of_stock' => $query->outOfStock(),
                default => null,
            };
        }

        $stocks = $query->orderBy('quantity')->paginate(30);
        $branches = Branch::active()->get();
        $totalValue = (clone $query)->sum(DB::raw('quantity * (SELECT purchase_price FROM products WHERE products.id = stocks.product_id)'));

        return view('admin.stock.index', compact('stocks', 'branches', 'totalValue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'required|integer|min:0',
        ]);

        $stock = Stock::updateOrCreate(
            [
                'branch_id' => $validated['branch_id'],
                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['product_variant_id'] ?? null,
            ],
            [
                'quantity' => $validated['quantity'],
                'min_stock' => $validated['min_stock'],
                'max_stock' => $validated['max_stock'],
            ]
        );

        return redirect()->route('admin.stock.index')->with('success', 'Stock updated successfully.');
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['branch', 'product', 'productVariant', 'user']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $movements = $query->latest()->paginate(30);
        $branches = Branch::active()->get();
        $products = Product::active()->orderBy('name')->get();

        return view('admin.stock.movements', compact('movements', 'branches', 'products'));
    }

    public function adjustments(Request $request)
    {
        $query = StockAdjustment::with(['branch', 'createdBy']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $adjustments = $query->latest()->paginate(20);
        $branches = Branch::active()->get();

        return view('admin.stock.adjustments', compact('adjustments', 'branches'));
    }

    public function adjustmentCreate()
    {
        $branches = Branch::active()->get();
        $products = Product::active()->with('variants')->orderBy('name')->get();

        return view('admin.stock.adjustment-create', compact('branches', 'products'));
    }

    public function adjustmentStore(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:addition,subtraction,damage,expired,lost,other',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated) {
            $adjustment = StockAdjustment::create([
                'branch_id' => $validated['branch_id'],
                'type' => $validated['type'],
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $validated['branch_id'],
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'] ?? null,
                    ],
                    ['quantity' => 0]
                );

                $qtyBefore = $stock->quantity;
                $adjustmentQty = abs($item['quantity']);

                if (in_array($validated['type'], ['subtraction', 'damage', 'expired', 'lost'])) {
                    $stock->decrement('quantity', $adjustmentQty);
                } else {
                    $stock->increment('quantity', $adjustmentQty);
                }

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity' => in_array($validated['type'], ['subtraction', 'damage', 'expired', 'lost']) ? -$adjustmentQty : $adjustmentQty,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $stock->quantity,
                ]);

                StockMovement::create([
                    'branch_id' => $validated['branch_id'],
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'type' => 'adjustment',
                    'quantity' => in_array($validated['type'], ['subtraction', 'damage', 'expired', 'lost']) ? -$adjustmentQty : $adjustmentQty,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $stock->quantity,
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'notes' => $validated['reason'] ?? null,
                    'user_id' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('admin.stock-adjustments.index')->with('success', 'Stock adjustment created successfully.');
    }

    public function transfers(Request $request)
    {
        $query = StockTransfer::with(['fromBranch', 'toBranch', 'createdBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->latest()->paginate(20);

        return view('admin.stock.transfers', compact('transfers'));
    }

    public function transferCreate()
    {
        $branches = Branch::active()->get();
        $products = Product::active()->with('variants')->orderBy('name')->get();

        return view('admin.stock.transfer-create', compact('branches', 'products'));
    }

    public function transferStore(Request $request)
    {
        $validated = $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|not_in:' . $request->from_branch_id,
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $transfer = StockTransfer::create([
                'from_branch_id' => $validated['from_branch_id'],
                'to_branch_id' => $validated['to_branch_id'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity_sent' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('admin.stock-transfers.index')->with('success', 'Stock transfer created successfully.');
    }

    public function transferReceive(StockTransfer $transfer)
    {
        if ($transfer->status !== 'in_transit') {
            return back()->with('error', 'This transfer cannot be received.');
        }

        DB::transaction(function () use ($transfer) {
            $transfer->items->each(function ($item) use ($transfer) {
                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $transfer->to_branch_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                    ],
                    ['quantity' => 0]
                );

                $qtyBefore = $stock->quantity;
                $stock->increment('quantity', $item->quantity_sent);
                $item->update(['quantity_received' => $item->quantity_sent]);

                StockMovement::create([
                    'branch_id' => $transfer->to_branch_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'type' => 'transfer_in',
                    'quantity' => $item->quantity_sent,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $stock->quantity,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'notes' => 'Received from transfer ' . $transfer->reference_number,
                    'user_id' => auth()->id(),
                ]);
            });

            $transfer->update([
                'status' => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);
        });

        return redirect()->route('admin.stock-transfers.index')->with('success', 'Transfer received successfully. Stock updated.');
    }
}
