@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sales History</h1>
        <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>New Sale
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="{{ route('admin.pos.history') }}" class="btn btn-outline-secondary">Clear</a>
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
                            <th>Items</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('admin.pos.receipt', $sale->id) }}" class="fw-semibold text-decoration-none">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td>{{ $sale->items->sum('quantity') }}</td>
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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.pos.receipt', $sale->id) }}">
                                            <i class="bi bi-receipt me-2"></i>View Receipt
                                        </a></li>
                                        @if($sale->status === 'completed')
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.pos.cancel', $sale->id) }}" method="POST"
                                                  onsubmit="return confirm('Cancel this sale? Stock will be restored.')">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-x-circle me-2"></i>Cancel Sale
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
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
                <a href="{{ route('admin.pos.index') }}" class="btn btn-primary mt-2">Start Selling</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
