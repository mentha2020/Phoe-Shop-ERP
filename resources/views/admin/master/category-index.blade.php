@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}" class="text-decoration-none">Categories</a></li>
    <li class="breadcrumb-item active">All Categories</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-tags me-2"></i>Categories</h4>
        <p class="text-muted mb-0">Manage product categories and subcategories</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Category
    </a>
</div>

<div class="card glass">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
        <h6 class="card-title fw-bold mb-0">Category List</h6>
        <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search categories..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="categoriesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Children</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($category->icon)
                                        <i class="bi bi-{{ $category->icon }} me-2 text-primary"></i>
                                    @else
                                        <i class="bi bi-folder me-2 text-muted"></i>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $category->name }}</div>
                                        @if($category->description)
                                            <small class="text-muted">{{ Str::limit($category->description, 40) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $category->parent->name ?? 'Root' }}
                                </span>
                            </td>
                            <td>
                                @if($category->children->count() > 0)
                                    <span class="badge bg-primary-subtle text-primary">
                                        <i class="bi bi-diagram-3 me-1"></i>{{ $category->children->count() }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-box-seam me-1"></i>{{ $category->products_count ?? $category->products->count() ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if($category->is_active)
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
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
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
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No categories found</p>
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="bi bi-plus-lg me-1"></i>Create First Category
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $categories->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
