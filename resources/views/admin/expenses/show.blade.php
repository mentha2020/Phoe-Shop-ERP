@extends('layouts.app')

@section('title', 'Expense ' . $expense->reference_number)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expense {{ $expense->reference_number }}</h1>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 200px;">Title</td>
                            <td class="fw-bold">{{ $expense->title }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Reference</td>
                            <td>{{ $expense->reference_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Category</td>
                            <td>{{ $expense->category->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Amount</td>
                            <td class="fw-bold text-primary fs-5">Rs. {{ number_format($expense->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Branch</td>
                            <td>{{ $expense->branch->name ?? 'All branches' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Payment Method</td>
                            <td class="text-uppercase">{{ $expense->payment_method }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Receipt #</td>
                            <td>{{ $expense->receipt_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($expense->status === 'approved')
                                    <span class="badge bg-success fs-6">Approved</span>
                                @elseif($expense->status === 'pending')
                                    <span class="badge bg-warning text-dark fs-6">Pending</span>
                                @else
                                    <span class="badge bg-danger fs-6">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Recurring</td>
                            <td>{{ $expense->is_recurring ? ucfirst($expense->recurring_frequency) : 'No' }}</td>
                        </tr>
                        @if($expense->description)
                        <tr>
                            <td class="text-muted">Description</td>
                            <td>{{ $expense->description }}</td>
                        </tr>
                        @endif
                        @if($expense->notes)
                        <tr>
                            <td class="text-muted">Notes</td>
                            <td>{{ $expense->notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Actions</h5></div>
                <div class="card-body">
                    @if($expense->status === 'pending')
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.expenses.status', $expense->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Approve</button>
                        </form>
                        <form action="{{ route('admin.expenses.status', $expense->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button class="btn btn-danger w-100"><i class="bi bi-x-circle me-1"></i>Reject</button>
                        </form>
                    </div>
                    @endif
                    <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="mt-2"
                          onsubmit="return confirm('Delete this expense?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Delete</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><h5 class="mb-0">Info</h5></div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Created By</td><td>{{ $expense->creator->name }}</td></tr>
                        <tr><td class="text-muted">Created At</td><td>{{ $expense->created_at->format('d M Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
