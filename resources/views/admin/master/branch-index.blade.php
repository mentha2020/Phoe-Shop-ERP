@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.branches.index') }}" class="text-decoration-none">Branches</a></li>
    <li class="breadcrumb-item active">All Branches</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-building me-2"></i>Branches</h4>
        <p class="text-muted mb-0">Manage your branch locations</p>
    </div>
    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Branch
    </a>
</div>

<div class="card glass">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
        <h6 class="card-title fw-bold mb-0">Branch List</h6>
        <form action="{{ route('admin.branches.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search branches..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="branchesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>City</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $branch->name }}</div>
                                @if($branch->email)
                                    <small class="text-muted">{{ $branch->email }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $branch->code }}</span></td>
                            <td>{{ $branch->city ?? '-' }}</td>
                            <td>{{ $branch->phone ?? '-' }}</td>
                            <td>
                                @if($branch->is_warehouse)
                                    <span class="badge bg-info">
                                        <i class="bi bi-house-check me-1"></i>Warehouse
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-shop me-1"></i>Branch
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($branch->is_active)
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
                                    <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this branch?')">
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
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No branches found</p>
                                    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="bi bi-plus-lg me-1"></i>Create First Branch
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($branches->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $branches->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable if needed
    // $('#branchesTable').DataTable({ processing: true, serverSide: true, ... });
});
</script>
@endpush
