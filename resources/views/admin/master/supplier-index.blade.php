@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}" class="text-decoration-none">Suppliers</a></li>
    <li class="breadcrumb-item active">All Suppliers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-truck me-2"></i>Suppliers</h4>
        <p class="text-muted mb-0">Manage your supplier database</p>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Supplier
    </a>
</div>

<div class="card glass">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
        <h6 class="card-title fw-bold mb-0">Supplier List</h6>
        <form action="{{ route('admin.suppliers.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search suppliers..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="suppliersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $supplier->name }}</div>
                                        @if($supplier->tax_number)
                                            <small class="text-muted">TIN: {{ $supplier->tax_number }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($supplier->company)
                                    <span class="badge bg-light text-dark">{{ $supplier->company }}</span>
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->email)
                                    <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $supplier->email }}</div>
                                @endif
                                @if($supplier->phone)
                                    <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $supplier->phone }}</div>
                                @endif
                            </td>
                            <td>
                                @if($supplier->full_address)
                                    <span class="text-muted">{{ Str::limit($supplier->full_address, 40) }}</span>
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->is_active)
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
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
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
                                    <i class="bi bi-truck fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No suppliers found</p>
                                    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="bi bi-plus-lg me-1"></i>Add First Supplier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suppliers->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $suppliers->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
