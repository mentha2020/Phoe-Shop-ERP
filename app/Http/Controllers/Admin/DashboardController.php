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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
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

        return view('dashboard', compact(
            'todaySales', 'monthRevenue', 'totalProducts', 'lowStockCount',
            'pendingRepairs', 'recentSales', 'topProducts', 'recentActivity'
        ));
    }
}
