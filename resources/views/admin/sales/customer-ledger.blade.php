@extends('layouts.app')

@section('title', 'Customer Ledger - ' . $customer->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Customer Ledger: {{ $customer->name }}</h1>
            <small class="text-muted">{{ $customer->email ?? '' }} {{ $customer->phone ? '| ' . $customer->phone : '' }}</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-plus-circle me-1"></i>Record Payment
            </button>
            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Sales</h6>
                    <h3 class="text-primary">${{ number_format($totalSales, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Paid</h6>
                    <h3 class="text-success">${{ number_format($totalPaid, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Refunded</h6>
                    <h3 class="text-warning">${{ number_format($totalRefunded, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Balance Due</h6>
                    <h3 class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">${{ number_format($balance, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Payment History</h5>
        </div>
        <div class="card-body">
            @if($payments->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Invoice</th>
                            <th class="text-end">Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td class="fw-semibold">{{ $payment->reference_number }}</td>
                            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($payment->type === 'payment')
                                    <span class="badge bg-success">Payment</span>
                                @elseif($payment->type === 'refund')
                                    <span class="badge bg-warning text-dark">Refund</span>
                                @elseif($payment->type === 'credit')
                                    <span class="badge bg-info">Credit</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($payment->type) }}</span>
                                @endif
                            </td>
                            <td class="text-uppercase">{{ $payment->method }}</td>
                            <td>
                                @if($payment->sale)
                                    <a href="{{ route('admin.sales.show', $payment->sale_id) }}">{{ $payment->sale->invoice_number }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $payment->type === 'refund' ? 'text-danger' : 'text-success' }}">
                                {{ $payment->type === 'refund' ? '-' : '' }}${{ number_format($payment->amount, 2) }}
                            </td>
                            <td><small class="text-muted">{{ $payment->notes ?? '-' }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
            @else
            <div class="text-center py-4">
                <i class="bi bi-journal-x text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-2">No payment history found</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.sales.customer-payment', $customer->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="e_wallet">E-Wallet</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link to Invoice (Optional)</label>
                        <input type="text" name="sale_id" class="form-control" placeholder="Sale ID if paying for specific invoice">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
