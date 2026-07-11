@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
    </div>
    <div class="d-flex gap-2">
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
                        <div class="card-stat-value">${{ number_format($todaySales, 2) }}</div>
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
                        <div class="card-stat-value">${{ number_format($monthRevenue, 2) }}</div>
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
        <div class="card h-100" style="border-left: 4px solid #10b981;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-graph-up-arrow" style="font-size: 1.5rem; color: #10b981;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="card-stat-value" style="color: #10b981;">${{ number_format($periodRevenue, 2) }}</div>
                        <div class="card-stat-label">Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100" style="border-left: 4px solid #f59e0b;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-receipt" style="font-size: 1.5rem; color: #f59e0b;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="card-stat-value" style="color: #f59e0b;">${{ number_format($periodCOGS, 2) }}</div>
                        <div class="card-stat-label">Cost of Goods</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100" style="border-left: 4px solid #ef4444;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-cash-stack" style="font-size: 1.5rem; color: #ef4444;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="card-stat-value" style="color: #ef4444;">${{ number_format($periodExpenses, 2) }}</div>
                        <div class="card-stat-label">Expenses</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100" style="border-left: 4px solid {{ $periodProfit >= 0 ? '#4f46e5' : '#ef4444' }};">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $periodProfit >= 0 ? 'rgba(79, 70, 229, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; display: flex; align-items: center; justify-content: center;">
                            <i class="bi {{ $periodProfit >= 0 ? 'bi-badge-dollar' : 'bi-graph-down-arrow' }}" style="font-size: 1.5rem; color: {{ $periodProfit >= 0 ? '#4f46e5' : '#ef4444' }};"></i>
                        </div>
                    </div>
                    <div>
                        <div class="card-stat-value" style="color: {{ $periodProfit >= 0 ? '#4f46e5' : '#ef4444' }};">${{ number_format($periodProfit, 2) }}</div>
                        <div class="card-stat-label">Profit / Loss</div>
                    </div>
                </div>
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
                                    <td>${{ number_format($sale->total, 2) }}</td>
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
                                    <td>${{ number_format($item->total_revenue, 2) }}</td>
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
});
</script>
@endpush
