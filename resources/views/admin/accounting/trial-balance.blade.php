@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Trial Balance</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
            <a href="{{ route('admin.accounting.journal-entries') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <h4>{{ config('app.name', 'Phone Shop ERP') }}</h4>
                <h5>Trial Balance</h5>
                <small class="text-muted">As of {{ now()->format('d M Y') }}</small>
            </div>

            @if($trialBalance->count())
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trialBalance as $item)
                        <tr>
                            <td>{{ $item['account_code'] }}</td>
                            <td>{{ $item['account_name'] }}</td>
                            <td class="text-end">${{ number_format($item['debit'], 2) }}</td>
                            <td class="text-end">${{ number_format($item['credit'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <td colspan="2" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold">${{ number_format($totalDebit, 2) }}</td>
                            <td class="text-end fw-bold">${{ number_format($totalCredit, 2) }}</td>
                        </tr>
                        <tr class="{{ abs($totalDebit - $totalCredit) < 0.01 ? 'table-success' : 'table-danger' }}">
                            <td colspan="2" class="text-end fw-bold">Difference:</td>
                            <td colspan="2" class="text-center fw-bold">
                                ${{ number_format(abs($totalDebit - $totalCredit), 2) }}
                                {{ abs($totalDebit - $totalCredit) < 0.01 ? '(Balanced)' : '(Unbalanced!)' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-scale text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">No posted journal entries yet</h5>
                <a href="{{ route('admin.accounting.journal-entries.create') }}" class="btn btn-primary mt-2">Create Journal Entry</a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .sidebar, .navbar, .sidebar-overlay { display: none !important; }
    .container-fluid { padding: 0 !important; }
}
</style>
@endsection
