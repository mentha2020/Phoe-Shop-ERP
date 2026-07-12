<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id', auth()->user()->branch_id ?? Branch::first()->id);
        $branch = Branch::find($branchId);

        $products = Product::with(['category', 'brand', 'stocks'])
            ->active()
            ->whereHas('stocks', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->where('quantity', '>', 0);
            })
            ->orderBy('name')
            ->get();

        $products->each(function ($product) use ($branchId) {
            $stock = $product->stocks->where('branch_id', $branchId)->first();
            $product->available_quantity = $stock ? $stock->quantity : 0;
        });

        $categories = \App\Models\Category::active()->orderBy('name')->get();
        $customers = Customer::active()->orderBy('name')->get();
        $branches = Branch::active()->get();

        return view('admin.pos.index', compact('products', 'categories', 'customers', 'branches', 'branch', 'branchId'));
    }

    public function searchProducts(Request $request)
    {
        $branchId = $request->get('branch_id');
        $search = $request->get('search', '');
        $categoryId = $request->get('category_id');

        $products = Product::with(['category', 'brand', 'stocks'])
            ->active()
            ->whereHas('stocks', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->where('quantity', '>', 0);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%")
                       ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->limit(50)
            ->get();

        $products->each(function ($product) use ($branchId) {
            $stock = $product->stocks->where('branch_id', $branchId)->first();
            $product->available_quantity = $stock ? $stock->quantity : 0;
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer,e_wallet,credit,mixed',
            'paid_amount' => 'required|numeric|min:0',
            'due_date' => 'required_if:payment_method,credit|nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        if (($validated['payment_method'] ?? '') === 'credit') {
            if (empty($validated['customer_id'])) {
                return response()->json(['message' => 'Customer is required for credit sales.'], 422);
            }
        }

        $sale = DB::transaction(function () use ($validated) {
            $isCredit = ($validated['payment_method'] ?? '') === 'credit';

            $sale = Sale::create([
                'branch_id' => $validated['branch_id'],
                'customer_id' => $validated['customer_id'] ?? null,
                'status' => $isCredit ? 'pending' : 'completed',
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'shipping_amount' => $validated['shipping_amount'] ?? 0,
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $validated['paid_amount'],
                'due_date' => $validated['due_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = ($item['unit_price'] * $item['quantity']) - ($item['discount_amount'] ?? 0);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'subtotal' => $subtotal,
                ]);

                // Update stock
                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $validated['branch_id'],
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'] ?? null,
                    ],
                    ['quantity' => 0]
                );

                $qtyBefore = $stock->quantity;
                $stock->decrement('quantity', $item['quantity']);
                $stock->refresh();

                StockMovement::create([
                    'branch_id' => $validated['branch_id'],
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $stock->quantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'user_id' => auth()->id(),
                ]);
            }

            // Record payment first so calculateTotals() can sum it
            SalePayment::create([
                'sale_id' => $sale->id,
                'method' => $validated['payment_method'],
                'amount' => $validated['paid_amount'],
                'received_by' => auth()->id(),
            ]);

            $sale->calculateTotals();

            // Update customer balance for credit sales
            if ($isCredit && $validated['customer_id']) {
                $dueAmount = $sale->total - $sale->paid_amount;
                if ($dueAmount > 0) {
                    Customer::where('id', $validated['customer_id'])
                        ->increment('current_balance', $dueAmount);
                }
            }

            return $sale;
        });

        return response()->json([
            'success' => true,
            'message' => 'Sale completed successfully!',
            'redirect' => route('admin.pos.receipt', $sale->id),
        ]);
    }

    public function receipt($id)
    {
        $sale = Sale::with(['items.product', 'customer', 'branch', 'creator', 'payments.receiver'])
            ->findOrFail($id);

        return view('admin.pos.receipt', compact('sale'));
    }

    public function history(Request $request)
    {
        $query = Sale::with(['customer', 'creator', 'branch']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $sales = $query->latest()->paginate(30);

        return view('admin.pos.history', compact('sales'));
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return back()->with('error', 'Only completed sales can be cancelled.');
        }

        DB::transaction(function () use ($sale) {
            // Reverse stock movements
            foreach ($sale->items as $item) {
                $stock = Stock::where('branch_id', $sale->branch_id)
                    ->where('product_id', $item->product_id)
                    ->where('product_variant_id', $item->product_variant_id)
                    ->first();

                if ($stock) {
                    $qtyBefore = $stock->quantity;
                    $stock->increment('quantity', $item->quantity);
                    $stock->refresh();

                    StockMovement::create([
                        'branch_id' => $sale->branch_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'type' => 'return',
                        'quantity' => $item->quantity,
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $stock->quantity,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'notes' => 'Sale cancelled - ' . $sale->invoice_number,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            $sale->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Sale cancelled and stock restored.');
    }
}
