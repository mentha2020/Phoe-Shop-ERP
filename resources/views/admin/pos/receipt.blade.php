@extends('layouts.pos')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Sale Completed!</h4>
                </div>
                <div class="card-body" id="receipt-content">
                    <div class="text-center mb-3">
                        @php $receiptLogo = \App\Models\Setting::get('logo'); @endphp
                        @if($receiptLogo)
                            <img src="{{ asset('storage/' . $receiptLogo) }}" alt="Logo" style="max-height: 80px; margin-bottom: 10px;">
                        @endif
                        <h5>{{ config('app.name', 'Phone Shop ERP') }}</h5>
                        <small class="text-muted">{{ $sale->branch->address ?? '' }}</small><br>
                        <small class="text-muted">Tel: {{ $sale->branch->phone ?? '' }}</small>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Invoice:</strong>
                        <span>{{ $sale->invoice_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Date:</strong>
                        <span>{{ $sale->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Cashier:</strong>
                        <span>{{ $sale->creator->name }}</span>
                    </div>
                    @if($sale->customer)
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Customer:</strong>
                        <span>{{ $sale->customer->name }}</span>
                    </div>
                    @endif
                    <hr>

                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            <tr>
                                <td>
                                    <div>{{ $item->product_name }}</div>
                                    <small class="text-muted">{{ $item->product_sku }}</small>
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>

                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal:</span>
                        <span>Rs. {{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    @if($sale->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-1 text-success">
                        <span>Discount:</span>
                        <span>-Rs. {{ number_format($sale->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($sale->tax_amount > 0)
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tax:</span>
                        <span>Rs. {{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total:</span>
                        <span>Rs. {{ number_format($sale->total, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-1">
                        <span>Payment Method:</span>
                        <span class="text-uppercase">{{ $sale->payment_method }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Amount Paid:</span>
                        <span>Rs. {{ number_format($sale->paid_amount, 2) }}</span>
                    </div>
                    @if($sale->change_amount > 0)
                    <div class="d-flex justify-content-between mb-1 text-success">
                        <span>Change:</span>
                        <span>Rs. {{ number_format($sale->change_amount, 2) }}</span>
                    </div>
                    @endif

                    <hr>
                    <div class="text-center">
                        <small class="text-muted">Thank you for your purchase!</small><br>
                        <small class="text-muted">{{ now()->format('d M Y H:i:s') }}</small>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i>Print Receipt
                        </button>
                        <a href="{{ route('admin.pos.index') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>New Sale
                        </a>
                        <a href="{{ route('admin.pos.history') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-clock-history me-1"></i>Sales History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body { overflow: auto !important; height: auto !important; }
@media print {
    body * { visibility: hidden; }
    #receipt-content, #receipt-content * { visibility: visible; }
    #receipt-content { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; }
    .card-footer { display: none !important; }
}
</style>
@endsection
