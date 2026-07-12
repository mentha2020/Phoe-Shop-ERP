@extends('layouts.app')

@section('title', 'Purchase Order - ' . $order->reference_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.purchase-orders.index') }}" class="text-decoration-none">Purchase Orders</a></li>
    <li class="breadcrumb-item active">{{ $order->reference_number }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $order->reference_number }}</h1>
        <p class="text-muted mb-0">Created {{ $order->created_at->format('M d, Y H:i') }} by {{ $order->createdBy?->name ?? 'System' }}</p>
    </div>
    <div class="d-flex gap-2">
        @if(in_array($order->status, ['draft', 'pending']))
            <form action="{{ route('admin.purchase-orders.approve', $order) }}" method="POST" onsubmit="return confirm('Approve this purchase order?')">
                @csrf <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Approve</button>
            </form>
        @endif
        @if(in_array($order->status, ['approved', 'partial']))
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#receiveModal"><i class="bi bi-box-arrow-in-down me-1"></i>Receive Items</button>
        @endif
        @if(!in_array($order->status, ['received', 'cancelled']))
            <form action="{{ route('admin.purchase-orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel this purchase order?')">
                @csrf <button type="submit" class="btn btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Cancel</button>
            </form>
        @endif
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom"><h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Order Details</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Status</label>
                        <div><span class="badge bg-{{ $order->status_badge }}-subtle text-{{ $order->status_badge }} fs-6">{{ ucfirst($order->status) }}</span></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Supplier</label>
                        <div class="fw-medium">{{ $order->supplier->name }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Branch</label>
                        <div class="fw-medium">{{ $order->branch->name }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Order Date</label>
                        <div>{{ $order->order_date->format('M d, Y') }}</div>
                    </div>
                    @if($order->expected_date)
                        <div class="col-md-3"><label class="form-label small text-muted">Expected</label><div>{{ $order->expected_date->format('M d, Y') }}</div></div>
                    @endif
                    @if($order->received_date)
                        <div class="col-md-3"><label class="form-label small text-muted">Received</label><div>{{ $order->received_date->format('M d, Y') }}</div></div>
                    @endif
                    @if($order->notes)
                        <div class="col-12"><label class="form-label small text-muted">Notes</label><div>{{ $order->notes }}</div></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom"><h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Order Items</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th class="ps-3">Product</th>
                                <th>Ordered</th>
                                <th>Received</th>
                                <th>Pending</th>
                                <th>Unit Cost</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-medium">{{ $item->product->name }}</div>
                                        @if($item->productVariant)<small class="text-muted">{{ $item->productVariant->name }}</small>@endif
                                    </td>
                                    <td>{{ $item->quantity_ordered }}</td>
                                    <td>{{ $item->quantity_received }}</td>
                                    <td>
                                        @if($item->quantity_pending > 0)
                                            <span class="text-warning fw-bold">{{ $item->quantity_pending }}</span>
                                        @else
                                            <span class="text-success"><i class="bi bi-check-circle"></i></span>
                                        @endif
                                    </td>
                                    <td>Rs. {{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="fw-bold">Rs. {{ number_format($item->total_cost, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom"><h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Summary</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal:</span><span>Rs. {{ number_format($order->subtotal, 2) }}</span></div>
                @if($order->tax_amount > 0)
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Tax ({{ $order->tax_rate }}%):</span><span>Rs. {{ number_format($order->tax_amount, 2) }}</span></div>
                @endif
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Discount:</span><span class="text-danger">-Rs. {{ number_format($order->discount_amount, 2) }}</span></div>
                @endif
                <hr>
                <div class="d-flex justify-content-between mb-3"><span class="fw-bold">Total:</span><span class="fs-5 fw-bold">Rs. {{ number_format($order->total_amount, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Paid:</span><span class="text-success">Rs. {{ number_format($order->paid_amount, 2) }}</span></div>
                <div class="d-flex justify-content-between"><span class="fw-bold">Balance Due:</span><span class="fs-5 fw-bold {{ $order->balance > 0 ? 'text-danger' : 'text-success' }}">Rs. {{ number_format($order->balance, 2) }}</span></div>
            </div>
        </div>

        @if($order->balance > 0 && !in_array($order->status, ['cancelled']))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom"><h5 class="mb-0"><i class="bi bi-cash me-2"></i>Record Payment</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.purchase-orders.payment', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group"><span class="input-group-text">$</span><input type="number" step="0.01" min="0.01" max="{{ $order->balance }}" name="amount" class="form-control" required></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-cash me-1"></i>Record Payment</button>
                    </form>
                </div>
            </div>
        @endif

        @if($order->payments->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Payment History</h5></div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($order->payments as $payment)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div><span class="fw-medium">Rs. {{ number_format($payment->amount, 2) }}</span><br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} - {{ $payment->payment_date->format('M d, Y') }}</small></div>
                                    <small class="text-muted">{{ $payment->reference_number }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Receive Modal --}}
<div class="modal fade" id="receiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="bi bi-box-arrow-in-down me-2"></i>Receive Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.purchase-orders.receive', $order) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Enter the quantity received for each item. Stock will be updated automatically.</p>
                    @foreach($order->items->where('quantity_pending', '>', 0) as $item)
                        <div class="d-flex align-items-center gap-3 mb-3 p-3 border rounded">
                            <div class="flex-grow-1">
                                <div class="fw-medium">{{ $item->product->name }}</div>
                                <small class="text-muted">Pending: {{ $item->quantity_pending }} | Cost: Rs. {{ number_format($item->unit_cost, 2) }}</small>
                            </div>
                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                            <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control form-control-sm" style="width: 100px;" min="1" max="{{ $item->quantity_pending }}" value="{{ $item->quantity_pending }}" required>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Confirm Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
