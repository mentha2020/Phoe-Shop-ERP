@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.branches.index') }}" class="text-decoration-none">Branches</a></li>
    <li class="breadcrumb-item active">Edit Branch</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-building me-2"></i>Edit Branch</h4>
        <p class="text-muted mb-0">Update branch: {{ $branch->name }}</p>
    </div>
    <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<form action="{{ route('admin.branches.update', $branch) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card glass">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="card-title fw-bold mb-0">Branch Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-medium">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $branch->name) }}" placeholder="Enter branch name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="code" class="form-label fw-medium">Branch Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $branch->code) }}" placeholder="e.g. BR-001" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label fw-medium">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Enter full address">{{ old('address', $branch->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="city" class="form-label fw-medium">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $branch->city) }}" placeholder="Enter city">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="state" class="form-label fw-medium">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state', $branch->state) }}" placeholder="Enter state">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-medium">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $branch->phone) }}" placeholder="Enter phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $branch->email) }}" placeholder="Enter email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card glass">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="card-title fw-bold mb-0">Settings</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_warehouse" name="is_warehouse" value="1" {{ old('is_warehouse', $branch->is_warehouse) ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="is_warehouse">Warehouse</label>
                        <div class="form-text">Mark this branch as a warehouse location</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="is_active">Active</label>
                        <div class="form-text">Enable this branch for operations</div>
                    </div>
                </div>
            </div>

            <div class="card glass mt-4">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Update Branch
                    </button>
                    <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Cancel
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>

<div class="row g-4 mt-0">
    <div class="col-lg-8"></div>
    <div class="col-lg-4">
        <div class="card glass mt-4 border-danger">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="card-title fw-bold mb-0 text-danger">Danger Zone</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Deleting a branch will remove all associated data. This action cannot be undone.</p>
                <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this branch? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>Delete Branch
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
