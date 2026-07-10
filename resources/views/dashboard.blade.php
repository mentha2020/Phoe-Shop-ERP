@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
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
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales',
                data: [1200, 1900, 3000, 500, 2000, 3000, 4500],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Brands Chart
    const brandsCtx = document.getElementById('brandsChart').getContext('2d');
    new Chart(brandsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Apple', 'Samsung', 'Xiaomi', 'OnePlus', 'Others'],
            datasets: [{
                data: [35, 25, 20, 10, 10],
                backgroundColor: ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#94a3b8']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 15 }
                }
            }
        }
    });
});
</script>
@endpush
