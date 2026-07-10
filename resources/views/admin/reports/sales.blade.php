@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sales Report</h1>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i>Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Sales</h6>
                    <h3 class="text-primary">{{ number_format($totalSales) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3 class="text-success">${{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Avg Sale Value</h6>
                    <h3>${{ number_format($avgSaleValue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Collected</h6>
                    <h3 class="text-info">${{ number_format($totalPaid, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Daily Sales Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Daily Sales</h5></div>
                <div class="card-body">
                    @if($dailySales->count())
                    <div style="height: 300px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead><tr><th>Date</th><th class="text-center">Sales</th><th class="text-end">Revenue</th></tr></thead>
                            <tbody>
                                @foreach($dailySales as $day)
                                <tr>
                                    <td>{{ $day['date'] }}</td>
                                    <td class="text-center">{{ $day['count'] }}</td>
                                    <td class="text-end fw-bold">${{ number_format($day['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-4">No data for selected period.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">By Payment Method</h5></div>
                <div class="card-body">
                    @foreach($paymentMethods as $pm)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                        <div>
                            <div class="fw-semibold">{{ $pm['method'] }}</div>
                            <small class="text-muted">{{ $pm['count'] }} sales</small>
                        </div>
                        <div class="text-end fw-bold">${{ number_format($pm['total'], 2) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="card mt-4">
        <div class="card-header"><h5 class="mb-0">Top Products</h5></div>
        <div class="card-body">
            @if($topProducts->count())
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>#</th><th>Product</th><th class="text-center">Qty Sold</th><th class="text-end">Revenue</th></tr></thead>
                    <tbody>
                        @foreach($topProducts as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $product->product_name }}</td>
                            <td class="text-center">{{ $product->total_qty }}</td>
                            <td class="text-end fw-bold">${{ number_format($product->total_revenue, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted text-center py-4">No product data.</p>
            @endif
        </div>
    </div>
</div>

<style>
@media print { .btn, .sidebar, .navbar { display: none !important; } }
</style>
@endsection
