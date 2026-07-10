@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-decoration-none">Customers</a></li>
    <li class="breadcrumb-item active">All Customers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-people me-2"></i>Customers</h4>
        <p class="text-muted mb-0">Manage your customer database</p>
    </div>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Customer
    </a>
</div>

<div class="card glass">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
        <h6 class="card-title fw-bold mb-0">Customer List</h6>
        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search customers..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="customersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <span class="fw-bold" style="font-size: 0.85rem;">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $customer->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($customer->email)
                                    <div><i class="bi bi-envelope me-1 text-muted"></i>{{ $customer->email }}</div>
                                @endif
                                @if($customer->phone)
                                    <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $customer->phone }}</div>
                                @endif
                            </td>
                            <td>
                                @if($customer->full_address)
                                    <span class="text-muted">{{ Str::limit($customer->full_address, 40) }}</span>
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->is_active)
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
                                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?')">
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
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No customers found</p>
                                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="bi bi-plus-lg me-1"></i>Add First Customer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $customers->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
