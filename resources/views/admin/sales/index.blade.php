@extends('layouts.app')

@section('title', 'Sales Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sales Management</h1>
        <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>New Sale
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-receipt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h4 class="mb-0">{{ number_format($totalSales) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-currency-dollar text-success fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Amount</h6>
                            <h4 class="mb-0">${{ number_format($totalAmount, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-credit-card text-info fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Paid</h6>
                            <h4 class="mb-0">${{ number_format($totalPaid, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Outstanding</h6>
                            <h4 class="mb-0">${{ number_format($totalAmount - $totalPaid, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Invoice or customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment</label>
                    <select name="payment_method" class="form-select">
                        <option value="">All</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-body">
            @if($sales->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('admin.sales.show', $sale->id) }}" class="fw-semibold text-decoration-none">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td>{{ $sale->branch->name ?? '-' }}</td>
                            <td class="text-end fw-bold">${{ number_format($sale->total, 2) }}</td>
                            <td class="text-end">${{ number_format($sale->paid_amount, 2) }}</td>
                            <td><span class="badge bg-info text-dark text-uppercase">{{ $sale->payment_method }}</span></td>
                            <td>
                                @if($sale->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($sale->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @elseif($sale->status === 'returned')
                                    <span class="badge bg-warning text-dark">Returned</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($sale->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($sale->status === 'completed')
                                <a href="{{ route('admin.sales.return-form', $sale->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-arrow-return-left"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $sales->withQueryString()->links() }}
            @else
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">No sales found</h5>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
