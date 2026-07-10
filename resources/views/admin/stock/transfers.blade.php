@extends('layouts.app')

@section('title', 'Stock Transfers')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.stock.index') }}" class="text-decoration-none">Stock</a></li>
    <li class="breadcrumb-item active">Transfers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Stock Transfers</h1>
        <p class="text-muted mb-0">Transfer stock between branches</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock-transfers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Transfer
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
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Created By</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-light text-dark">{{ $transfer->reference_number }}</span>
                            </td>
                            <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $transfer->fromBranch->name }}</td>
                            <td>{{ $transfer->toBranch->name }}</td>
                            <td>
                                <span class="badge bg-{{ $transfer->status_badge }}-subtle text-{{ $transfer->status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                                </span>
                            </td>
                            <td>{{ $transfer->items->count() }} item(s)</td>
                            <td>{{ $transfer->createdBy?->name ?? '-' }}</td>
                            <td class="text-end pe-3">
                                @if($transfer->status === 'in_transit')
                                    <form action="{{ route('admin.stock-transfers.receive', $transfer) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm receipt of this transfer? Stock will be updated.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Receive">
                                            <i class="bi bi-check-circle me-1"></i>Receive
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-arrow-left-right fs-1 d-block mb-2"></i>
                                <p class="mb-0">No transfers found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transfers->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $transfers->links() }}
        </div>
    @endif
</div>
@endsection
