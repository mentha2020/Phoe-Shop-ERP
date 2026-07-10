@extends('layouts.app')

@section('title', 'Stock Management')

@section('breadcrumb')
    <li class="breadcrumb-item active">Stock</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Stock Management</h1>
        <p class="text-muted mb-0">Monitor inventory levels across branches</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-outline-warning">
            <i class="bi bi-sliders me-1"></i>Adjustment
        </a>
        <a href="{{ route('admin.stock-transfers.create') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-left-right me-1"></i>Transfer
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-box-seam text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stocks->total() }}</div>
                        <div class="text-muted small">Stock Records</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-currency-dollar text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">${{ number_format($totalValue, 2) }}</div>
                        <div class="text-muted small">Total Stock Value</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ \App\Models\Stock::lowStock()->count() }}</div>
                        <div class="text-muted small">Low Stock Items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-x-circle text-danger fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ \App\Models\Stock::outOfStock()->count() }}</div>
                        <div class="text-muted small">Out of Stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <form action="{{ route('admin.stock.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Branch</label>
                <select name="branch_id" class="form-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Product name, SKU..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Product</th>
                        <th>Branch</th>
                        <th>Variant</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Min</th>
                        <th class="text-center">Max</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr class="{{ $stock->is_low_stock ? 'table-warning' : '' }} {{ $stock->is_out_of_stock ? 'table-danger' : '' }}">
                            <td class="ps-3">
                                <div class="fw-medium">{{ $stock->product->name }}</div>
                                <small class="text-muted">{{ $stock->product->sku ?? '-' }}</small>
                            </td>
                            <td>{{ $stock->branch->name }}</td>
                            <td>{{ $stock->productVariant?->name ?? '-' }}</td>
                            <td class="text-center fw-bold">{{ $stock->quantity }}</td>
                            <td class="text-center">{{ $stock->min_stock }}</td>
                            <td class="text-center">{{ $stock->max_stock }}</td>
                            <td>
                                @if($stock->is_out_of_stock)
                                    <span class="badge bg-danger-subtle text-danger">Out of Stock</span>
                                @elseif($stock->is_low_stock)
                                    <span class="badge bg-warning-subtle text-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">In Stock</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStockModal"
                                    data-id="{{ $stock->id }}"
                                    data-branch="{{ $stock->branch_id }}"
                                    data-product="{{ $stock->product_id }}"
                                    data-variant="{{ $stock->product_variant_id }}"
                                    data-qty="{{ $stock->quantity }}"
                                    data-min="{{ $stock->min_stock }}"
                                    data-max="{{ $stock->max_stock }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No stock records found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($stocks->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $stocks->withQueryString()->links() }}
        </div>
    @endif
</div>

<div class="modal fade" id="editStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.stock.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Stock</label>
                            <input type="number" name="min_stock" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Max Stock</label>
                            <input type="number" name="max_stock" class="form-control" min="0" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
