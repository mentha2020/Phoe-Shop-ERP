@extends('layouts.app')

@section('title', 'Sale Detail - ' . $sale->invoice_number)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Sale {{ $sale->invoice_number }}</h1>
            <small class="text-muted">Created {{ $sale->created_at->format('d M Y H:i') }} by {{ $sale->creator->name }}</small>
        </div>
        <div class="d-flex gap-2">
            @if($sale->status === 'completed')
            <a href="{{ route('admin.sales.return-form', $sale->id) }}" class="btn btn-warning">
                <i class="bi bi-arrow-return-left me-1"></i>Process Return
            </a>
            @endif
            <a href="{{ route('admin.pos.receipt', $sale->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-receipt me-1"></i>View Receipt
            </a>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sale Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sale Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product_name }}</div>
                                        <small class="text-muted">{{ $item->product_sku }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">${{ number_format($item->discount_amount, 2) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end">Subtotal:</td>
                                    <td class="text-end">${{ number_format($sale->subtotal, 2) }}</td>
                                </tr>
                                @if($sale->discount_amount > 0)
                                <tr class="text-success">
                                    <td colspan="4" class="text-end">Discount:</td>
                                    <td class="text-end">-${{ number_format($sale->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($sale->tax_amount > 0)
                                <tr>
                                    <td colspan="4" class="text-end">Tax:</td>
                                    <td class="text-end">${{ number_format($sale->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold">${{ number_format($sale->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Returns -->
            @if($sale->returns->count())
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Returns</h5>
                </div>
                <div class="card-body">
                    @foreach($sale->returns as $return)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $return->return_number }}</strong>
                                <span class="badge bg-{{ $return->status === 'completed' ? 'success' : 'warning' }} ms-2">{{ ucfirst($return->status) }}</span>
                            </div>
                            <span class="text-muted">{{ $return->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="mt-2">
                            <strong>Reason:</strong> {{ $return->reason }}
                        </div>
                        <table class="table table-sm mt-2 mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($return->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sale Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($sale->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($sale->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @elseif($sale->status === 'returned')
                                    <span class="badge bg-warning text-dark">Returned</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($sale->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Payment</td>
                            <td class="text-uppercase">{{ $sale->payment_method }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Branch</td>
                            <td>{{ $sale->branch->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Customer</td>
                            <td>
                                @if($sale->customer)
                                    {{ $sale->customer->name }}
                                    @if($sale->customer->phone)
                                        <br><small class="text-muted">{{ $sale->customer->phone }}</small>
                                    @endif
                                @else
                                    Walk-in
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total:</span>
                        <span class="fw-bold">${{ number_format($sale->total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Paid:</span>
                        <span class="text-success">${{ number_format($sale->paid_amount, 2) }}</span>
                    </div>
                    @if($sale->balance > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Balance Due:</span>
                        <span class="text-danger">${{ number_format($sale->balance, 2) }}</span>
                    </div>
                    @endif
                    @if($sale->change_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Change Given:</span>
                        <span>${{ number_format($sale->change_amount, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($sale->customer)
            <div class="card mt-4">
                <div class="card-body text-center">
                    <a href="{{ route('admin.sales.customer-ledger', $sale->customer_id) }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-journal-text me-1"></i>View Customer Ledger
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
