<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $branchId = $request->get('branch_id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $query = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $sales = $query->get();

        // Summary
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');
        $totalPaid = $sales->sum('paid_amount');
        $avgSaleValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Daily sales
        $dailySales = $sales->groupBy(fn ($sale) => $sale->created_at->format('Y-m-d'))
            ->map(fn ($group) => [
                'date' => $group->first()->created_at->format('d M'),
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ])
            ->values();

        // Payment method breakdown
        $paymentMethods = $sales->groupBy('payment_method')
            ->map(fn ($group) => [
                'method' => ucfirst($group->first()->payment_method),
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ])
            ->values();

        // Top products
        $topProducts = SaleItem::whereHas('sale', function ($q) use ($dateFrom, $dateTo, $branchId) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            })
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $branches = Branch::active()->get();

        return view('admin.reports.sales', compact(
            'totalSales', 'totalRevenue', 'totalPaid', 'avgSaleValue',
            'dailySales', 'paymentMethods', 'topProducts', 'branches',
            'dateFrom', 'dateTo', 'branchId'
        ));
    }

    public function inventoryReport(Request $request)
    {
        $branchId = $request->get('branch_id');

        $query = Stock::with(['product', 'branch', 'productVariant']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $stocks = $query->get();

        $totalProducts = Product::active()->count();
        $totalStockValue = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;

        $stockByCategory = collect();
        $lowStockProducts = collect();

        foreach ($stocks as $stock) {
            $price = $stock->product->selling_price ?? 0;
            $totalStockValue += $stock->quantity * $price;

            if ($stock->min_stock > 0 && $stock->quantity <= $stock->min_stock) {
                $lowStockCount++;
                $lowStockProducts->push($stock);
            }
            if ($stock->quantity == 0) {
                $outOfStockCount++;
            }
        }

        $stockByCategory = Product::with('category')
            ->active()
            ->get()
            ->groupBy(fn ($p) => $p->category->name ?? 'Uncategorized')
            ->map(fn ($group) => [
                'name' => $group->first()->category->name ?? 'Uncategorized',
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        $branches = Branch::active()->get();

        return view('admin.reports.inventory', compact(
            'totalProducts', 'totalStockValue', 'lowStockCount', 'outOfStockCount',
            'stockByCategory', 'lowStockProducts', 'branches', 'branchId'
        ));
    }

    public function financialReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Revenue
        $totalRevenue = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->sum('total');

        // Cost of Goods (from purchase orders)
        $totalCOGS = PurchaseOrder::where('status', 'received')
            ->whereBetween('received_at', [$dateFrom, $dateTo])
            ->sum('total_amount');

        // Expenses
        $totalExpenses = Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');

        // Gross Profit
        $grossProfit = $totalRevenue - $totalCOGS;

        // Net Profit
        $netProfit = $grossProfit - $totalExpenses;

        // Monthly revenue trend (last 6 months)
        $monthlyRevenue = Sale::where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Expense by category
        $expenseByCategory = Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('expense_categories.name')
            ->get();

        return view('admin.reports.financial', compact(
            'totalRevenue', 'totalCOGS', 'totalExpenses', 'grossProfit', 'netProfit',
            'monthlyRevenue', 'expenseByCategory', 'dateFrom', 'dateTo'
        ));
    }
}
