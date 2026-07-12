@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        @php
            $ranges = [
                'today' => 'Today',
                'yesterday' => 'Yesterday',
                'week' => 'This Week',
                'month' => 'This Month',
                'year' => 'This Year',
            ];
        @endphp
        @foreach($ranges as $key => $label)
            <a href="{{ route('admin.dashboard', ['range' => $key]) }}"
               class="btn btn-sm {{ ($range ?? 'today') === $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- Stats Cards --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-primary me-3">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($todaySales, 2) }}</div>
                        <div class="card-stat-label">Sales Today</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-success me-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($monthRevenue, 2) }}</div>
                        <div class="card-stat-label">Revenue This Month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-warning me-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">{{ $totalProducts }}</div>
                        <div class="card-stat-label">Total Products</div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small"><i class="bi bi-exclamation-circle text-warning"></i> {{ $lowStockCount }} low stock</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-danger me-3">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">{{ $pendingRepairs }}</div>
                        <div class="card-stat-label">Pending Repairs</div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small"><i class="bi bi-clock"></i> Awaiting pickup</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Profit/Loss Cards --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-success me-3">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($periodRevenue, 2) }}</div>
                        <div class="card-stat-label">Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-warning me-3">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($periodCOGS, 2) }}</div>
                        <div class="card-stat-label">Cost of Goods</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-danger me-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($periodExpenses, 2) }}</div>
                        <div class="card-stat-label">Expenses</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-info me-3">
                        <i class="bi bi-badge-dollar"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($periodProfit, 2) }}</div>
                        <div class="card-stat-label">Profit / Loss</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Due/Outstanding Cards --}}
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-4">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-primary me-3">
                        <i class="bi bi-person-exclamation"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($salesDue->total_due ?? 0, 2) }}</div>
                        <div class="card-stat-label">Sales Due from Customers</div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small"><i class="bi bi-file-earmark-text"></i> {{ $salesDue->count ?? 0 }} unpaid invoices</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-danger me-3">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($purchaseDue->total_due ?? 0, 2) }}</div>
                        <div class="card-stat-label">Purchase Due to Suppliers</div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small"><i class="bi bi-bag-check"></i> {{ $purchaseDue->count ?? 0 }} pending POs</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card card-stat h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="card-stat-icon gradient-warning me-3">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <div class="card-stat-value">Rs. {{ number_format($repairDue->total_due ?? 0, 2) }}</div>
                        <div class="card-stat-label">Repair Due from Customers</div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted small"><i class="bi bi-clock"></i> {{ $repairDue->count ?? 0 }} unpaid repairs</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Revenue vs Expenses Chart --}}
<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0">
                <h6 class="card-title fw-bold mb-0">Revenue vs Expenses (Last 12 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueExpensesChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0">
                <h6 class="card-title fw-bold mb-0">Profit vs Expenses (Last 12 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="profitExpensesChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activity & Top Products --}}
<div class="row g-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0">Recent Sales</h6>
                <a href="{{ route('admin.sales.index') }}" class="text-primary text-decoration-none small">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td><a href="{{ route('admin.sales.show', $sale->id) }}" class="text-decoration-none">{{ $sale->invoice_number }}</a></td>
                                    <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                                    <td>Rs. {{ number_format($sale->total, 2) }}</td>
                                    <td><span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($sale->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No sales yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0">Top Products</h6>
                <a href="{{ route('admin.products.index') }}" class="text-primary text-decoration-none small">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $item)
                                <tr>
                                    <td>{{ $item->product?->name ?? 'N/A' }}</td>
                                    <td>{{ $item->total_sold }}</td>
                                    <td>Rs. {{ number_format($item->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">No products sold yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = {!! json_encode($chartLabels) !!};
    const revenue = {!! json_encode($chartRevenue) !!};
    const expenses = {!! json_encode($chartExpenses) !!};
    const profit = {!! json_encode($chartProfit) !!};

    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    const sharedOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: { legend: { labels: { color: textColor, usePointStyle: true, padding: 16 } } },
        scales: {
            x: { ticks: { color: textColor, maxRotation: 45 }, grid: { color: gridColor } },
            y: { ticks: { color: textColor, callback: v => 'Rs. ' + v.toLocaleString() }, grid: { color: gridColor } }
        }
    };

    new Chart(document.getElementById('revenueExpensesChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'Revenue', data: revenue, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4, pointRadius: 3 },
                { label: 'Expenses', data: expenses, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', fill: true, tension: 0.4, pointRadius: 3 }
            ]
        },
        options: sharedOptions
    });

    new Chart(document.getElementById('profitExpensesChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'Profit', data: profit, borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,0.1)', fill: true, tension: 0.4, pointRadius: 3 },
                { label: 'Expenses', data: expenses, borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)', fill: true, tension: 0.4, pointRadius: 3 }
            ]
        },
        options: sharedOptions
    });
});
</script>
@endpush
