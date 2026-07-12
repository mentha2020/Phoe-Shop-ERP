@extends('layouts.app')

@section('title', 'Activity Log')

@section('breadcrumb')
    <li class="breadcrumb-item active">Activity Log</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Activity Log</h1>
        <p class="text-muted mb-0">Track all system actions and changes</p>
    </div>
    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#purgeModal">
        <i class="bi bi-trash me-1"></i>Purge Old Logs
    </button>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <form action="{{ route('admin.activity-log') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach($logTypes as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">User</label>
                <select name="causer" class="form-select">
                    <option value="">All Users</option>
                    @foreach($causers as $causer)
                        <option value="{{ $causer->id }}" {{ request('causer') == $causer->id ? 'selected' : '' }}>{{ $causer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">Time</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Subject</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td class="ps-3">
                                <div class="small">{{ $activity->created_at->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ $activity->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                @if($activity->causer)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                            {{ strtoupper(substr($activity->causer->name, 0, 2)) }}
                                        </div>
                                        <span class="fw-medium">{{ $activity->causer->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'auth' => 'info',
                                        'user' => 'primary',
                                        'role' => 'warning',
                                        'system' => 'secondary',
                                    ];
                                    $color = $typeColors[$activity->log_name] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ ucfirst($activity->log_name) }}</span>
                            </td>
                            <td>{{ $activity->description }}</td>
                            <td>
                                @if($activity->subject_type)
                                    <span class="text-muted small">{{ class_basename($activity->subject_type) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($activity->properties['ip']))
                                    <code class="small">{{ $activity->properties['ip'] }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No activity logs found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($activities->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $activities->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Purge Modal --}}
<div class="modal fade" id="purgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Purge Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.activity-log.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>This will permanently delete activity log entries within the selected date range.</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_from" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_to" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This cannot be undone.')">
                        <i class="bi bi-trash me-1"></i>Purge Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
