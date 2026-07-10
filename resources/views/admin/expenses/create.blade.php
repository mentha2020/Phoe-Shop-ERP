@extends('layouts.app')

@section('title', 'New Expense')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">New Expense</h1>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.expenses.store') }}" method="POST">
                        @csrf

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
                            <div class="col-md-6">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g., Office Rent">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="expense_category_id" class="form-select" required>
                                    <option value="">Select category...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required value="{{ old('amount') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="expense_date" class="form-control" required value="{{ old('expense_date', now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">All branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
                                    <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="e_wallet" {{ old('payment_method') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Receipt #</label>
                                <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Recurring?</label>
                                <select name="is_recurring" class="form-select" id="isRecurring" onchange="toggleRecurring()">
                                    <option value="0">No</option>
                                    <option value="1" {{ old('is_recurring') ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="recurringFreq" style="display: none;">
                                <label class="form-label">Frequency</label>
                                <select name="recurring_frequency" class="form-select">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-1"></i>Record Expense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleRecurring() {
    document.getElementById('recurringFreq').style.display =
        document.getElementById('isRecurring').value === '1' ? 'block' : 'none';
}
</script>
@endpush
