@extends('layouts.app')

@section('title', 'Stock Movement Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Stock Movement Report</h1>
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total Movements</h6><h3 class="text-primary">{{ number_format($totalMovements) }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total In</h6><h3 class="text-success">+{{ number_format($totalIn) }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total Out</h6><h3 class="text-danger">-{{ number_format($totalOut) }}</h3></div></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Movement Type</h5></div><div class="card-body">
                @if($movementsByType->count())
                <table class="table"><thead><tr><th>Type</th><th class="text-center">Count</th><th class="text-center">In</th><th class="text-center">Out</th></tr></thead>
                <tbody>@foreach($movementsByType as $m)<tr><td><span class="badge bg-secondary">{{ $m['type'] }}</span></td><td class="text-center">{{ $m['count'] }}</td><td class="text-center text-success">+{{ $m['in'] }}</td><td class="text-center text-danger">-{{ $m['out'] }}</td></tr>@endforeach</tbody></table>
                @else <p class="text-muted text-center py-4">No data.</p> @endif
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-header"><h5 class="mb-0">Transfers</h5></div><div class="card-body">
                @if($transfers->count())
                <div class="table-responsive"><table class="table"><thead><tr><th>Ref</th><th>From</th><th>To</th><th>Status</th><th class="text-center">Items</th></tr></thead>
                <tbody>@foreach($transfers as $t)<tr><td>{{ $t->reference_number }}</td><td>{{ $t->fromBranch->name }}</td><td>{{ $t->toBranch->name }}</td><td><span class="badge bg-{{ $t->status === 'received' ? 'success' : ($t->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($t->status) }}</span></td><td class="text-center">{{ $t->items->count() }}</td></tr>@endforeach</tbody></table></div>
                @else <p class="text-muted text-center py-4">No transfers.</p> @endif
            </div></div>
        </div>
    </div>

    <div class="card mb-4"><div class="card-header"><h5 class="mb-0">Adjustments</h5></div><div class="card-body">
        @if($adjustments->count())
        <div class="table-responsive"><table class="table"><thead><tr><th>Ref</th><th>Branch</th><th>Type</th><th>Date</th><th>Items</th></tr></thead>
        <tbody>@foreach($adjustments as $a)<tr><td>{{ $a->reference_number }}</td><td>{{ $a->branch->name }}</td><td><span class="badge bg-secondary">{{ ucfirst($a->type) }}</span></td><td>{{ $a->created_at->format('d M Y') }}</td><td>{{ $a->items->count() }}</td></tr>@endforeach</tbody></table></div>
        @else <p class="text-muted text-center py-4">No adjustments.</p> @endif
    </div></div>

    <div class="card"><div class="card-header"><h5 class="mb-0">Recent Movements</h5></div><div class="card-body">
        @if($movements->count())
        <div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Product</th><th>Branch</th><th>Type</th><th class="text-center">Qty</th><th>Before</th><th>After</th></tr></thead>
        <tbody>@foreach($movements->take(20) as $m)<tr>
            <td>{{ $m->created_at->format('d M H:i') }}</td>
            <td class="fw-semibold">{{ $m->product->name ?? 'N/A' }}</td>
            <td>{{ $m->branch->name }}</td>
            <td><span class="badge bg-secondary">{{ ucfirst($m->type) }}</span></td>
            <td class="text-center {{ $m->quantity > 0 ? 'text-success' : 'text-danger' }}">{{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}</td>
            <td>{{ $m->quantity_before }}</td>
            <td>{{ $m->quantity_after }}</td>
        </tr>@endforeach</tbody></table></div>
        @else <p class="text-muted text-center py-4">No movements.</p> @endif
    </div></div>
</div>
<style>@media print { .btn, .sidebar, .navbar { display: none !important; } }</style>
@endsection
