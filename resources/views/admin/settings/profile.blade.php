@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Settings</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-gear me-2"></i>General
                </a>
                <a href="{{ route('admin.settings.notifications') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-bell me-2"></i>Notifications
                </a>
                <a href="{{ route('admin.settings.profile') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-person me-2"></i>My Profile
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.profile.update') }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Language</label>
                                <select name="language" class="form-select">
                                    <option value="en" {{ $user->language === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="id" {{ $user->language === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-select">
                                    <option value="UTC" {{ $user->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="Asia/Jakarta" {{ $user->timezone === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" {{ $user->timezone === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" {{ $user->timezone === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted" style="width: 200px;">User ID</td><td><code>{{ $user->id }}</code></td></tr>
                        <tr><td class="text-muted">Role</td><td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </td></tr>
                        <tr><td class="text-muted">Branch</td><td>{{ $user->branch->name ?? 'Not assigned' }}</td></tr>
                        <tr><td class="text-muted">Member Since</td><td>{{ $user->created_at->format('d M Y') }}</td></tr>
                        <tr><td class="text-muted">Last Login</td><td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Never' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
