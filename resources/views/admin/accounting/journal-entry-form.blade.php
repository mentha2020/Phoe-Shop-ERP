@extends('layouts.app')

@section('title', 'New Journal Entry')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">New Journal Entry</h1>
        <a href="{{ route('admin.accounting.journal-entries') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <form action="{{ route('admin.accounting.journal-entries.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Entry Items</h5>
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
                            <table class="table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 120px;">Account Code</th>
                                        <th>Account Name</th>
                                        <th style="width: 200px;">Description</th>
                                        <th style="width: 130px;">Bank Account</th>
                                        <th style="width: 130px;">Debit</th>
                                        <th style="width: 130px;">Credit</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
                            <i class="bi bi-plus me-1"></i>Add Row
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Entry Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="general">General</option>
                                <option value="payment">Payment</option>
                                <option value="receipt">Receipt</option>
                                <option value="journal">Journal</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Debit:</span>
                            <span id="totalDebit" class="fw-bold">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Credit:</span>
                            <span id="totalCredit" class="fw-bold">$0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Difference:</span>
                            <span id="difference" class="fw-bold">$0.00</span>
                        </div>
                        <div id="balanceStatus" class="mt-2 text-center"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                    <i class="bi bi-check-circle me-1"></i>Create Entry
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const bankAccounts = @json($bankAccounts);
let itemCount = 0;

function addItem() {
    const tbody = document.getElementById('itemsBody');
    const row = document.createElement('tr');
    row.id = `item-${itemCount}`;
    row.innerHTML = `
        <td><input type="text" name="items[${itemCount}][account_code]" class="form-control form-control-sm" placeholder="e.g., 1001" required></td>
        <td><input type="text" name="items[${itemCount}][account_name]" class="form-control form-control-sm" placeholder="Cash, Sales, etc." required></td>
        <td><input type="text" name="items[${itemCount}][description]" class="form-control form-control-sm" placeholder="Optional"></td>
        <td>
            <select name="items[${itemCount}][bank_account_id]" class="form-select form-select-sm">
                <option value="">None</option>
                ${bankAccounts.map(a => `<option value="${a.id}">${a.name}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" name="items[${itemCount}][debit]" class="form-control form-control-sm debit-input" value="0" step="0.01" min="0" onchange="calculateTotals()"></td>
        <td><input type="number" name="items[${itemCount}][credit]" class="form-control form-control-sm credit-input" value="0" step="0.01" min="0" onchange="calculateTotals()"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${itemCount})"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);
    itemCount++;
}

function removeItem(index) {
    document.getElementById(`item-${index}`).remove();
    calculateTotals();
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });
    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebit').textContent = '$' + totalDebit.toFixed(2);
    document.getElementById('totalCredit').textContent = '$' + totalCredit.toFixed(2);
    const diff = Math.abs(totalDebit - totalCredit);
    document.getElementById('difference').textContent = '$' + diff.toFixed(2);

    const statusEl = document.getElementById('balanceStatus');
    const submitBtn = document.getElementById('submitBtn');
    if (totalDebit > 0 && totalCredit > 0 && diff < 0.01) {
        statusEl.innerHTML = '<span class="badge bg-success">Balanced</span>';
        submitBtn.disabled = false;
    } else {
        statusEl.innerHTML = '<span class="badge bg-danger">Not Balanced</span>';
        submitBtn.disabled = true;
    }
}

// Add initial rows
addItem();
addItem();
</script>
@endpush
