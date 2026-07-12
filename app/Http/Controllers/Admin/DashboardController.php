<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\RepairJob;
use App\Models\Stock;
use App\Models\Brand;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', 'today');

        if ($range === 'yesterday') {
            $dateFrom = now()->subDay()->startOfDay();
            $dateTo = now()->subDay()->endOfDay();
        } elseif ($range === 'week') {
            $dateFrom = now()->startOfWeek();
            $dateTo = now()->endOfDay();
        } elseif ($range === 'month') {
            $dateFrom = now()->startOfMonth();
            $dateTo = now()->endOfDay();
        } elseif ($range === 'year') {
            $dateFrom = now()->startOfYear();
            $dateTo = now()->endOfDay();
        } else {
            $dateFrom = now()->startOfDay();
            $dateTo = now()->endOfDay();
        }

        $salesQuery = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        $periodRevenue = (clone $salesQuery)->sum('total');
        $periodExpenses = Expense::whereBetween('created_at', [$dateFrom, $dateTo])->sum('amount');

        $costQuery = SaleItem::select('sale_items.product_id', 'sale_items.quantity', 'products.purchase_price')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo]);

        $periodCOGS = (clone $costQuery)->sum(DB::raw('sale_items.quantity * products.purchase_price'));
        $periodProfit = $periodRevenue - $periodCOGS - $periodExpenses;

        $todaySales = Sale::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total');

        $monthRevenue = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        $totalProducts = Product::count();

        $lowStockCount = Stock::where('quantity', '<=', 5)
            ->where('quantity', '>', 0)
            ->count();

        $pendingRepairs = RepairJob::whereIn('status', ['received', 'diagnosed', 'in_progress', 'waiting_parts'])
            ->count();

        $recentSales = Sale::with(['customer', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $topProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $recentActivity = Activity::with('causer')
            ->latest()
            ->limit(10)
            ->get();

        $chartLabels = [];
        $chartRevenue = [];
        $chartExpenses = [];
        $chartProfit = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->format('M Y');

            $mRevenue = (float) Sale::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');
            $chartRevenue[] = $mRevenue;

            $mCOGS = (float) SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.status', 'completed')
                ->whereYear('sales.created_at', $month->year)
                ->whereMonth('sales.created_at', $month->month)
                ->sum(DB::raw('sale_items.quantity * products.purchase_price'));
            $mExpenses = (float) Expense::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $chartExpenses[] = $mExpenses;
            $chartProfit[] = $mRevenue - $mCOGS - $mExpenses;
        }

        return view('dashboard', compact(
            'todaySales', 'monthRevenue', 'totalProducts', 'lowStockCount',
            'pendingRepairs', 'recentSales', 'topProducts', 'recentActivity',
            'periodRevenue', 'periodCOGS', 'periodExpenses', 'periodProfit', 'range',
            'chartLabels', 'chartRevenue', 'chartExpenses', 'chartProfit'
        ));
    }
}
