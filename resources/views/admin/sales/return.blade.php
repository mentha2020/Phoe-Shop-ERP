@extends('layouts.app')

@section('title', 'Process Return - ' . $sale->invoice_number)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Process Return for {{ $sale->invoice_number }}</h1>
        <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <form action="{{ route('admin.sales.return-store', $sale->id) }}" method="POST" id="returnForm">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Select Items to Return</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" class="form-check-input" id="selectAll" onclick="toggleAll(this)">
                                        </th>
                                        <th>Product</th>
                                        <th class="text-center">Qty Sold</th>
                                        <th class="text-center">Return Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Return Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input item-check"
                                                   name="items[{{ $index }}][sale_item_id]" value="{{ $item->id }}"
                                                   data-price="{{ $item->unit_price }}" data-max="{{ $item->quantity }}"
                                                   onclick="toggleItem(this)" required>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $item->product_name }}</div>
                                            <small class="text-muted">{{ $item->product_sku }}</small>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">
                                            <input type="number" name="items[{{ $index }}][quantity]"
                                                   class="form-control form-control-sm text-center qty-input"
                                                   value="0" min="0" max="{{ $item->quantity }}"
                                                   style="width: 80px; display: inline-block;"
                                                   onchange="calculateReturn()" disabled>
                                        </td>
                                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end return-amount">$0.00</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-bold">Reason for Return <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control" required placeholder="Enter reason for return...">
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-bold">Additional Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">Total Return Amount: </span>
                            <span class="fw-bold fs-5" id="totalReturn">$0.00</span>
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg" id="submitBtn" disabled>
                            <i class="bi bi-arrow-return-left me-1"></i>Process Return
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Original Sale</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-muted">Invoice</td>
                            <td>{{ $sale->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date</td>
                            <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Customer</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total</td>
                            <td class="fw-bold">${{ number_format($sale->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Paid</td>
                            <td>${{ number_format($sale->paid_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAll(checkbox) {
    document.querySelectorAll('.item-check').forEach(cb => {
        cb.checked = checkbox.checked;
        toggleItem(cb);
    });
    calculateReturn();
}

function toggleItem(checkbox) {
    const row = checkbox.closest('tr');
    const qtyInput = row.querySelector('.qty-input');
    if (checkbox.checked) {
        qtyInput.disabled = false;
        qtyInput.max = checkbox.dataset.max;
        qtyInput.value = 1;
    } else {
        qtyInput.disabled = true;
        qtyInput.value = 0;
    }
    calculateReturn();
}

function calculateReturn() {
    let total = 0;
    document.querySelectorAll('.item-check:checked').forEach(cb => {
        const row = cb.closest('tr');
        const price = parseFloat(cb.dataset.price);
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const amount = price * qty;
        row.querySelector('.return-amount').textContent = '$' + amount.toFixed(2);
        total += amount;
    });
    document.getElementById('totalReturn').textContent = '$' + total.toFixed(2);
    document.getElementById('submitBtn').disabled = total <= 0;
}
</script>
@endsection
