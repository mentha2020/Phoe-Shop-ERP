@extends('layouts.app')

@section('title', isset($account) ? 'Edit Bank Account' : 'New Bank Account')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ isset($account) ? 'Edit Bank Account' : 'New Bank Account' }}</h1>
        <a href="{{ route('admin.accounting.bank-accounts') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($account) ? route('admin.accounting.bank-accounts.update', $account->id) : route('admin.accounting.bank-accounts.store') }}" method="POST">
                        @csrf
                        @if(isset($account)) @method('PUT') @endif

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
                                <label class="form-label">Account Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name', $account->name ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" class="form-control" required value="{{ old('bank_name', $account->bank_name ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $account->account_number ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Account Type <span class="text-danger">*</span></label>
                                <select name="account_type" class="form-select" required>
                                    <option value="savings" {{ (old('account_type', $account->account_type ?? '') === 'savings') ? 'selected' : '' }}>Savings</option>
                                    <option value="checking" {{ (old('account_type', $account->account_type ?? '') === 'checking') ? 'selected' : '' }}>Checking</option>
                                    <option value="credit" {{ (old('account_type', $account->account_type ?? '') === 'credit') ? 'selected' : '' }}>Credit Card</option>
                                    <option value="cash_wallet" {{ (old('account_type', $account->account_type ?? '') === 'cash_wallet') ? 'selected' : '' }}>Cash / Wallet</option>
                                </select>
                            </div>
                            @if(!isset($account))
                            <div class="col-md-6">
                                <label class="form-label">Opening Balance</label>
                                <input type="number" name="opening_balance" class="form-control" step="0.01" min="0" value="{{ old('opening_balance', 0) }}">
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label class="form-label">Currency</label>
                                <input type="text" name="currency" class="form-control" maxlength="3" value="{{ old('currency', $account->currency ?? 'USD') }}">
                            </div>
                            @if(isset($account))
                            <div class="col-md-6">
                                <label class="form-label">Current Balance</label>
                                <input type="text" class="form-control" value="Rs. {{ number_format($account->current_balance, 2) }}" readonly>
                                <small class="text-muted">Balance is managed through journal entries</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Active</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $account->is_active ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ !$account->is_active ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $account->notes ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>{{ isset($account) ? 'Update' : 'Create' }} Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
