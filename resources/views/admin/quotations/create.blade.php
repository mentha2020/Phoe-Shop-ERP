@extends('layouts.app')

@section('title', 'New Quotation')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">New Quotation</h1>
        <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotationForm">
        @csrf
        <div class="row g-4">
            <div class="col-md-8">
                <!-- Items -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quotation Items</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                            <i class="bi bi-plus me-1"></i>Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 300px;">Product</th>
                                        <th style="width: 100px;">Qty</th>
                                        <th style="width: 150px;">Unit Price</th>
                                        <th style="width: 120px;">Discount</th>
                                        <th style="width: 120px;">Subtotal</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                </tbody>
                            </table>
                        </div>
                        <div id="noItems" class="text-center text-muted py-4">
                            No items added yet. Click "Add Item" to start.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quotation Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select" required>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valid Until</label>
                            <input type="date" name="valid_until" class="form-control" value="{{ now()->addDays(30)->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount Amount</label>
                            <input type="number" name="discount_amount" class="form-control" value="0" step="0.01" min="0" onchange="calculateTotal()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax Amount</label>
                            <input type="number" name="tax_amount" class="form-control" value="0" step="0.01" min="0" onchange="calculateTotal()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Terms & Conditions</label>
                            <textarea name="terms" class="form-control" rows="3">This quotation is valid for 30 days from the date of issue. Prices are subject to change after the validity period.</textarea>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotalDisplay">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount:</span>
                            <span id="discountDisplay">-$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span id="taxDisplay">$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total:</span>
                            <span id="totalDisplay">$0.00</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mt-3" id="submitBtn">
                    <i class="bi bi-check-circle me-1"></i>Create Quotation
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const products = @json($products);
let itemCount = 0;

function addItem() {
    const tbody = document.getElementById('itemsBody');
    const noItems = document.getElementById('noItems');
    noItems.style.display = 'none';

    const row = document.createElement('tr');
    row.id = `item-${itemCount}`;
    row.innerHTML = `
        <td>
            <select name="items[${itemCount}][product_id]" class="form-select form-select-sm product-select"
                    onchange="updatePrice(${itemCount})" required>
                <option value="">Select product...</option>
                ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price}">${p.name} (${p.sku})</option>`).join('')}
            </select>
            <input type="hidden" name="items[${itemCount}][product_variant_id]" value="">
        </td>
        <td><input type="number" name="items[${itemCount}][quantity]" class="form-control form-control-sm" value="1" min="1" onchange="calculateRow(${itemCount})"></td>
        <td><input type="number" name="items[${itemCount}][unit_price]" class="form-control form-control-sm" value="0" step="0.01" onchange="calculateRow(${itemCount})"></td>
        <td><input type="number" name="items[${itemCount}][discount_amount]" class="form-control form-control-sm" value="0" step="0.01" onchange="calculateRow(${itemCount})"></td>
        <td class="subtotal-cell fw-bold">$0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${itemCount})"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    itemCount++;
}

function updatePrice(index) {
    const select = document.querySelector(`#item-${index} .product-select`);
    const option = select.options[select.selectedIndex];
    const price = option.dataset.price || 0;
    const row = document.getElementById(`item-${index}`);
    row.querySelector('input[name$="[unit_price]"]').value = parseFloat(price).toFixed(2);
    calculateRow(index);
}

function calculateRow(index) {
    const row = document.getElementById(`item-${index}`);
    const qty = parseFloat(row.querySelector('input[name$="[quantity]"]').value) || 0;
    const price = parseFloat(row.querySelector('input[name$="[unit_price]"]').value) || 0;
    const discount = parseFloat(row.querySelector('input[name$="[discount_amount]"]').value) || 0;
    const subtotal = (qty * price) - discount;
    row.querySelector('.subtotal-cell').textContent = '$' + subtotal.toFixed(2);
    calculateTotal();
}

function removeItem(index) {
    document.getElementById(`item-${index}`).remove();
    calculateTotal();
}

function calculateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.subtotal-cell').forEach(cell => {
        subtotal += parseFloat(cell.textContent.replace('$', '')) || 0;
    });
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
    const tax = parseFloat(document.querySelector('input[name="tax_amount"]').value) || 0;
    const total = subtotal - discount + tax;

    document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('discountDisplay').textContent = '-$' + discount.toFixed(2);
    document.getElementById('taxDisplay').textContent = '$' + tax.toFixed(2);
    document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
}

// Add first item
addItem();
</script>
@endpush
