@extends('layouts.app')

@section('title', 'Repair ' . $repair->ticket_number)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <span class="badge {{ $repair->status_badge_class }} me-2">{{ $repair->status_label }}</span>
                {{ $repair->ticket_number }}
            </h1>
            <small class="text-muted">Created {{ $repair->created_at->format('d M Y H:i') }} by {{ $repair->creator->name }}</small>
        </div>
        <a href="{{ route('admin.repairs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <!-- Device Info -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Device Information</h5>
                    <span class="badge bg-secondary">{{ $repair->device_type }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <strong>Brand:</strong> {{ $repair->device_brand }}
                        </div>
                        <div class="col-md-4">
                            <strong>Model:</strong> {{ $repair->device_model }}
                        </div>
                        <div class="col-md-4">
                            <strong>Serial:</strong> {{ $repair->device_serial ?? '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Password:</strong> {{ $repair->device_password ?? '-' }}
                        </div>
                        <div class="col-md-12">
                            <strong>Issue:</strong>
                            <p class="mt-1 mb-0">{{ $repair->issue_description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnosis & Resolution -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Diagnosis & Resolution</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.repairs.update', $repair->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="3" placeholder="Enter diagnosis...">{{ $repair->diagnosis }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Resolution</label>
                            <textarea name="resolution" class="form-control" rows="3" placeholder="Enter resolution...">{{ $repair->resolution }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Save Notes</button>
                    </form>
                </div>
            </div>

            <!-- Parts Used -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Parts Used</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPartModal">
                        <i class="bi bi-plus me-1"></i>Add Part
                    </button>
                </div>
                <div class="card-body">
                    @if($repair->parts->count())
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Part</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">Selling Price</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repair->parts as $part)
                                <tr>
                                    <td>{{ $part->part_name }}</td>
                                    <td class="text-center">{{ $part->quantity }}</td>
                                    <td class="text-end">Rs. {{ number_format($part->unit_cost, 2) }}</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($part->selling_price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('admin.repairs.part.remove', $part->id) }}" method="POST"
                                              onsubmit="return confirm('Remove this part?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm text-danger p-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end fw-bold">Parts Total:</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($repair->parts_total, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-3 mb-0">No parts added yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status Update -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Update Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.repairs.status', $repair->id) }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2">
                            @if($repair->status === 'received')
                                <button name="status" value="diagnosed" class="btn btn-warning">Mark as Diagnosed</button>
                            @endif
                            @if(in_array($repair->status, ['received', 'diagnosed']))
                                <button name="status" value="in_progress" class="btn btn-primary">Start Repair</button>
                            @endif
                            @if(in_array($repair->status, ['in_progress', 'received', 'diagnosed']))
                                <button name="status" value="waiting_parts" class="btn btn-secondary">Waiting for Parts</button>
                            @endif
                            @if(in_array($repair->status, ['in_progress', 'waiting_parts']))
                                <button name="status" value="completed" class="btn btn-success">Mark Completed</button>
                            @endif
                            @if($repair->status === 'completed')
                                <button name="status" value="delivered" class="btn btn-dark">Mark as Delivered</button>
                            @endif
                            @if(!in_array($repair->status, ['delivered', 'cancelled']))
                                <button name="status" value="cancelled" class="btn btn-outline-danger">Cancel Repair</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assignment -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Customer</td><td>{{ $repair->customer->name ?? 'Walk-in' }}</td></tr>
                        <tr><td class="text-muted">Branch</td><td>{{ $repair->branch->name }}</td></tr>
                        <tr><td class="text-muted">Assigned To</td><td>{{ $repair->assignedTo->name ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Est. Completion</td><td>{{ $repair->estimated_completion ? \Carbon\Carbon::parse($repair->estimated_completion)->format('d M Y') : '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <!-- Cost Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Cost Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Estimated:</span><span>Rs. {{ number_format($repair->estimated_cost, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Parts:</span><span>Rs. {{ number_format($repair->parts_total, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Final Cost:</span><span class="fw-bold">Rs. {{ number_format($repair->final_cost, 2) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2"><span>Deposit:</span><span class="text-success">Rs. {{ number_format($repair->deposit_amount, 2) }}</span></div>
                    <div class="d-flex justify-content-between fw-bold"><span>Balance:</span><span class="{{ $repair->balance > 0 ? 'text-danger' : 'text-success' }}">Rs. {{ number_format($repair->balance, 2) }}</span></div>
                    @if($repair->balance > 0 && !in_array($repair->status, ['delivered', 'cancelled']))
                    <form action="{{ route('admin.repairs.deposit', $repair->id) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="deposit_amount" class="form-control" step="0.01" min="0.01" placeholder="Amount">
                            <button type="submit" class="btn btn-success">Record</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Part Modal -->
<div class="modal fade" id="addPartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.repairs.part.add', $repair->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Part Name <span class="text-danger">*</span></label>
                        <input type="text" name="part_name" class="form-control" required placeholder="e.g., Screen, Battery">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit Cost</label>
                        <input type="number" name="unit_cost" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" name="selling_price" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Part</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
