@extends('layouts.app')

@section('title', 'User Performance Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">User Performance Report</h1>
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

    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card"><div class="card-header"><h5 class="mb-0">Sales Performance by User</h5></div><div class="card-body">
                @if($salesByUser->count())
                <div class="table-responsive"><table class="table"><thead><tr><th>#</th><th>User</th><th class="text-center">Sales</th><th class="text-end">Revenue</th><th class="text-end">Avg Sale</th><th class="text-end">% of Total</th></tr></thead>
                <tbody>@foreach($salesByUser as $i => $u)<tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $u->name }}</td>
                    <td class="text-center">{{ number_format($u->total_sales) }}</td>
                    <td class="text-end fw-bold">Rs. {{ number_format($u->total_revenue, 2) }}</td>
                    <td class="text-end">Rs. {{ number_format($u->avg_sale, 2) }}</td>
                    <td class="text-end">{{ $totalRevenue > 0 ? number_format(($u->total_revenue / $totalRevenue) * 100, 1) : 0 }}%</td>
                </tr>@endforeach</tbody>
                <tfoot><tr class="table-light"><td colspan="2" class="fw-bold">Total</td><td class="text-center fw-bold">{{ number_format($salesByUser->sum('total_sales')) }}</td><td class="text-end fw-bold">Rs. {{ number_format($totalRevenue, 2) }}</td><td class="text-end fw-bold">Rs. {{ number_format($salesByUser->sum('total_sales') > 0 ? $totalRevenue / $salesByUser->sum('total_sales') : 0, 2) }}</td><td class="text-end fw-bold">100%</td></tr></tfoot></table></div>
                @else <p class="text-muted text-center py-4">No sales data.</p> @endif
            </div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card"><div class="card-header"><h5 class="mb-0">Daily Revenue by User</h5></div><div class="card-body">
                @if($dailyByUser->count())
                <div class="table-responsive"><table class="table"><thead><tr><th>Date</th>@foreach($salesByUser as $u)<th class="text-end">{{ $u->name }}</th>@endforeach</tr></thead>
                <tbody>@foreach($dailyByUser as $d)<tr><td>{{ $d['date'] }}</td>@foreach($salesByUser as $u)<td class="text-end">Rs. {{ number_format($d['users'][$u->name] ?? 0, 2) }}</td>@endforeach</tr>@endforeach</tbody></table></div>
                @else <p class="text-muted text-center py-4">No data.</p> @endif
            </div></div>
        </div>
        <div class="col-md-5">
            <div class="card"><div class="card-header"><h5 class="mb-0">Repair Performance</h5></div><div class="card-body">
                @if($repairsByUser->count())
                <table class="table"><thead><tr><th>User</th><th class="text-center">Repairs</th><th class="text-end">Revenue</th></tr></thead>
                <tbody>@foreach($repairsByUser as $u)<tr><td class="fw-semibold">{{ $u->name }}</td><td class="text-center">{{ $u->total_repairs }}</td><td class="text-end fw-bold">Rs. {{ number_format($u->total_revenue, 2) }}</td></tr>@endforeach</tbody></table>
                @else <p class="text-muted text-center py-4">No repair data.</p> @endif
            </div></div>
        </div>
    </div>
</div>
<style>@media print { .btn, .sidebar, .navbar { display: none !important; } }</style>
@endsection
