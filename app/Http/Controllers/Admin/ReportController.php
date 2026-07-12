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
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\RepairJob;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;
use App\Models\User;
use App\Models\SupplierPayment;
use App\Models\CustomerPayment;
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
            ->whereBetween('received_date', [$dateFrom, $dateTo])
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

    public function purchaseReport(Request $request)
    {
        $branchId = $request->get('branch_id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $query = PurchaseOrder::with(['supplier', 'branch'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $orders = $query->get();

        $totalOrders = $orders->count();
        $totalAmount = $orders->sum('total_amount');
        $totalPaid = $orders->sum('paid_amount');
        $pendingAmount = $totalAmount - $totalPaid;
        $receivedOrders = $orders->where('status', 'received')->count();

        $statusBreakdown = $orders->groupBy('status')->map(fn ($group) => [
            'status' => ucfirst($group->first()->status),
            'count' => $group->count(),
            'total' => $group->sum('total_amount'),
        ])->values();

        $purchasesBySupplier = $orders->groupBy(fn ($o) => $o->supplier->name ?? 'Unknown')
            ->map(fn ($group) => [
                'name' => $group->first()->supplier->name ?? 'Unknown',
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ])
            ->sortByDesc('total')
            ->values();

        $dailyPurchases = $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))
            ->map(fn ($group) => [
                'date' => $group->first()->created_at->format('d M'),
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ])
            ->values();

        $branches = Branch::active()->get();

        return view('admin.reports.purchases', compact(
            'totalOrders', 'totalAmount', 'totalPaid', 'pendingAmount', 'receivedOrders',
            'statusBreakdown', 'purchasesBySupplier', 'dailyPurchases', 'branches',
            'dateFrom', 'dateTo', 'branchId'
        ));
    }

    public function customerReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('current_balance', '>', 0)->count();
        $totalReceivable = Customer::sum('current_balance');

        $topCustomers = Sale::where('status', 'completed')
            ->whereNotNull('customer_id')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(sales.total) as total_spent'))
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(15)
            ->get();

        $customerBalances = Customer::where('current_balance', '>', 0)
            ->orderByDesc('current_balance')
            ->limit(15)
            ->get(['id', 'name', 'current_balance', 'credit_limit']);

        $newCustomers = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->select(DB::raw("DATE(created_at) as date"), DB::raw('COUNT(DISTINCT customer_id) as count'))
            ->whereNotNull('customer_id')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $recentPayments = CustomerPayment::with('customer')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.reports.customers', compact(
            'totalCustomers', 'activeCustomers', 'totalReceivable',
            'topCustomers', 'customerBalances', 'newCustomers', 'recentPayments',
            'dateFrom', 'dateTo'
        ));
    }

    public function expenseReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $query = Expense::with('category')
            ->whereBetween('expense_date', [$dateFrom, $dateTo]);

        $expenses = $query->get();

        $totalExpenses = $expenses->sum('amount');
        $approvedExpenses = $expenses->where('status', 'approved')->sum('amount');
        $pendingExpenses = $expenses->where('status', 'pending')->sum('amount');
        $rejectedExpenses = $expenses->where('status', 'rejected')->sum('amount');

        $expenseByCategory = $expenses->where('status', 'approved')
            ->groupBy(fn ($e) => $e->category->name ?? 'Uncategorized')
            ->map(fn ($group) => [
                'name' => $group->first()->category->name ?? 'Uncategorized',
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ])
            ->sortByDesc('total')
            ->values();

        $dailyExpenses = $expenses->where('status', 'approved')
            ->groupBy(fn ($e) => $e->expense_date->format('Y-m-d'))
            ->map(fn ($group) => [
                'date' => $group->first()->expense_date->format('d M'),
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ])
            ->values();

        $expenseByPayment = $expenses->where('status', 'approved')
            ->groupBy('payment_method')
            ->map(fn ($group) => [
                'method' => ucfirst(str_replace('_', ' ', $group->first()->payment_method)),
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ])
            ->values();

        $recentExpenses = $expenses->sortByDesc('created_at')->take(15)->values();

        return view('admin.reports.expenses', compact(
            'totalExpenses', 'approvedExpenses', 'pendingExpenses', 'rejectedExpenses',
            'expenseByCategory', 'dailyExpenses', 'expenseByPayment', 'recentExpenses',
            'dateFrom', 'dateTo'
        ));
    }

    public function repairReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $repairs = RepairJob::with(['customer', 'branch', 'assignedTo'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->get();

        $totalRepairs = $repairs->count();
        $completedRepairs = $repairs->where('status', 'completed')->count();
        $deliveredRepairs = $repairs->where('status', 'delivered')->count();
        $pendingRepairs = $repairs->whereIn('status', ['received', 'diagnosed', 'in_progress', 'waiting_parts'])->count();
        $totalRevenue = $repairs->where('status', 'completed')->sum('final_cost');
        $totalDeposits = $repairs->sum('deposit_amount');

        $statusBreakdown = $repairs->groupBy('status')->map(fn ($group) => [
            'status' => ucfirst(str_replace('_', ' ', $group->first()->status)),
            'count' => $group->count(),
        ])->values();

        $repairsByDevice = $repairs->groupBy('device_type')
            ->map(fn ($group) => [
                'type' => $group->first()->device_type ?? 'Unknown',
                'count' => $group->count(),
                'revenue' => $group->sum('final_cost'),
            ])
            ->sortByDesc('count')
            ->values();

        $dailyRepairs = $repairs->groupBy(fn ($r) => $r->created_at->format('Y-m-d'))
            ->map(fn ($group) => [
                'date' => $group->first()->created_at->format('d M'),
                'count' => $group->count(),
                'revenue' => $group->sum('final_cost'),
            ])
            ->values();

        $topTechnicians = $repairs->where('status', 'completed')
            ->groupBy(fn ($r) => $r->assignedTo?->name ?? 'Unassigned')
            ->map(fn ($group) => [
                'name' => $group->first()->assignedTo?->name ?? 'Unassigned',
                'count' => $group->count(),
                'revenue' => $group->sum('final_cost'),
            ])
            ->sortByDesc('count')
            ->values();

        return view('admin.reports.repairs', compact(
            'totalRepairs', 'completedRepairs', 'deliveredRepairs', 'pendingRepairs',
            'totalRevenue', 'totalDeposits', 'statusBreakdown', 'repairsByDevice',
            'dailyRepairs', 'topTechnicians', 'dateFrom', 'dateTo'
        ));
    }

    public function stockMovementReport(Request $request)
    {
        $branchId = $request->get('branch_id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $movements = StockMovement::with(['product', 'branch'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);

        if ($branchId) {
            $movements->where('branch_id', $branchId);
        }

        $movements = $movements->latest()->get();

        $totalMovements = $movements->count();
        $totalIn = $movements->where('quantity', '>', 0)->sum('quantity');
        $totalOut = abs($movements->where('quantity', '<', 0)->sum('quantity'));

        $movementsByType = $movements->groupBy('type')->map(fn ($group) => [
            'type' => ucfirst(str_replace('_', ' ', $group->first()->type)),
            'count' => $group->count(),
            'in' => $group->where('quantity', '>', 0)->sum('quantity'),
            'out' => abs($group->where('quantity', '<', 0)->sum('quantity')),
        ])->values();

        $transfers = StockTransfer::with(['fromBranch', 'toBranch'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
        if ($branchId) {
            $transfers->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId);
        }
        $transfers = $transfers->latest()->get();

        $adjustments = StockAdjustment::with('branch')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
        if ($branchId) {
            $adjustments->where('branch_id', $branchId);
        }
        $adjustments = $adjustments->latest()->get();

        $branches = Branch::active()->get();

        return view('admin.reports.stock_movements', compact(
            'totalMovements', 'totalIn', 'totalOut', 'movementsByType',
            'movements', 'transfers', 'adjustments', 'branches',
            'dateFrom', 'dateTo', 'branchId'
        ));
    }

    public function supplierReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $totalSuppliers = Supplier::count();
        $suppliersWithBalance = Supplier::where('current_balance', '>', 0)->count();
        $totalPayable = Supplier::sum('current_balance');

        $topSuppliers = PurchaseOrder::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select('suppliers.name', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(purchase_orders.total_amount) as total_spent'))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderByDesc('total_spent')
            ->limit(15)
            ->get();

        $supplierBalances = Supplier::where('current_balance', '>', 0)
            ->orderByDesc('current_balance')
            ->limit(15)
            ->get(['id', 'name', 'company', 'current_balance', 'credit_limit']);

        $recentPayments = SupplierPayment::with('supplier')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->latest()
            ->limit(15)
            ->get();

        $purchasesByMonth = PurchaseOrder::where('status', 'received')
            ->where('received_date', '>=', now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(received_date, '%Y-%m') as month"),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.suppliers', compact(
            'totalSuppliers', 'suppliersWithBalance', 'totalPayable',
            'topSuppliers', 'supplierBalances', 'recentPayments', 'purchasesByMonth',
            'dateFrom', 'dateTo'
        ));
    }

    public function userReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $salesByUser = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->join('users', 'sales.created_by', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as total_sales'), DB::raw('SUM(sales.total) as total_revenue'), DB::raw('AVG(sales.total) as avg_sale'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->get();

        $totalRevenue = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->sum('total');

        $dailyByUser = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->join('users', 'sales.created_by', '=', 'users.id')
            ->select(
                DB::raw("DATE(sales.created_at) as date"),
                'users.name',
                DB::raw('SUM(sales.total) as total')
            )
            ->groupBy('date', 'users.name')
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(fn ($group, $date) => [
                'date' => Carbon::parse($date)->format('d M'),
                'users' => $group->pluck('total', 'name')->toArray(),
            ])
            ->values();

        $repairsByUser = RepairJob::whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->join('users', 'repair_jobs.created_by', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as total_repairs'), DB::raw('SUM(repair_jobs.final_cost) as total_revenue'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_repairs')
            ->get();

        return view('admin.reports.users', compact(
            'salesByUser', 'totalRevenue', 'dailyByUser', 'repairsByUser',
            'dateFrom', 'dateTo'
        ));
    }
}
