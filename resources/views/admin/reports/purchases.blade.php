@extends('layouts.app')

@section('title', 'Purchase Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Purchase Report</h1>
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>

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

    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total Orders</h6><h3 class="text-primary">{{ number_format($totalOrders) }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total Amount</h6><h3 class="text-success">Rs. {{ number_format($totalAmount, 2) }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Paid</h6><h3 class="text-info">Rs. {{ number_format($totalPaid, 2) }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Pending</h6><h3 class="text-warning">Rs. {{ number_format($pendingAmount, 2) }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Received</h6><h3>{{ $receivedOrders }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Avg Order</h6><h3>Rs. {{ number_format($totalOrders > 0 ? $totalAmount / $totalOrders : 0, 2) }}</h3></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Supplier</h5></div><div class="card-body">
                @foreach($purchasesBySupplier as $s)
                <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                    <div><div class="fw-semibold">{{ $s['name'] }}</div><small class="text-muted">{{ $s['count'] }} orders</small></div>
                    <div class="text-end fw-bold">Rs. {{ number_format($s['total'], 2) }}</div>
                </div>
                @endforeach
            </div></div>
        </div>
        <div class="col-md-7">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Status</h5></div><div class="card-body">
                <table class="table"><thead><tr><th>Status</th><th class="text-center">Count</th><th class="text-end">Amount</th></tr></thead>
                <tbody>@foreach($statusBreakdown as $s)<tr><td><span class="badge bg-secondary">{{ $s['status'] }}</span></td><td class="text-center">{{ $s['count'] }}</td><td class="text-end fw-bold">Rs. {{ number_format($s['total'], 2) }}</td></tr>@endforeach</tbody></table>
            </div></div>
        </div>
    </div>

    <div class="card mt-4"><div class="card-header"><h5 class="mb-0">Daily Purchases</h5></div><div class="card-body">
        @if($dailyPurchases->count())
        <div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th class="text-center">Orders</th><th class="text-end">Amount</th></tr></thead>
        <tbody>@foreach($dailyPurchases as $d)<tr><td>{{ $d['date'] }}</td><td class="text-center">{{ $d['count'] }}</td><td class="text-end fw-bold">Rs. {{ number_format($d['total'], 2) }}</td></tr>@endforeach</tbody></table></div>
        @else <p class="text-muted text-center py-4">No data for selected period.</p> @endif
    </div></div>
</div>
<style>@media print { .btn, .sidebar, .navbar { display: none !important; } }</style>
@endsection
