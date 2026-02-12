<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - التقارير المتقدمة</title>

    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- بوتستراب -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- مكتبات التقارير المتقدمة -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/material_blue.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; transition: all 0.3s; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 5px; margin: 0 10px 5px; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .sidebar .nav-link.active { background: #3498db; }
        .main-content { padding: 20px; width: 100%; transition: all 0.3s; }
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .navbar-brand { font-weight: 700; font-size: 1.2rem; }
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 fs-4 fw-bold border-bottom border-secondary">
                <i class="bi bi-graph-up-arrow text-info me-2"></i> التقارير المتقدمة
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="{{ route('reports-v2.index') }}" class="nav-link {{ request()->routeIs('reports-v2.index') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> لوحة التحكم
                </a>
                <a href="{{ route('reports-v2.shipments') }}" class="nav-link {{ request()->routeIs('reports-v2.shipments') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> الشحنات
                </a>
                <a href="{{ route('reports-v2.collections') }}" class="nav-link {{ request()->routeIs('reports-v2.collections') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin me-2"></i> التحصيلات
                </a>
                <a href="{{ route('reports-v2.expenses') }}" class="nav-link {{ request()->routeIs('reports-v2.expenses') ? 'active' : '' }}">
                    <i class="bi bi-wallet2 me-2"></i> المصاريف
                </a>
                <a href="{{ route('reports-v2.treasury') }}" class="nav-link {{ request()->routeIs('reports-v2.treasury') ? 'active' : '' }}">
                    <i class="bi bi-safe me-2"></i> الخزنة
                </a>
                
                
                <hr class="text-white-50 mx-3">
                
                <a href="/admin" class="nav-link text-info">
                    <i class="bi bi-speedometer me-2"></i> لوحة التحكم الرئيسية
                </a>
                
                <a href="{{ route('dashboard') }}" class="nav-link text-secondary small">
                    <i class="bi bi-box-arrow-right me-2"></i> النظام القديم
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 px-4 py-3">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <button class="btn btn-outline-secondary" id="menu-toggle"><i class="bi bi-list"></i></button>
                    
                    <!-- روابط التنقل السريع في النافبار -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3 d-none d-md-flex">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports-v2.index') ? 'active fw-bold text-primary' : '' }}" href="{{ route('reports-v2.index') }}">الرئيسية</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                التقارير
                            </a>
                            <ul class="dropdown-menu border-0 shadow-sm">
                                <li><a class="dropdown-item" href="{{ route('reports-v2.shipments') }}"><i class="bi bi-box-seam me-2 text-primary"></i>الشحنات</a></li>
                                <li><a class="dropdown-item" href="{{ route('reports-v2.collections') }}"><i class="bi bi-cash-coin me-2 text-success"></i>التحصيلات</a></li>
                                <li><a class="dropdown-item" href="{{ route('reports-v2.expenses') }}"><i class="bi bi-wallet2 me-2 text-danger"></i>المصاريف</a></li>
                                <li><a class="dropdown-item" href="{{ route('reports-v2.treasury') }}"><i class="bi bi-safe me-2 text-info"></i>الخزنة</a></li>
                            </ul>
                        </li>
                    </ul>
                        <span class="me-3 fw-bold text-secondary">{{ auth()->user()->name ?? 'المستخدم' }}</span>
                        <div class="dropdown">
                            <button class="btn btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-4"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-left me-2"></i> تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup@1.8.2/dist/countUp.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>

    @stack('scripts')
</body>
</html>
