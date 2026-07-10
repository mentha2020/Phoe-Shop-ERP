@extends('layouts.auth', ['title' => 'Access Denied'])

@section('content')
<div class="text-center">
    <div class="mb-4">
        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
            <i class="bi bi-shield-x text-danger" style="font-size: 3rem;"></i>
        </div>
    </div>
    <h1 class="fw-bold text-danger">403</h1>
    <h4 class="fw-bold mb-3">Access Denied</h4>
    <p class="text-muted mb-4">You don't have permission to access this page. Please contact your administrator if you believe this is an error.</p>
    <div class="d-flex justify-content-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-house me-1"></i>Go to Dashboard
        </a>
        <button onclick="history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Go Back
        </button>
    </div>
</div>
@endsection
