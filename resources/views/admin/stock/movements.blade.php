@extends('layouts.app')

@section('title', 'Stock Movements')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.stock.index') }}" class="text-decoration-none">Stock</a></li>
    <li class="breadcrumb-item active">Movements</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Stock Movements</h1>
        <p class="text-muted mb-0">Track all stock changes across the system</p>
    </div>
    <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Stock
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <form action="{{ route('admin.stock.movements') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted">Branch</label>
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                    <option value="transfer_in" {{ request('type') === 'transfer_in' ? 'selected' : '' }}>Transfer In</option>
                    <option value="transfer_out" {{ request('type') === 'transfer_out' ? 'selected' : '' }}>Transfer Out</option>
                    <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Return</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.stock.movements') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Product</th>
                        <th>Branch</th>
                        <th>Type</th>
                        <th class="text-center">Qty Before</th>
                        <th class="text-center">Change</th>
                        <th class="text-center">Qty After</th>
                        <th>User</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td class="ps-3 small">{{ $movement->created_at->format('M d, H:i') }}</td>
                            <td>
                                <div class="fw-medium">{{ $movement->product->name }}</div>
                                @if($movement->productVariant)
                                    <small class="text-muted">{{ $movement->productVariant->name }}</small>
                                @endif
                            </td>
                            <td>{{ $movement->branch->name }}</td>
                            <td>
                                @php
                                    $typeColors = [
                                        'purchase' => 'success',
                                        'sale' => 'primary',
                                        'adjustment' => 'warning',
                                        'transfer_in' => 'info',
                                        'transfer_out' => 'info',
                                        'return' => 'secondary',
                                    ];
                                    $color = $typeColors[$movement->type] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $movement->type)) }}</span>
                            </td>
                            <td class="text-center">{{ $movement->quantity_before }}</td>
                            <td class="text-center fw-bold {{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </td>
                            <td class="text-center">{{ $movement->quantity_after }}</td>
                            <td>{{ $movement->user?->name ?? '-' }}</td>
                            <td><small class="text-muted">{{ Str::limit($movement->notes, 30) ?? '-' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-arrow-left-right fs-1 d-block mb-2"></i>
                                <p class="mb-0">No stock movements found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($movements->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $movements->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
