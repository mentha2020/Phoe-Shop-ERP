@extends('layouts.app')

@section('title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">My Profile</h1>
        <p class="text-muted mb-0">Manage your account settings</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle mb-3" width="100" height="100" alt="{{ $user->name }}">
                @else
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                @foreach($user->getRoleNames() as $roleName)
                    <span class="badge bg-primary-subtle text-primary">{{ $roleName }}</span>
                @endforeach
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Update Avatar</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    <div class="mb-3">
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" name="avatar" id="avatarInput" accept="image/*" required>
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="avatarPreview" class="text-center mb-3" style="display: none;">
                        <img src="" class="rounded-circle" width="80" height="80" alt="Preview">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-upload me-1"></i>Upload Avatar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch</label>
                            <input type="text" class="form-control" value="{{ $user->branch?->name ?? 'Not assigned' }}" disabled>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.password') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key me-1"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Account Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Email Verified</label>
                        <div>
                            @if($user->email_verified_at)
                                <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i>Verified</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning"><i class="bi bi-exclamation-circle-fill me-1"></i>Not Verified</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Last Login</label>
                        <div class="small">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    document.getElementById('avatarInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('avatarPreview');
                preview.querySelector('img').src = ev.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
