@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-4">Settings</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i>General
                </a>
                <a href="{{ route('admin.settings.notifications') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.notifications') ? 'active' : '' }}">
                    <i class="bi bi-bell me-2"></i>Notifications
                </a>
                <a href="{{ route('admin.settings.profile') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.profile') ? 'active' : '' }}">
                    <i class="bi bi-person me-2"></i>My Profile
                </a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">General Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf @method('PUT')

                        <div class="row g-3">
                            @foreach($settings as $group => $items)
                                <div class="col-12">
                                    <h6 class="text-muted text-uppercase mb-3">{{ ucfirst($group) }}</h6>
                                </div>
                                @foreach($items as $item)
                                <div class="col-md-6">
                                    <label class="form-label">{{ ucwords(str_replace('_', ' ', $item->key)) }}</label>
                                    @if($item->type === 'boolean')
                                        <select name="settings[{{ $item->key }}]" class="form-select">
                                            <option value="1" {{ $item->value === '1' ? 'selected' : '' }}>Enabled</option>
                                            <option value="0" {{ $item->value === '0' ? 'selected' : '' }}>Disabled</option>
                                        </select>
                                    @elseif($item->type === 'textarea')
                                        <textarea name="settings[{{ $item->key }}]" class="form-control" rows="3">{{ $item->value }}</textarea>
                                    @else
                                        <input type="{{ $item->type }}" name="settings[{{ $item->key }}]" class="form-control" value="{{ $item->value }}">
                                    @endif
                                </div>
                                @endforeach
                            @endforeach

                            @if(count($settings) === 0)
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" name="settings[company_name]" class="form-control" value="{{ Setting::get('company_name', config('app.name')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Company Email</label>
                                        <input type="email" name="settings[company_email]" class="form-control" value="{{ Setting::get('company_email') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Company Phone</label>
                                        <input type="text" name="settings[company_phone]" class="form-control" value="{{ Setting::get('company_phone') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Currency</label>
                                        <input type="text" name="settings[currency]" class="form-control" value="{{ Setting::get('currency', 'USD') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Company Address</label>
                                        <textarea name="settings[company_address]" class="form-control" rows="2">{{ Setting::get('company_address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
