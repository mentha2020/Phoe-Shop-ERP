@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quotations</h1>
        <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>New Quotation
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="card">
        <div class="card-body">
            @if($quotations->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Number</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Valid Until</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $quo)
                        <tr>
                            <td>
                                <a href="{{ route('admin.quotations.show', $quo->id) }}" class="fw-semibold text-decoration-none">
                                    {{ $quo->quotation_number }}
                                </a>
                            </td>
                            <td>{{ $quo->created_at->format('d M Y') }}</td>
                            <td>{{ $quo->customer->name ?? 'Walk-in' }}</td>
                            <td>{{ $quo->valid_until ? \Carbon\Carbon::parse($quo->valid_until)->format('d M Y') : '-' }}</td>
                            <td class="text-end fw-bold">${{ number_format($quo->total, 2) }}</td>
                            <td>
                                @php
                                    $badgeClass = match($quo->status) {
                                        'draft' => 'bg-secondary',
                                        'sent' => 'bg-info',
                                        'accepted' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'expired' => 'bg-warning text-dark',
                                        'converted' => 'bg-primary',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($quo->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.quotations.show', $quo->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $quotations->withQueryString()->links() }}
            @else
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">No quotations found</h5>
                <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary mt-2">Create Quotation</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
