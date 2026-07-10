@extends('layouts.app')

@section('title', 'Notification Settings')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Settings</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-gear me-2"></i>General
                </a>
                <a href="{{ route('admin.settings.notifications') }}" class="list-group-item list-group-item-action active">
                    <i class="bi bi-bell me-2"></i>Notifications
                </a>
                <a href="{{ route('admin.settings.profile') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-person me-2"></i>My Profile
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Notification Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.notifications.update') }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="email_notifications" value="0">
                                    <input type="checkbox" name="email_notifications" value="1" class="form-check-input"
                                           {{ \App\Models\Setting::get('email_notifications', '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label">Email Notifications</label>
                                </div>
                                <small class="text-muted">Send notifications via email</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="low_stock_alert" value="0">
                                    <input type="checkbox" name="low_stock_alert" value="1" class="form-check-input"
                                           {{ \App\Models\Setting::get('low_stock_alert', '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label">Low Stock Alerts</label>
                                </div>
                                <small class="text-muted">Alert when products are below minimum stock</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="new_order_alert" value="0">
                                    <input type="checkbox" name="new_order_alert" value="1" class="form-check-input"
                                           {{ \App\Models\Setting::get('new_order_alert', '1') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label">New Order Alerts</label>
                                </div>
                                <small class="text-muted">Notify when new purchase orders are created</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="daily_report" value="0">
                                    <input type="checkbox" name="daily_report" value="1" class="form-check-input"
                                           {{ \App\Models\Setting::get('daily_report', '0') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label">Daily Reports</label>
                                </div>
                                <small class="text-muted">Receive daily sales summary</small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Notifications -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">User Notification Preferences</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
