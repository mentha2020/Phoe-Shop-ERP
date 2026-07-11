<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'creator', 'branch']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sales = $query->latest()->paginate(30);
        $totalSales = $sales->total();
        $totalAmount = $sales->sum('total');
        $totalPaid = $sales->sum('paid_amount');

        return view('admin.sales.index', compact('sales', 'totalSales', 'totalAmount', 'totalPaid'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'branch', 'creator', 'payments.receiver', 'returns.items.product']);

        return view('admin.sales.show', compact('sale'));
    }

    public function returnSale(Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return back()->with('error', 'Only completed sales can be returned.');
        }

        return view('admin.sales.return', compact('sale'));
    }

    public function storeReturn(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $sale) {
            $return = SaleReturn::create([
                'sale_id' => $sale->id,
                'status' => 'completed',
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'processed_by' => auth()->id(),
            ]);

            $totalReturn = 0;

            foreach ($validated['items'] as $item) {
                $saleItem = SaleItem::find($item['sale_item_id']);

                if ($item['quantity'] > $saleItem->quantity) {
                    throw new \Exception("Return quantity cannot exceed sold quantity for {$saleItem->product_name}.");
                }

                $subtotal = $saleItem->unit_price * $item['quantity'];

                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'product_variant_id' => $saleItem->product_variant_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $saleItem->unit_price,
                    'subtotal' => $subtotal,
                ]);

                // Restore stock
                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $sale->branch_id,
                        'product_id' => $saleItem->product_id,
                        'product_variant_id' => $saleItem->product_variant_id,
                    ],
                    ['quantity' => 0]
                );

                $qtyBefore = $stock->quantity;
                $stock->increment('quantity', $item['quantity']);
                $stock->refresh();

                StockMovement::create([
                    'branch_id' => $sale->branch_id,
                    'product_id' => $saleItem->product_id,
                    'product_variant_id' => $saleItem->product_variant_id,
                    'type' => 'return',
                    'quantity' => $item['quantity'],
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $stock->quantity,
                    'reference_type' => SaleReturn::class,
                    'reference_id' => $return->id,
                    'notes' => "Return from sale {$sale->invoice_number}",
                    'user_id' => auth()->id(),
                ]);

                $totalReturn += $subtotal;
            }

            $return->update(['total_amount' => $totalReturn]);

            // Update sale status
            $totalReturned = $sale->returns->where('status', 'completed')->sum('total_amount') + $totalReturn;
            if ($totalReturned >= $sale->total) {
                $sale->update(['status' => 'returned']);
            }
        });

        return redirect()->route('admin.sales.show', $sale->id)->with('success', 'Sale return processed successfully. Stock restored.');
    }

    public function customerLedger(Customer $customer)
    {
        $payments = CustomerPayment::where('customer_id', $customer->id)
            ->with(['sale', 'creator'])
            ->latest()
            ->paginate(30);

        $totalSales = Sale::where('customer_id', $customer->id)->where('status', 'completed')->sum('total');
        $totalPaid = CustomerPayment::where('customer_id', $customer->id)->where('type', 'payment')->sum('amount');
        $totalRefunded = CustomerPayment::where('customer_id', $customer->id)->where('type', 'refund')->sum('amount');
        $balance = $totalSales - $totalPaid - $totalRefunded;

        return view('admin.sales.customer-ledger', compact('customer', 'payments', 'totalSales', 'totalPaid', 'totalRefunded', 'balance'));
    }

    public function recordCustomerPayment(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,transfer,e_wallet,cheque',
            'sale_id' => 'nullable|exists:sales,id',
            'notes' => 'nullable|string',
        ]);

        CustomerPayment::create([
            'customer_id' => $customer->id,
            'sale_id' => $validated['sale_id'] ?? null,
            'type' => 'payment',
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }
}
