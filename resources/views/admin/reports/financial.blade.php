@extends('layouts.app')

@section('title', 'Financial Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Financial Report</h1>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>

    <!-- Filters -->
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

    <!-- P&L Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6>Revenue (Sales)</h6>
                    <h2>Rs. {{ number_format($totalRevenue, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <h6>Cost of Goods Sold</h6>
                    <h2>Rs. {{ number_format($totalCOGS, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning">
                <div class="card-body">
                    <h6>Operating Expenses</h6>
                    <h2>Rs. {{ number_format($totalExpenses, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm {{ $netProfit >= 0 ? 'bg-primary' : 'bg-danger' }} text-white">
                <div class="card-body">
                    <h6>Net Profit</h6>
                    <h2>Rs. {{ number_format($netProfit, 2) }}</h2>
                    <small>{{ $netProfit >= 0 ? 'Profitable' : 'Loss' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Breakdown -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Profit Breakdown</h5></div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <td class="fw-bold">Total Revenue</td>
                    <td class="text-end">Rs. {{ number_format($totalRevenue, 2) }}</td>
                </tr>
                <tr>
                    <td>Less: Cost of Goods Sold</td>
                    <td class="text-end text-danger">-Rs. {{ number_format($totalCOGS, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <td class="fw-bold">Gross Profit</td>
                    <td class="text-end fw-bold">Rs. {{ number_format($grossProfit, 2) }}</td>
                </tr>
                <tr>
                    <td>Less: Operating Expenses</td>
                    <td class="text-end text-danger">-Rs. {{ number_format($totalExpenses, 2) }}</td>
                </tr>
                <tr class="{{ $netProfit >= 0 ? 'table-success' : 'table-danger' }}">
                    <td class="fw-bold fs-5">Net Profit</td>
                    <td class="text-end fw-bold fs-5">Rs. {{ number_format($netProfit, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row g-4">
        <!-- Monthly Revenue -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Monthly Revenue (Last 6 Months)</h5></div>
                <div class="card-body">
                    @if($monthlyRevenue->count())
                    <table class="table">
                        <thead><tr><th>Month</th><th class="text-center">Sales</th><th class="text-end">Revenue</th></tr></thead>
                        <tbody>
                            @foreach($monthlyRevenue as $month)
                            <tr>
                                <td>{{ $month->month }}</td>
                                <td class="text-center">{{ $month->count }}</td>
                                <td class="text-end fw-bold">Rs. {{ number_format($month->revenue, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted text-center py-4">No revenue data.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Expense by Category -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Expenses by Category</h5></div>
                <div class="card-body">
                    @if($expenseByCategory->count())
                    @foreach($expenseByCategory as $cat)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                        <span>{{ $cat->name }}</span>
                        <span class="fw-bold">Rs. {{ number_format($cat->total, 2) }}</span>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted text-center py-4">No expense data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print { .btn, .sidebar, .navbar { display: none !important; } }
</style>
@endsection
