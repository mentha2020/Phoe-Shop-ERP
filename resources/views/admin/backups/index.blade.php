@extends('layouts.app')

@section('title', 'Backup & Restore')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Backup & Restore</h1>
    </div>

    <div class="row g-4">
        <!-- Create Backup -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-download me-2"></i>Create Backup</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Create a backup of your current database. The backup file will be stored locally.</p>
                    <form action="{{ route('admin.backups.create') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-download me-1"></i>Create Backup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Restore Backup -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Restore Backup</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Upload a backup file to restore your database. This will overwrite the current database.</p>
                    <form action="{{ route('admin.backups.restore') }}" method="POST" enctype="multipart/form-data"
                          onsubmit="return confirm('WARNING: This will overwrite the current database. Are you sure?')">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="backup_file" class="form-control" accept=".sql,.sqlite" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="bi bi-upload me-1"></i>Restore from File
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Backups -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Existing Backups</h5>
        </div>
        <div class="card-body">
            @if(count($backups))
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                        <tr>
                            <td>
                                <i class="bi bi-file-earmark-zip text-primary me-2"></i>
                                <span class="fw-semibold">{{ $backup['filename'] }}</span>
                            </td>
                            <td>{{ number_format($backup['size'] / 1024, 1) }} KB</td>
                            <td>{{ \Carbon\Carbon::fromTimestamp($backup['date'])->format('d M Y H:i:s') }}</td>
                            <td>
                                <a href="{{ route('admin.backups.download', $backup['filename']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <form action="{{ route('admin.backups.delete', $backup['filename']) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Delete this backup?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-hdd text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">No backups yet</h5>
                <p class="text-muted">Create your first backup above.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
