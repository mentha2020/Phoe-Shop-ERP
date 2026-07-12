@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.purchase-orders.index') }}" class="text-decoration-none">Purchase Orders</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Create Purchase Order</h1>
        <p class="text-muted mb-0">Create a new purchase order from a supplier</p>
    </div>
    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form action="{{ route('admin.purchase-orders.store') }}" method="POST" id="poForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom"><h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Order Details</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expected Date</label>
                            <input type="date" name="expected_date" class="form-control" value="{{ old('expected_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" name="tax_rate" class="form-control" value="{{ old('tax_rate', '0') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discount Amount</label>
                            <div class="input-group"><span class="input-group-text">$</span><input type="number" step="0.01" min="0" name="discount_amount" class="form-control" value="{{ old('discount_amount', '0') }}"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Order notes..." value="{{ old('notes') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Order Items</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()"><i class="bi bi-plus me-1"></i>Add Item</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="itemsTable">
                            <thead class="border-bottom">
                                <tr>
                                    <th class="ps-3" style="width:40%">Product</th>
                                    <th>Qty</th>
                                    <th>Unit Cost</th>
                                    <th>Total</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td class="ps-3">
                                        <select name="items[0][product_id]" class="form-select form-select-sm" required onchange="updatePrice(this)">
                                            <option value="">Select Product</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-price="{{ $p->purchase_price }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm" min="1" value="1" required oninput="calcRow(this)"></td>
                                    <td><div class="input-group input-group-sm"><span class="input-group-text">$</span><input type="number" step="0.01" name="items[0][unit_cost]" class="form-control" min="0" value="0.00" required oninput="calcRow(this)"></div></td>
                                    <td class="fw-bold row-total">Rs. 0.00</td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr><td colspan="3" class="text-end fw-bold">Subtotal:</td><td colspan="2" class="fw-bold" id="subtotal">Rs. 0.00</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top:1rem">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Purchase Order</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Create order in <strong>Draft</strong> status</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Approve to confirm with supplier</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Receive items to update stock levels</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Record payments against the order</li>
                    </ul>
                    <hr>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Purchase Order</button>
                        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let itemIndex = 1;
function addItem() {
    const tbody = document.getElementById('itemsBody');
    const row = tbody.querySelector('.item-row').cloneNode(true);
    row.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        if (el.tagName === 'SELECT') el.value = '';
        else if (el.name.includes('quantity')) el.value = '1';
        else el.value = '0.00';
    });
    row.querySelector('.row-total').textContent = 'Rs. 0.00';
    tbody.appendChild(row);
    itemIndex++;
    calcTotal();
}
function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length > 1) { btn.closest('tr').remove(); calcTotal(); }
}
function updatePrice(sel) {
    const opt = sel.options[sel.selectedIndex];
    const price = opt.dataset.price || 0;
    const row = sel.closest('tr');
    row.querySelector('[name*="unit_cost"]').value = parseFloat(price).toFixed(2);
    calcRow(sel);
}
function calcRow(el) {
    const row = el.closest('tr');
    const qty = parseFloat(row.querySelector('[name*="quantity"]').value) || 0;
    const cost = parseFloat(row.querySelector('[name*="unit_cost"]').value) || 0;
    row.querySelector('.row-total').textContent = 'Rs. ' + (qty * cost).toFixed(2);
    calcTotal();
}
function calcTotal() {
    let total = 0;
    document.querySelectorAll('.row-total').forEach(td => { total += parseFloat(td.textContent.replace('Rs. ','')) || 0; });
    document.getElementById('subtotal').textContent = 'Rs. ' + total.toFixed(2);
}
</script>
@endpush
