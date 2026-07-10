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
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center p-4">
        <div class="w-100" style="max-width: 440px;">
            {{-- Logo --}}
            <div class="text-center mb-4">
                <a href="/" class="text-decoration-none">
                    <div class="bg-primary rounded-4 p-3 d-inline-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-phone text-white fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">PhoneShop ERP</h4>
                    <p class="text-muted small">Enterprise Resource Planning</p>
                </a>
            </div>

            {{-- Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
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
            <div class="text-center mt-4">
                <p class="text-muted small">&copy; {{ date('Y') }} PhoneShop ERP. All rights reserved.</p>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
