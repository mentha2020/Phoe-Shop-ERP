@extends('layouts.app')

@section('title', 'Repair Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Repair Report</h1>
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
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Total</h6><h3 class="text-primary">{{ number_format($totalRepairs) }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Completed</h6><h3 class="text-success">{{ $completedRepairs }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Delivered</h6><h3 class="text-info">{{ $deliveredRepairs }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Pending</h6><h3 class="text-warning">{{ $pendingRepairs }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Revenue</h6><h3 class="text-success">Rs. {{ number_format($totalRevenue, 2) }}</h3></div></div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm"><div class="card-body text-center"><h6 class="text-muted">Deposits</h6><h3 class="text-info">Rs. {{ number_format($totalDeposits, 2) }}</h3></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Status</h5></div><div class="card-body">
                @foreach($statusBreakdown as $s)
                <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                    <span class="badge bg-secondary">{{ $s['status'] }}</span>
                    <span class="fw-bold">{{ $s['count'] }}</span>
                </div>
                @endforeach
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Device Type</h5></div><div class="card-body">
                @foreach($repairsByDevice as $d)
                <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                    <div><div class="fw-semibold">{{ $d['type'] }}</div><small class="text-muted">Rs. {{ number_format($d['revenue'], 2) }} revenue</small></div>
                    <span class="fw-bold">{{ $d['count'] }}</span>
                </div>
                @endforeach
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Technician</h5></div><div class="card-body">
                @foreach($topTechnicians as $t)
                <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                    <div><div class="fw-semibold">{{ $t['name'] }}</div><small class="text-muted">Rs. {{ number_format($t['revenue'], 2) }} revenue</small></div>
                    <span class="fw-bold">{{ $t['count'] }}</span>
                </div>
                @endforeach
            </div></div>
        </div>
    </div>

    <div class="card mt-4"><div class="card-header"><h5 class="mb-0">Daily Repairs</h5></div><div class="card-body">
        @if($dailyRepairs->count())
        <div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th class="text-center">Repairs</th><th class="text-end">Revenue</th></tr></thead>
        <tbody>@foreach($dailyRepairs as $d)<tr><td>{{ $d['date'] }}</td><td class="text-center">{{ $d['count'] }}</td><td class="text-end fw-bold">Rs. {{ number_format($d['revenue'], 2) }}</td></tr>@endforeach</tbody></table></div>
        @else <p class="text-muted text-center py-4">No data.</p> @endif
    </div></div>
</div>
<style>@media print { .btn, .sidebar, .navbar { display: none !important; } }</style>
@endsection
