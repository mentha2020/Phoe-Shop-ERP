@extends('layouts.app')

@section('title', 'Expense Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expense Report</h1>
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
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white"><div class="card-body"><h6>Total Approved</h6><h2>Rs. {{ number_format($approvedExpenses, 2) }}</h2></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning"><div class="card-body"><h6>Pending</h6><h2>Rs. {{ number_format($pendingExpenses, 2) }}</h2></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white"><div class="card-body"><h6>Rejected</h6><h2>Rs. {{ number_format($rejectedExpenses, 2) }}</h2></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white"><div class="card-body"><h6>All Expenses</h6><h2>Rs. {{ number_format($totalExpenses, 2) }}</h2></div></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Category</h5></div><div class="card-body">
                @foreach($expenseByCategory as $cat)
                <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                    <div><div class="fw-semibold">{{ $cat['name'] }}</div><small class="text-muted">{{ $cat['count'] }} expenses</small></div>
                    <div class="text-end fw-bold">Rs. {{ number_format($cat['total'], 2) }}</div>
                </div>
                @endforeach
            </div></div>
        </div>
        <div class="col-md-5">
            <div class="card"><div class="card-header"><h5 class="mb-0">By Payment Method</h5></div><div class="card-body">
                @foreach($expenseByPayment as $pm)
                <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                    <div><div class="fw-semibold">{{ $pm['method'] }}</div><small class="text-muted">{{ $pm['count'] }} expenses</small></div>
                    <div class="text-end fw-bold">Rs. {{ number_format($pm['total'], 2) }}</div>
                </div>
                @endforeach
            </div></div>
        </div>
    </div>

    <div class="card mt-4"><div class="card-header"><h5 class="mb-0">Recent Expenses</h5></div><div class="card-body">
        @if($recentExpenses->count())
        <div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Status</th><th class="text-end">Amount</th></tr></thead>
        <tbody>@foreach($recentExpenses as $e)<tr>
            <td>{{ $e->expense_date->format('d M Y') }}</td>
            <td class="fw-semibold">{{ $e->title }}</td>
            <td>{{ $e->category?->name ?? 'N/A' }}</td>
            <td><span class="badge bg-{{ $e->status === 'approved' ? 'success' : ($e->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($e->status) }}</span></td>
            <td class="text-end fw-bold">Rs. {{ number_format($e->amount, 2) }}</td>
        </tr>@endforeach</tbody></table></div>
        @else <p class="text-muted text-center py-4">No expenses.</p> @endif
    </div></div>
</div>
<style>@media print { .btn, .sidebar, .navbar { display: none !important; } }</style>
@endsection
