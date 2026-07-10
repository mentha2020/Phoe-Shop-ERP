@extends('layouts.app')

@section('title', 'Stock Adjustments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.stock.index') }}" class="text-decoration-none">Stock</a></li>
    <li class="breadcrumb-item active">Adjustments</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Stock Adjustments</h1>
        <p class="text-muted mb-0">History of all stock adjustments</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Adjustment
        </a>
        <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Reference</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Created By</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adjustment)
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-light text-dark">{{ $adjustment->reference_number }}</span>
                            </td>
                            <td>{{ $adjustment->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $adjustment->branch->name }}</td>
                            <td>
                                <span class="badge bg-{{ $adjustment->type_badge }}-subtle text-{{ $adjustment->type_badge }}">
                                    {{ ucfirst($adjustment->type) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($adjustment->reason, 30) ?? '-' }}</td>
                            <td>{{ $adjustment->createdBy?->name ?? '-' }}</td>
                            <td class="text-end pe-3">
                                <span class="text-muted small">{{ $adjustment->items->count() }} item(s)</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-sliders fs-1 d-block mb-2"></i>
                                <p class="mb-0">No adjustments found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($adjustments->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $adjustments->links() }}
        </div>
    @endif
</div>
@endsection
