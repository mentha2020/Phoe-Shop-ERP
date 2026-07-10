<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Phone Shop ERP') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="{{ request()->cookie('sidebar_collapsed') ? 'sidebar-collapsed' : '' }}">
    {{-- Sidebar Overlay (Mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-phone text-white fs-5"></i>
                </div>
                <span class="brand-text ms-2">PhoneShop ERP</span>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-header">Main</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>

            <div class="menu-header">Point of Sale</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.pos.index') }}" class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i>
                        <span>POS</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Sales</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.quotations.index') }}" class="{{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Quotations</span>
                    </a>
                </li>
            </ul>

            <div class="menu-header">Inventory</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.stock.index') }}" class="{{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
                        <i class="bi bi-stack"></i>
                        <span>Stock</span>
                        @if(isset($lowStockCount) && $lowStockCount > 0)
                            <span class="menu-badge">{{ $lowStockCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.stock-transfers.index') }}" class="{{ request()->routeIs('admin.stock-transfers.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Transfers</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.stock-adjustments.index') }}" class="{{ request()->routeIs('admin.stock-adjustments.*') ? 'active' : '' }}">
                        <i class="bi bi-sliders"></i>
                        <span>Adjustments</span>
                    </a>
                </li>
            </ul>

            <div class="menu-header">Purchasing</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.purchase-orders.index') }}" class="{{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}">
                        <i class="bi bi-bag-check"></i>
                        <span>Purchase Orders</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i>
                        <span>Suppliers</span>
                    </a>
                </li>
            </ul>

            <div class="menu-header">Repairs</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.repairs.index') }}" class="{{ request()->routeIs('admin.repairs.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i>
                        <span>Repair Jobs</span>
                    </a>
                </li>
            </ul>

            <div class="menu-header">Finance</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.accounting.index') }}" class="{{ request()->routeIs('admin.accounting.*') ? 'active' : '' }}">
                        <i class="bi bi-calculator"></i>
                        <span>Accounting</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Expenses</span>
                    </a>
                </li>
            </ul>

            <div class="menu-header">Reports</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.reports.sales') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Reports</span>
                    </a>
                </li>
            </ul>

            <div class="menu-header">Master Data</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="bi bi-bookmark"></i>
                        <span>Brands</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
            </ul>

            @can('manage-users')
            <div class="menu-header">Administration</div>
            <ul class="list-unstyled mb-0">
                <li class="menu-item">
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock"></i>
                        <span>Roles & Permissions</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.branches.index') }}" class="{{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Branches</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.activity-log') }}" class="{{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Activity Log</span>
                    </a>
                </li>
            </ul>
            @endcan
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Top Navbar --}}
        <nav class="navbar-top">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark d-lg-none me-2" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <button class="btn btn-link text-dark d-none d-lg-flex me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-5"></i>
                </button>

                {{-- Global Search --}}
                <div class="position-relative d-none d-md-block">
                    <form action="{{ route('admin.search') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="search" name="q" class="form-control bg-light border-0 ps-0" placeholder="Search products, customers, orders..." style="width: 300px;" value="{{ request('q') }}">
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex align-items-center ms-auto">
                {{-- Theme Toggle --}}
                <button class="btn btn-link text-dark position-relative me-2" onclick="toggleTheme()" title="Toggle Theme">
                    <i class="bi bi-moon-stars fs-5 d-none" id="themeIconLight"></i>
                    <i class="bi bi-sun fs-5" id="themeIconDark"></i>
                </button>

                {{-- Notifications --}}
                <div class="dropdown me-2">
                    <button class="btn btn-link text-dark position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="display: none;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <span>Notifications</span>
                            <a href="#" class="text-primary text-decoration-none small">Mark all read</a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div id="notificationList">
                            <div class="dropdown-item text-center text-muted py-3">
                                No new notifications
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Menu --}}
                <div class="dropdown">
                    <button class="btn btn-link text-dark text-decoration-none d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="avatar-sm me-2">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle" width="32" height="32" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <span class="d-none d-md-inline fw-medium">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <div class="p-4">
            {{-- Breadcrumb --}}
            @hasSection('breadcrumb')
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"><i class="bi bi-house"></i></a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
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

    {{-- Toast Container --}}
    <div id="toast-container"></div>

    @stack('scripts')
</body>
</html>
