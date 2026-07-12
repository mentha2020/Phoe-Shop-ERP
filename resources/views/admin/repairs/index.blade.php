@extends('layouts.app')

@section('title', 'Repair Jobs')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Repair Jobs</h1>
        <a href="{{ route('admin.repairs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>New Repair Job
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Ticket, device, customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="diagnosed" {{ request('status') == 'diagnosed' ? 'selected' : '' }}>Diagnosed</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="waiting_parts" {{ request('status') == 'waiting_parts' ? 'selected' : '' }}>Waiting Parts</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="{{ route('admin.repairs.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Repairs Table -->
    <div class="card">
        <div class="card-body">
            @if($repairs->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket #</th>
                            <th>Device</th>
                            <th>Customer</th>
                            <th>Issue</th>
                            <th>Est. Cost</th>
                            <th>Deposit</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($repairs as $repair)
                        <tr>
                            <td>
                                <a href="{{ route('admin.repairs.show', $repair->id) }}" class="fw-semibold text-decoration-none">
                                    {{ $repair->ticket_number }}
                                </a>
                                <br><small class="text-muted">{{ $repair->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $repair->device_brand }} {{ $repair->device_model }}</div>
                                <small class="text-muted">{{ $repair->device_type }}</small>
                            </td>
                            <td>{{ $repair->customer->name ?? 'Walk-in' }}</td>
                            <td><small>{{ Str::limit($repair->issue_description, 50) }}</small></td>
                            <td class="text-end">Rs. {{ number_format($repair->estimated_cost, 2) }}</td>
                            <td class="text-end">Rs. {{ number_format($repair->deposit_amount, 2) }}</td>
                            <td>{{ $repair->assignedTo->name ?? '-' }}</td>
                            <td><span class="badge {{ $repair->status_badge_class }}">{{ $repair->status_label }}</span></td>
                            <td>
                                <a href="{{ route('admin.repairs.show', $repair->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $repairs->withQueryString()->links() }}
            @else
            <div class="text-center py-5">
                <i class="bi bi-tools text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">No repair jobs found</h5>
                <a href="{{ route('admin.repairs.create') }}" class="btn btn-primary mt-2">Create Repair Job</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
