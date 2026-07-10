@extends('layouts.app')

@section('title', 'New Repair Job')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">New Repair Job</h1>
        <a href="{{ route('admin.repairs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <form action="{{ route('admin.repairs.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-8">
                <!-- Device Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Device Information</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Device Type <span class="text-danger">*</span></label>
                                <select name="device_type" class="form-select" required>
                                    <option value="">Select type...</option>
                                    <option value="Smartphone">Smartphone</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Desktop">Desktop</option>
                                    <option value="Smartwatch">Smartwatch</option>
                                    <option value="Headphone">Headphone/Earphone</option>
                                    <option value="Charger">Charger/Adapter</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Brand <span class="text-danger">*</span></label>
                                <input type="text" name="device_brand" class="form-control" required placeholder="e.g., Apple, Samsung">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Model <span class="text-danger">*</span></label>
                                <input type="text" name="device_model" class="form-control" required placeholder="e.g., iPhone 15 Pro">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Serial Number / IMEI</label>
                                <input type="text" name="device_serial" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Password / PIN</label>
                                <input type="text" name="device_password" class="form-control" placeholder="For testing">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Issue Description <span class="text-danger">*</span></label>
                                <textarea name="issue_description" class="form-control" rows="3" required placeholder="Describe the issue in detail..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Assignment -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Assignment</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Cost -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Cost & Schedule</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Estimated Cost <span class="text-danger">*</span></label>
                            <input type="number" name="estimated_cost" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deposit Amount</label>
                            <input type="number" name="deposit_amount" class="form-control" step="0.01" min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Est. Completion Date</label>
                            <input type="date" name="estimated_completion" class="form-control">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-check-circle me-1"></i>Create Repair Job
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
