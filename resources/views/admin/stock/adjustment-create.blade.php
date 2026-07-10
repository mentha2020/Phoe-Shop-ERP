@extends('layouts.app')

@section('title', 'Create Stock Adjustment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.stock-adjustments.index') }}" class="text-decoration-none">Adjustments</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Create Stock Adjustment</h1>
        <p class="text-muted mb-0">Add, remove, or adjust stock quantities</p>
    </div>
    <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<form action="{{ route('admin.stock-adjustments.store') }}" method="POST" id="adjustmentForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Adjustment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="addition" {{ old('type') === 'addition' ? 'selected' : '' }}>Addition (Stock In)</option>
                                <option value="subtraction" {{ old('type') === 'subtraction' ? 'selected' : '' }}>Subtraction (Stock Out)</option>
                                <option value="damage" {{ old('type') === 'damage' ? 'selected' : '' }}>Damage</option>
                                <option value="expired" {{ old('type') === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="lost" {{ old('type') === 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reason</label>
                            <input type="text" name="reason" class="form-control" placeholder="Brief reason" value="{{ old('reason') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Items</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
                        <i class="bi bi-plus me-1"></i>Add Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th>Quantity</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td class="ps-3">
                                        <select name="items[0][product_id]" class="form-select form-select-sm" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku ?? 'N/A' }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control form-control-sm" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Instructions</h6>
                    <ul class="list-unstyled mb-0 small text-muted">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Addition:</strong> Increases stock quantity</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Subtraction:</strong> Decreases stock quantity</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Damage/Expired/Lost:</strong> Decreases stock</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>All changes are logged with before/after quantities</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Each item creates a stock movement record</li>
                    </ul>
                    <hr>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Adjustment
                        </button>
                        <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-light">Cancel</a>
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
    const row = document.querySelector('.item-row').cloneNode(true);
    row.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        if (el.tagName === 'SELECT') el.value = '';
        if (el.tagName === 'INPUT') el.value = '1';
    });
    document.getElementById('itemsBody').appendChild(row);
    itemIndex++;
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('tr').remove();
    }
}
</script>
@endpush
