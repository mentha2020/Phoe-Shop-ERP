@extends('layouts.app')

@section('title', 'Edit Role - ' . $role->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">Administration</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}" class="text-decoration-none">Roles</a></li>
    <li class="breadcrumb-item active">Edit {{ $role->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Edit Role</h1>
        <p class="text-muted mb-0">Update role information and permissions</p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Roles
    </a>
</div>

<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-shield me-2"></i>Role Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-key me-2"></i>Permissions</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                        <label class="form-check-label" for="selectAllPermissions">Select All</label>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($permissions->groupBy(fn($p) => explode('.', $p->name)[0]) as $module => $modulePermissions)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-uppercase text-muted fw-bold mb-0">
                                    <i class="bi bi-folder2 me-1"></i>{{ ucfirst($module) }}
                                </h6>
                                <div class="form-check">
                                    <input class="form-check-input module-select" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                                    <label class="form-check-label small" for="module_{{ $module }}">Select All</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                @foreach($modulePermissions as $permission)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" data-module="{{ $module }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                {{ ucfirst(explode('.', $permission->name)[1] ?? '') }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-3">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 1rem;">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Role Details</h6>
                    <div class="small text-muted mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Created:</span>
                            <span>{{ $role->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Permissions:</span>
                            <span>{{ $role->permissions->count() }} assigned</span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Update Role
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.module-select').forEach(moduleCheckbox => {
        moduleCheckbox.addEventListener('change', function() {
            const module = this.dataset.module;
            document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    document.getElementById('selectAllPermissions').addEventListener('change', function() {
        document.querySelectorAll('.permission-checkbox, .module-select').forEach(cb => {
            cb.checked = this.checked;
        });
    });
</script>
@endpush
