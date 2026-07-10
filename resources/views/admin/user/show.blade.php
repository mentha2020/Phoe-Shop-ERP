@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">User Profile</h1>
        <p class="text-muted mb-0">Viewing details for {{ $user->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle mb-3" width="100" height="100" alt="{{ $user->name }}">
                @else
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 2rem;">
                        {{ $user->initials }}
                    </div>
                @endif
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                @foreach($user->getRoleNames() as $roleName)
                    <span class="badge bg-primary-subtle text-primary fs-6">{{ $roleName }}</span>
                @endforeach
                <div class="mt-3">
                    @if($user->status === 'active')
                        <span class="badge bg-success-subtle text-success">
                            <i class="bi bi-check-circle-fill me-1"></i>Active
                        </span>
                    @elseif($user->status === 'inactive')
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="bi bi-pause-circle-fill me-1"></i>Inactive
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger">
                            <i class="bi bi-x-circle-fill me-1"></i>Suspended
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="bi bi-clock-history me-2"></i>Activity</h6>
                <div class="small text-muted">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Account Created</span>
                        <span>{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Last Updated</span>
                        <span>{{ $user->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Last Login</span>
                        <span>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Full Name</label>
                        <div class="fw-medium">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Email Address</label>
                        <div class="fw-medium">
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Phone Number</label>
                        <div class="fw-medium">
                            @if($user->phone)
                                <a href="tel:{{ $user->phone }}" class="text-decoration-none">{{ $user->phone }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Branch</label>
                        <div class="fw-medium">{{ $user->branch?->name ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Role & Permissions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Assigned Role</label>
                        <div>
                            @foreach($user->getRoleNames() as $roleName)
                                <span class="badge bg-primary-subtle text-primary fs-6">{{ $roleName }}</span>
                            @endforeach
                            @if($user->getRoleNames()->isEmpty())
                                <span class="text-muted">No role assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Permissions</label>
                        <div>
                            @php $permissions = $user->getAllPermissions(); @endphp
                            @if($permissions->count() > 0)
                                <span class="badge bg-success-subtle text-success">{{ $permissions->count() }} permissions</span>
                            @else
                                <span class="text-muted">No permissions assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($permissions->isNotEmpty())
                    <div class="mt-3">
                        <label class="form-label text-muted small">Permission List</label>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($permissions->take(20) as $permission)
                                <span class="badge bg-light text-dark">{{ $permission->name }}</span>
                            @endforeach
                            @if($permissions->count() > 20)
                                <span class="badge bg-light text-muted">+{{ $permissions->count() - 20 }} more</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Account Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Status</label>
                        <div>
                            @if($user->status === 'active')
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @elseif($user->status === 'inactive')
                                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Suspended</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Email Verified</label>
                        <div>
                            @if($user->email_verified_at)
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>Verified
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i>Not Verified
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
