@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Inventory Report</h1>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i>Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Products</h6>
                    <h3 class="text-primary">{{ number_format($totalProducts) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Stock Value</h6>
                    <h3 class="text-success">Rs. {{ number_format($totalStockValue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Low Stock</h6>
                    <h3 class="text-warning">{{ $lowStockCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Out of Stock</h6>
                    <h3 class="text-danger">{{ $outOfStockCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Category Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Products by Category</h5></div>
                <div class="card-body">
                    @foreach($stockByCategory as $cat)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                        <span>{{ $cat['name'] }}</span>
                        <span class="badge bg-primary">{{ $cat['count'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Low Stock Alerts</h5></div>
                <div class="card-body">
                    @if($lowStockProducts->count())
                    <div style="max-height: 300px; overflow-y: auto;">
                        @foreach($lowStockProducts->take(10) as $stock)
                        <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                            <div>
                                <div class="fw-semibold">{{ $stock->product->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $stock->branch->name ?? '-' }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $stock->quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $stock->quantity }} / {{ $stock->min_stock }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-4">All stock levels are healthy!</p>
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
