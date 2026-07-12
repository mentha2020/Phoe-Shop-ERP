@extends('layouts.app')

@section('title', 'Accounting & Finance')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Accounting & Finance</h1>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Bank Balance</h6>
                    <h2 class="mb-0">Rs. {{ number_format($totalBalance, 2) }}</h2>
                    <small>{{ $bankAccounts->count() }} accounts</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Debit</h6>
                    <h2 class="mb-0 text-success">Rs. {{ number_format($totalDebit, 2) }}</h2>
                    <small class="text-muted">Posted entries</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Credit</h6>
                    <h2 class="mb-0 text-danger">Rs. {{ number_format($totalCredit, 2) }}</h2>
                    <small class="text-muted">Posted entries</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Net Position</h6>
                    <h2 class="mb-0 {{ $totalDebit - $totalCredit >= 0 ? 'text-success' : 'text-danger' }}">
                        Rs. {{ number_format($totalDebit - $totalCredit, 2) }}
                    </h2>
                    <small class="text-muted">Debit - Credit</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Bank Accounts -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bank Accounts</h5>
                    <a href="{{ route('admin.accounting.bank-accounts') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($bankAccounts->count())
                    @foreach($bankAccounts as $account)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                        <div>
                            <div class="fw-semibold">
                                {{ $account->name }}
                                @if($account->is_default)
                                    <span class="badge bg-primary">Default</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $account->bank_name }} - {{ $account->account_type }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">Rs. {{ number_format($account->current_balance, 2) }}</div>
                            <small class="text-muted">{{ strtoupper($account->currency) }}</small>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted text-center py-3">No bank accounts configured.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Journal Entries -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Journal Entries</h5>
                    <a href="{{ route('admin.accounting.journal-entries') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentEntries->count())
                    @foreach($recentEntries as $entry)
                    <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'border-bottom py-2' : 'pt-2' }}">
                        <div>
                            <a href="{{ route('admin.accounting.journal-entry-show', $entry->id) }}" class="fw-semibold text-decoration-none">
                                {{ $entry->entry_number }}
                            </a>
                            <br><small class="text-muted">{{ Str::limit($entry->description, 40) }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $entry->status === 'posted' ? 'bg-success' : ($entry->status === 'draft' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($entry->status) }}
                            </span>
                            <br><small class="text-muted">{{ $entry->entry_date }}</small>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted text-center py-3">No journal entries yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mt-4">
        <div class="col-md-3">
            <a href="{{ route('admin.accounting.bank-accounts.create') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-bank text-primary" style="font-size: 2rem;"></i>
                    <h6 class="mt-2 mb-0">New Bank Account</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.accounting.journal-entries.create') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text text-success" style="font-size: 2rem;"></i>
                    <h6 class="mt-2 mb-0">New Journal Entry</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.accounting.trial-balance') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-scale text-warning" style="font-size: 2rem;"></i>
                    <h6 class="mt-2 mb-0">Trial Balance</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.accounting.journal-entries') }}" class="card border-0 shadow-sm text-decoration-none">
                <div class="card-body text-center">
                    <i class="bi bi-list-columns text-info" style="font-size: 2rem;"></i>
                    <h6 class="mt-2 mb-0">All Entries</h6>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
