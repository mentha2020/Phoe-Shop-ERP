@extends('layouts.app')

@section('title', 'Journal Entries')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Journal Entries</h1>
        <a href="{{ route('admin.accounting.journal-entries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>New Entry
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="voided" {{ request('status') == 'voided' ? 'selected' : '' }}>Voided</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All</option>
                        <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>Receipt</option>
                        <option value="journal" {{ request('type') == 'journal' ? 'selected' : '' }}>Journal</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
                    <a href="{{ route('admin.accounting.journal-entries') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Entries Table -->
    <div class="card">
        <div class="card-body">
            @if($entries->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Entry #</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('admin.accounting.journal-entry-show', $entry->id) }}" class="fw-semibold text-decoration-none">
                                    {{ $entry->entry_number }}
                                </a>
                            </td>
                            <td>{{ $entry->entry_date }}</td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst($entry->type) }}</span></td>
                            <td>{{ Str::limit($entry->description, 50) }}</td>
                            <td class="text-end">Rs. {{ number_format($entry->total_debit, 2) }}</td>
                            <td class="text-end">Rs. {{ number_format($entry->total_credit, 2) }}</td>
                            <td>
                                @if($entry->status === 'posted')
                                    <span class="badge bg-success">Posted</span>
                                @elseif($entry->status === 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @else
                                    <span class="badge bg-danger">Voided</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.accounting.journal-entry-show', $entry->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $entries->withQueryString()->links() }}
            @else
            <div class="text-center py-5">
                <i class="bi bi-journal text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">No journal entries found</h5>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
