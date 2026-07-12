@extends('layouts.app')

@section('title', 'Role Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">Administration</a></li>
    <li class="breadcrumb-item active">Roles & Permissions</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Roles & Permissions</h1>
        <p class="text-muted mb-0">Manage user roles and their assigned permissions</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Role
    </a>
</div>

<div class="row g-4">
    @forelse($roles as $role)
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                <i class="bi bi-shield-check text-primary fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $role->name }}</h5>
                                <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-dark p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.roles.edit', $role) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger" onclick="confirmRoleDelete('{{ $role->id }}', '{{ $role->name }}')">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        @if($role->permissions->isNotEmpty())
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($role->permissions->take(6) as $permission)
                                    <span class="badge bg-light text-dark">{{ $permission->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 6)
                                    <span class="badge bg-light text-muted">+{{ $role->permissions->count() - 6 }} more</span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted small">No permissions assigned</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <button class="btn btn-sm btn-outline-danger" onclick="confirmRoleDelete('{{ $role->id }}', '{{ $role->name }}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="text-muted">
                        <i class="bi bi-shield fs-1 d-block mb-2"></i>
                        <p class="mb-0">No roles found</p>
                        <a href="{{ route('admin.roles.create') }}" class="text-primary text-decoration-none">Create your first role</a>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Delete Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                        <i class="bi bi-exclamation-triangle text-danger fs-2"></i>
                    </div>
                    <p>Are you sure you want to delete <strong id="deleteRoleName"></strong>?</p>
                    <p class="text-muted small mb-0">Users with this role will lose their associated permissions.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRoleForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmRoleDelete(id, name) {
        document.getElementById('deleteRoleName').textContent = name;
        document.getElementById('deleteRoleForm').action = '{{ route("admin.roles.index") }}/' + id;
        new bootstrap.Modal(document.getElementById('deleteRoleModal')).show();
    }
</script>
@endpush
