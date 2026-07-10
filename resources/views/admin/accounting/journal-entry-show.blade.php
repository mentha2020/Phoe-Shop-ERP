@extends('layouts.app')

@section('title', 'Journal Entry ' . $entry->entry_number)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Journal Entry {{ $entry->entry_number }}</h1>
            <small class="text-muted">Created {{ $entry->created_at->format('d M Y H:i') }} by {{ $entry->creator->name }}</small>
        </div>
        <div class="d-flex gap-2">
            @if($entry->status === 'draft')
            <form action="{{ route('admin.accounting.journal-entry-post', $entry->id) }}" method="POST"
                  onsubmit="return confirm('Post this entry? Bank balances will be updated.')">
                @csrf
                <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Post Entry</button>
            </form>
            @endif
            @if($entry->status === 'posted')
            <form action="{{ route('admin.accounting.journal-entry-void', $entry->id) }}" method="POST"
                  onsubmit="return confirm('Void this entry? Bank balances will be reversed.')">
                @csrf
                <button class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Void Entry</button>
            </form>
            @endif
            <a href="{{ route('admin.accounting.journal-entries') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entry->items as $item)
                                <tr>
                                    <td>{{ $item->account_code }}</td>
                                    <td>{{ $item->account_name }}</td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td class="text-end">{{ $item->debit > 0 ? '$' . number_format($item->debit, 2) : '-' }}</td>
                                    <td class="text-end">{{ $item->credit > 0 ? '$' . number_format($item->credit, 2) : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end fw-bold">Totals:</td>
                                    <td class="text-end fw-bold">${{ number_format($entry->total_debit, 2) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($entry->total_credit, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Entry Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Number</td><td>{{ $entry->entry_number }}</td></tr>
                        <tr><td class="text-muted">Date</td><td>{{ $entry->entry_date }}</td></tr>
                        <tr><td class="text-muted">Type</td><td class="text-uppercase">{{ $entry->type }}</td></tr>
                        <tr><td class="text-muted">Status</td><td>
                            @if($entry->status === 'posted')
                                <span class="badge bg-success">Posted</span>
                            @elseif($entry->status === 'draft')
                                <span class="badge bg-warning text-dark">Draft</span>
                            @else
                                <span class="badge bg-danger">Voided</span>
                            @endif
                        </td></tr>
                        <tr><td class="text-muted">Description</td><td>{{ $entry->description }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
