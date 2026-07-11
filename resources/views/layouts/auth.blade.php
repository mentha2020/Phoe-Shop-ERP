<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Phone Shop ERP') }} - {{ $title ?? 'Login' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <style>
        .auth-card .form-control {
            background-color: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }
        .auth-card .form-control:focus {
            background-color: #0f172a;
            border-color: #6366f1;
            color: #e2e8f0;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }
        .auth-card .form-label {
            color: #cbd5e1;
        }
        .auth-card .btn-outline-secondary {
            color: #94a3b8;
            border-color: #334155;
        }
        .auth-card .form-check-label {
            color: #94a3b8;
        }
        .auth-card a.text-primary {
            color: #818cf8 !important;
        }
    </style>
</head>
<body class="bg-dark" style="overflow: hidden;">
    <div class="min-vh-100 d-flex align-items-center justify-content-center p-2">
        <div class="w-100" style="max-width: 350px;">
            {{-- Logo --}}
            <div class="text-center mb-2">
                <a href="/" class="text-decoration-none">
                    @php $authLogo = \App\Models\Setting::get('logo'); @endphp
                    @if($authLogo)
                        <img src="{{ asset('storage/' . $authLogo) }}" alt="Logo" style="max-width: 180px; height: auto;">
                    @else
                        <div class="bg-primary rounded-4 p-3 d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-phone text-white fs-2"></i>
                        </div>
                    @endif
                </a>
            </div>

            {{-- Card --}}
            <div class="card border-0 shadow auth-card" style="background: #1e293b;">
                <div class="card-body p-3">
                    {{-- Flash Messages --}}
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-2">
                <p class="text-light" style="font-size: 0.75rem;">&copy; {{ date('Y') }} PhoneShop ERP. All rights reserved.</p>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
