<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with(['customer', 'creator', 'branch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $quotations = $query->latest()->paginate(30);

        return view('admin.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::active()->orderBy('name')->get();
        $branches = Branch::active()->get();
        $products = Product::active()->orderBy('name')->get();

        return view('admin.quotations.create', compact('customers', 'branches', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'valid_until' => 'nullable|date|after:today',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
        ]);

        $quotation = DB::transaction(function () use ($validated) {
            $quotation = Quotation::create([
                'branch_id' => $validated['branch_id'],
                'customer_id' => $validated['customer_id'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = ($item['unit_price'] * $item['quantity']) - ($item['discount_amount'] ?? 0);

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'subtotal' => $subtotal,
                ]);
            }

            $quotation->calculateTotals();

            return $quotation;
        });

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation created successfully!');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['items.product', 'customer', 'branch', 'creator', 'convertedSale']);

        return view('admin.quotations.show', compact('quotation'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!in_array($quotation->status, ['draft', 'sent'])) {
            return back()->with('error', 'Only draft or sent quotations can be updated.');
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'valid_until' => 'nullable|date|after:today',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $quotation) {
            $quotation->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $quotation->items()->delete();

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $subtotal = ($item['unit_price'] * $item['quantity']) - ($item['discount_amount'] ?? 0);

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'subtotal' => $subtotal,
                ]);
            }

            $quotation->calculateTotals();
        });

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation updated successfully!');
    }

    public function updateStatus(Quotation $quotation, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:sent,accepted,rejected,expired',
        ]);

        $quotation->update(['status' => $validated['status']]);

        return back()->with('success', 'Quotation status updated to ' . ucfirst($validated['status']) . '.');
    }

    public function convertToSale(Quotation $quotation)
    {
        if ($quotation->status !== 'accepted') {
            return back()->with('error', 'Only accepted quotations can be converted to sales.');
        }

        $sale = DB::transaction(function () use ($quotation) {
            $sale = Sale::create([
                'branch_id' => $quotation->branch_id,
                'customer_id' => $quotation->customer_id,
                'status' => 'completed',
                'discount_amount' => $quotation->discount_amount,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'payment_method' => 'pending',
                'notes' => "Converted from quotation {$quotation->quotation_number}",
                'created_by' => auth()->id(),
            ]);

            foreach ($quotation->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount_amount' => $item->discount_amount,
                    'subtotal' => $item->subtotal,
                ]);

                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $quotation->branch_id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                    ],
                    ['quantity' => 0]
                );

                $qtyBefore = $stock->quantity;
                $stock->decrement('quantity', $item->quantity);
                $stock->refresh();

                StockMovement::create([
                    'branch_id' => $quotation->branch_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'type' => 'sale',
                    'quantity' => -$item->quantity,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $stock->quantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'user_id' => auth()->id(),
                ]);
            }

            $sale->calculateTotals();

            $quotation->update([
                'status' => 'converted',
                'converted_sale_id' => $sale->id,
            ]);

            return $sale;
        });

        return redirect()->route('admin.sales.show', $sale->id)->with('success', 'Quotation converted to sale successfully!');
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Converted quotations cannot be deleted.');
        }

        $quotation->delete();

        return redirect()->route('admin.quotations.index')->with('success', 'Quotation deleted successfully.');
    }
}
