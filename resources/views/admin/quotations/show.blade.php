@extends('layouts.app')

@section('title', 'Quotation ' . $quotation->quotation_number)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Quotation {{ $quotation->quotation_number }}</h1>
            <small class="text-muted">Created {{ $quotation->created_at->format('d M Y H:i') }} by {{ $quotation->creator->name }}</small>
        </div>
        <div class="d-flex gap-2">
            @if(in_array($quotation->status, ['draft', 'sent']))
            <form action="{{ route('admin.quotations.status', $quotation->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="sent">
                <button type="submit" class="btn btn-info">
                    <i class="bi bi-send me-1"></i>Mark as Sent
                </button>
            </form>
            @endif

            @if($quotation->status === 'sent')
            <form action="{{ route('admin.quotations.status', $quotation->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="accepted">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>Accept
                </button>
            </form>
            <form action="{{ route('admin.quotations.status', $quotation->id) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i>Reject
                </button>
            </form>
            @endif

            @if($quotation->status === 'accepted')
            <form action="{{ route('admin.quotations.convert', $quotation->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Convert this quotation to a sale? Stock will be deducted.')">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle me-1"></i>Convert to Sale
                </button>
            </form>
            @endif

            @if(!in_array($quotation->status, ['converted']))
            <form action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Delete this quotation?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </form>
            @endif

            <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Items</h5>
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
                                @foreach($quotation->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product_name }}</div>
                                        <small class="text-muted">{{ $item->product_sku }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">Rs. {{ number_format($item->discount_amount, 2) }}</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($quotation->notes)
            <div class="card mt-4">
                <div class="card-header"><h5 class="mb-0">Notes</h5></div>
                <div class="card-body"><p class="mb-0">{{ $quotation->notes }}</p></div>
            </div>
            @endif

            @if($quotation->terms)
            <div class="card mt-4">
                <div class="card-header"><h5 class="mb-0">Terms & Conditions</h5></div>
                <div class="card-body"><p class="mb-0">{{ $quotation->terms }}</p></div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Info</h5></div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Status</td><td>
                            @php
                                $badgeClass = match($quotation->status) {
                                    'draft' => 'bg-secondary',
                                    'sent' => 'bg-info',
                                    'accepted' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'expired' => 'bg-warning text-dark',
                                    'converted' => 'bg-primary',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($quotation->status) }}</span>
                        </td></tr>
                        <tr><td class="text-muted">Customer</td><td>{{ $quotation->customer->name ?? 'Walk-in' }}</td></tr>
                        <tr><td class="text-muted">Branch</td><td>{{ $quotation->branch->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Valid Until</td><td>{{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') : '-' }}</td></tr>
                        @if($quotation->convertedSale)
                        <tr><td class="text-muted">Converted Sale</td><td><a href="{{ route('admin.sales.show', $quotation->converted_sale_id) }}">{{ $quotation->convertedSale->invoice_number }}</a></td></tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal:</span><span>Rs. {{ number_format($quotation->subtotal, 2) }}</span></div>
                    @if($quotation->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success"><span>Discount:</span><span>-Rs. {{ number_format($quotation->discount_amount, 2) }}</span></div>
                    @endif
                    @if($quotation->tax_amount > 0)
                    <div class="d-flex justify-content-between mb-2"><span>Tax:</span><span>Rs. {{ number_format($quotation->tax_amount, 2) }}</span></div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Total:</span><span>Rs. {{ number_format($quotation->total, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
