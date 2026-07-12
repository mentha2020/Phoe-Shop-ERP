@extends('layouts.app')

@section('title', 'Customer Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Customer Report</h1>
        <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total Customers</h6><h3 class="text-primary">{{ number_format($totalCustomers) }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">With Balance</h6><h3 class="text-warning">{{ number_format($activeCustomers) }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total Receivable</h6><h3 class="text-danger">Rs. {{ number_format($totalReceivable, 2) }}</h3></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card"><div class="card-header"><h5 class="mb-0">Top Customers</h5></div><div class="card-body">
                @if($topCustomers->count())
                <div class="table-responsive"><table class="table"><thead><tr><th>#</th><th>Customer</th><th class="text-center">Orders</th><th class="text-end">Total Spent</th></tr></thead>
                <tbody>@foreach($topCustomers as $i => $c)<tr><td>{{ $i + 1 }}</td><td class="fw-semibold">{{ $c->name }}</td><td class="text-center">{{ $c->total_orders }}</td><td class="text-end fw-bold">Rs. {{ number_format($c->total_spent, 2) }}</td></tr>@endforeach</tbody></table></div>
                @else <p class="text-muted text-center py-4">No data.</p> @endif
            </div></div>
        </div>
        <div class="col-md-5">
            <div class="card"><div class="card-header"><h5 class="mb-0">Outstanding Balances</h5></div><div class="card-body">
                @foreach($customerBalances as $c)
                <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                    <div><div class="fw-semibold">{{ $c->name }}</div><small class="text-muted">Limit: Rs. {{ number_format($c->credit_limit, 2) }}</small></div>
                    <div class="text-end fw-bold text-danger">Rs. {{ number_format($c->current_balance, 2) }}</div>
                </div>
                @endforeach
            </div></div>
        </div>
    </div>

    <div class="card mt-4"><div class="card-header"><h5 class="mb-0">Recent Payments</h5></div><div class="card-body">
        @if($recentPayments->count())
        <div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Customer</th><th>Method</th><th class="text-end">Amount</th></tr></thead>
        <tbody>@foreach($recentPayments as $p)<tr><td>{{ $p->created_at->format('d M Y') }}</td><td>{{ $p->customer?->name ?? 'N/A' }}</td><td>{{ ucfirst($p->method) }}</td><td class="text-end fw-bold">Rs. {{ number_format($p->amount, 2) }}</td></tr>@endforeach</tbody></table></div>
        @else <p class="text-muted text-center py-4">No payments.</p> @endif
    </div></div>
</div>
<style>@media print { .btn, .sidebar, .navbar { display: none !important; } }</style>
@endsection
