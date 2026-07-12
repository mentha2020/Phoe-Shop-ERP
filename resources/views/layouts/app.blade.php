<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Phone Shop ERP'))</title>
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
                @php $sidebarLogo = \App\Models\Setting::get('logo'); @endphp
                @if($sidebarLogo)
                    <img src="{{ asset('storage/' . $sidebarLogo) }}" alt="Logo" class="brand-logo">
                @else
                    <div class="bg-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-phone text-white fs-5"></i>
                    </div>
                @endif
        </div>

        <div class="sidebar-menu" x-data="{ openGroup: '{{ match(true) {
            request()->routeIs('admin.pos.*', 'admin.sales.*', 'admin.quotations.*') => 'pos',
            request()->routeIs('admin.products.*', 'admin.stock.*', 'admin.stock-transfers.*', 'admin.stock-adjustments.*') => 'inventory',
            request()->routeIs('admin.purchase-orders.*', 'admin.suppliers.*') => 'purchasing',
            request()->routeIs('admin.repairs.*') => 'repairs',
            request()->routeIs('admin.accounting.*', 'admin.expenses.*') => 'finance',
            request()->routeIs('admin.reports.*') => 'reports',
            request()->routeIs('admin.customers.*', 'admin.brands.*', 'admin.categories.*', 'admin.branches.*') => 'master',
            request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.activity-log') => 'admin',
            default => ''
        } }}' }">
            <div class="menu-item">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            @can('pos.view')
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'pos' ? '' : 'pos'" class="menu-header w-100 text-start">
                    <span>Point of Sale</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'pos' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'pos'" x-transition>
                <li class="menu-item">
                    <a href="{{ route('admin.pos.index') }}" class="{{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i>
                        <span>POS</span>
                    </a>
                </li>
                @can('sales.view')
                <li class="menu-item">
                    <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Sales</span>
                    </a>
                </li>
                @endcan
                <li class="menu-item">
                    <a href="{{ route('admin.quotations.index') }}" class="{{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Quotations</span>
                    </a>
                </li>
                </ul>
            </div>
            @endcan

            @canany(['products.view', 'inventory.view'])
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'inventory' ? '' : 'inventory'" class="menu-header w-100 text-start">
                    <span>Inventory</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'inventory' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'inventory'" x-transition>
                @can('products.view')
                <li class="menu-item">
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Products</span>
                    </a>
                </li>
                @endcan
                @can('inventory.view')
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
                @endcan
                </ul>
            </div>
            @endcanany

            @can('purchase.view')
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'purchasing' ? '' : 'purchasing'" class="menu-header w-100 text-start">
                    <span>Purchasing</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'purchasing' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'purchasing'" x-transition>
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
            </div>
            @endcan

            @can('repairs.view')
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'repairs' ? '' : 'repairs'" class="menu-header w-100 text-start">
                    <span>Repairs</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'repairs' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'repairs'" x-transition>
                <li class="menu-item">
                    <a href="{{ route('admin.repairs.index') }}" class="{{ request()->routeIs('admin.repairs.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i>
                        <span>Repair Jobs</span>
                    </a>
                </li>
                </ul>
            </div>
            @endcan

            @canany(['accounting.view', 'expenses.view'])
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'finance' ? '' : 'finance'" class="menu-header w-100 text-start">
                    <span>Finance</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'finance' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'finance'" x-transition>
                @can('accounting.view')
                <li class="menu-item">
                    <a href="{{ route('admin.accounting.index') }}" class="{{ request()->routeIs('admin.accounting.*') ? 'active' : '' }}">
                        <i class="bi bi-calculator"></i>
                        <span>Accounting</span>
                    </a>
                </li>
                @endcan
                @can('expenses.view')
                <li class="menu-item">
                    <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Expenses</span>
                    </a>
                </li>
                @endcan
                </ul>
            </div>
            @endcanany

            @canany(['reports.view-sales', 'reports.view-purchases', 'reports.view-inventory', 'reports.view-financial', 'reports.view-customers', 'reports.view-suppliers', 'reports.view-repairs'])
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'reports' ? '' : 'reports'" class="menu-header w-100 text-start">
                    <span>Reports</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'reports' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'reports'" x-transition>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.sales') }}" class="{{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Sales</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.purchases') }}" class="{{ request()->routeIs('admin.reports.purchases') ? 'active' : '' }}">
                        <i class="bi bi-bag-check"></i>
                        <span>Purchases</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.inventory') }}" class="{{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Inventory</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.customers') }}" class="{{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.suppliers') }}" class="{{ request()->routeIs('admin.reports.suppliers') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i>
                        <span>Suppliers</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.expenses') }}" class="{{ request()->routeIs('admin.reports.expenses') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Expenses</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.repairs') }}" class="{{ request()->routeIs('admin.reports.repairs') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i>
                        <span>Repairs</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.stock_movements') }}" class="{{ request()->routeIs('admin.reports.stock_movements') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Stock Movements</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.users') }}" class="{{ request()->routeIs('admin.reports.users') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i>
                        <span>User Performance</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.reports.financial') }}" class="{{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}">
                        <i class="bi bi-calculator"></i>
                        <span>Financial</span>
                    </a>
                </li>
                </ul>
            </div>
            @endcanany

            @canany(['customers.view', 'brands.view', 'categories.view', 'branches.view'])
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'master' ? '' : 'master'" class="menu-header w-100 text-start">
                    <span>Master Data</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'master' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'master'" x-transition>
                @can('customers.view')
                <li class="menu-item">
                    <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Customers</span>
                    </a>
                </li>
                @endcan
                @can('brands.view')
                <li class="menu-item">
                    <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="bi bi-bookmark"></i>
                        <span>Brands</span>
                    </a>
                </li>
                @endcan
                @can('categories.view')
                <li class="menu-item">
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                    </a>
                </li>
                @endcan
                @can('branches.view')
                <li class="menu-item">
                    <a href="{{ route('admin.branches.index') }}" class="{{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Branches</span>
                    </a>
                </li>
                @endcan
                </ul>
            </div>
            @endcanany

            @can('users.view')
            <div class="menu-group">
                <button @click="openGroup = openGroup === 'admin' ? '' : 'admin'" class="menu-header w-100 text-start">
                    <span>Administration</span>
                    <span class="menu-chevron"><i class="bi bi-chevron-down" :class="openGroup === 'admin' ? 'rotate-open' : 'rotate-closed'"></i></span>
                </button>
                <ul class="list-unstyled mb-0" x-show="openGroup === 'admin'" x-transition>
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
                    <a href="{{ route('admin.activity-log') }}" class="{{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Activity Log</span>
                    </a>
                </li>
                </ul>
            </div>
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
                <button class="btn btn-link position-relative me-2" onclick="toggleTheme()" title="Toggle Theme">
                    <i class="bi bi-moon-stars fs-5 d-none" id="themeIconLight"></i>
                    <i class="bi bi-sun fs-5" id="themeIconDark"></i>
                </button>

                {{-- Fullscreen --}}
                <button class="btn btn-link position-relative me-2" onclick="toggleFullScreen()" title="Fullscreen">
                    <i class="bi bi-arrows-fullscreen fs-5"></i>
                </button>

                {{-- Notifications --}}
                <div class="dropdown me-2">
                    <button class="btn btn-link position-relative" data-bs-toggle="dropdown">
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
                    <button class="btn btn-link text-decoration-none d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown">
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
                            <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                <i class="bi bi-person me-2"></i>My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
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
