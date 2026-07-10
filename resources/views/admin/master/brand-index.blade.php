@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}" class="text-decoration-none">Brands</a></li>
    <li class="breadcrumb-item active">All Brands</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-bookmark me-2"></i>Brands</h4>
        <p class="text-muted mb-0">Manage product brands</p>
    </div>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Brand
    </a>
</div>

<div class="card glass">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
        <h6 class="card-title fw-bold mb-0">Brand List</h6>
        <form action="{{ route('admin.brands.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search brands..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="brandsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $brand->name }}</div>
                                @if($brand->slug)
                                    <small class="text-muted">{{ $brand->slug }}</small>
                                @endif
                            </td>
                            <td>
                                @if($brand->description)
                                    <span class="text-muted">{{ Str::limit($brand->description, 50) }}</span>
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-box-seam me-1"></i>{{ $brand->products_count ?? $brand->products->count() ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if($brand->is_active)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-bookmark fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No brands found</p>
                                    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="bi bi-plus-lg me-1"></i>Create First Brand
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($brands->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $brands->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
