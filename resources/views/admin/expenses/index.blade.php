@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expenses</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.expenses.categories') }}" class="btn btn-outline-primary">
                <i class="bi bi-tags me-1"></i>Categories
            </a>
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>New Expense
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="alert alert-info d-flex justify-content-between">
        <span>Total Expenses (filtered):</span>
        <strong>${{ number_format($totalExpenses, 2) }}</strong>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-body">
            @if($expenses->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ref #</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Branch</th>
                            <th class="text-end">Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                        <tr>
                            <td>
                                <a href="{{ route('admin.expenses.show', $expense->id) }}" class="fw-semibold text-decoration-none">
                                    {{ $expense->reference_number }}
                                </a>
                            </td>
                            <td>{{ $expense->expense_date }}</td>
                            <td>{{ $expense->title }}</td>
                            <td><span class="badge bg-light text-dark">{{ $expense->category->name ?? '-' }}</span></td>
                            <td>{{ $expense->branch->name ?? '-' }}</td>
                            <td class="text-end fw-bold">${{ number_format($expense->amount, 2) }}</td>
                            <td class="text-uppercase">{{ $expense->payment_method }}</td>
                            <td>
                                @if($expense->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($expense->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.expenses.show', $expense->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($expense->status === 'pending')
                                <div class="btn-group btn-group-sm">
                                    <form action="{{ route('admin.expenses.status', $expense->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i></button>
                                    </form>
                                    <form action="{{ route('admin.expenses.status', $expense->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $expenses->withQueryString()->links() }}
            @else
            <div class="text-center py-5">
                <i class="bi bi-cash-stack text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">No expenses found</h5>
                <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary mt-2">Record Expense</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
