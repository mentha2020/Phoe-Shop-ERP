@extends('layouts.app')

@section('title', 'Bank Accounts')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Bank Accounts</h1>
        <a href="{{ route('admin.accounting.bank-accounts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Add Account
        </a>
    </div>

    <div class="row g-4">
        @foreach($accounts as $account)
        <div class="col-md-4">
            <div class="card {{ $account->is_default ? 'border-primary' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">
                                {{ $account->name }}
                                @if($account->is_default)
                                    <span class="badge bg-primary">Default</span>
                                @endif
                            </h5>
                            <p class="text-muted mb-0">{{ $account->bank_name }}</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.accounting.bank-accounts.edit', $account->id) }}">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a></li>
                                @if(!$account->is_default && $account->journal_entry_items_count === 0)
                                <li>
                                    <form action="{{ route('admin.accounting.bank-accounts.destroy', $account->id) }}" method="POST"
                                          onsubmit="return confirm('Delete this account?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Account #</span>
                        <span>{{ $account->account_number ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Type</span>
                        <span class="text-uppercase">{{ $account->account_type }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Transactions</span>
                        <span>{{ $account->journal_entry_items_count }}</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h3 class="{{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                            Rs. {{ number_format($account->current_balance, 2) }}
                        </h3>
                        <small class="text-muted">Current Balance</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
