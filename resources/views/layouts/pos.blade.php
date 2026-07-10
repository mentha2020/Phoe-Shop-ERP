<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Phone Shop ERP') }} - POS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body { overflow: hidden; height: 100vh; }
        .pos-layout { display: flex; height: 100vh; }
        .pos-products { flex: 1; overflow-y: auto; }
        .pos-cart { width: 380px; background: white; border-left: 1px solid #e2e8f0; display: flex; flex-direction: column; }
        .pos-product-card { cursor: pointer; transition: all 0.2s; }
        .pos-product-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .pos-cart-item { border-bottom: 1px solid #f1f5f9; }
        .pos-keyboard-btn { min-height: 60px; font-size: 1.1rem; }
        @media (max-width: 991.98px) {
            .pos-cart { width: 100%; position: fixed; bottom: 0; left: 0; right: 0; max-height: 50vh; z-index: 1050; border-radius: 1rem 1rem 0 0; box-shadow: 0 -4px 20px rgba(0,0,0,0.15); transform: translateY(100%); transition: transform 0.3s; }
            .pos-cart.show { transform: translateY(0); }
        }
    </style>
</head>
<body>
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            <div class="toast show align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.toast').remove()"></button>
                </div>
            </div>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>
