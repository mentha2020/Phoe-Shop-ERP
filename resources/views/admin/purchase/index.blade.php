@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('breadcrumb')
    <li class="breadcrumb-item active">Purchase Orders</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><i class="bi bi-bag-check me-2"></i>Purchase Orders</h1>
        <p class="text-muted mb-0">Manage supplier purchase orders</p>
    </div>
    <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>New Purchase Order
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <form action="{{ route('admin.purchase-orders.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" class="form-control" placeholder="PO number, supplier..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">All</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach(['draft','pending','approved','partial','received','cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Reference</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Branch</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('admin.purchase-orders.show', $order) }}" class="fw-medium text-dark text-decoration-none">{{ $order->reference_number }}</a>
                            </td>
                            <td>{{ $order->order_date->format('M d, Y') }}</td>
                            <td>{{ $order->supplier->name }}</td>
                            <td>{{ $order->branch->name }}</td>
                            <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @if($order->paid_amount > 0)
                                    <span class="text-success">${{ number_format($order->paid_amount, 2) }}</span>
                                @else
                                    <span class="text-muted">$0.00</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $order->status_badge }}-subtle text-{{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.purchase-orders.show', $order) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-bag fs-1 d-block mb-2"></i>
                                <p class="mb-0">No purchase orders found</p>
                                <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary btn-sm mt-3"><i class="bi bi-plus-lg me-1"></i>Create First PO</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white border-top">{{ $orders->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
