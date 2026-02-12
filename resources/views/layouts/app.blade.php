
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Animate CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'نظام إدارة الشحنات') }}</title>

    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- بوتستراب -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    
    <!-- الأنماط المخصصة -->
    <style>
    
@media print {
    nav, .sidebar, .navbar, .btn, .actions, .pagination {
        display: none !important;
    }
}
#menuButton {
    display: none;
}
/* إخفاء السايد بار على الشاشات الصغيرة */
@media (max-width: 1050px) {
    .sidebar {
        position: fixed !important;
        width: 250px !important;
        top: 0;
        left: 0;
        height: 100vh;
        background-color: #343a40;
        z-index: 1050;
        transition: transform 0.3s ease;
        transform: translateX(-100%); /* يبدأ مخفي */
        display: block; /* خلي العرض دائم */
    }
    .sidebar.show {
        transform: translateX(0); /* يظهر لما يضاف كلاس show */
    }
    #menuButton {
        display: inline-block; /* يظهر لما تكون الشاشة أصغر من 1050 */
    }
}

/* إظهار السايد بار على الشاشات الكبيرة */
@media (min-width: 1050px) {
    .sidebar {
        display: block; /* إظهار السايد بار */
    }

    .menu-icon {
        display: none; /* إخفاء الأيقونة على الكمبيوتر */
    }
}


#layout {
    display: flex;
    transition: all 0.3s ease-in-out;
}

.sidebar {
    max-width: 300px;
        position: relative;

    width: 100%;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #343a40;
    min-height: 100vh;
}

.sidebar.collapsed {
    width: 50px;
}

.sidebar.collapsed ul,
.sidebar.collapsed .nav-link span,
.sidebar.collapsed .sidebar-header h5 {
    display: none;
}

.sidebar-header button {
    color: white;
    transition: color 0.3s;
}

.sidebar.collapsed .sidebar-header button {
    color: #212529;
}


.main-content {
    transition: all 0.3s ease-in-out;
    margin-left: 0;
    padding: 20px;
    width: 100%;
}

#toggleSidebarBtn {
    background: transparent;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: color 0.3s;
}

#toggleSidebarBtn:hover {
    color: #ffc107;
}







        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }
       
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: #007bff;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .status-delivered {
            background-color: rgba(40, 167, 69, 0.2);
        }
        .status-returned {
            background-color: rgba(220, 53, 69, 0.2);
        }
        .status-custody {
            background-color: rgba(0, 123, 255, 0.2);
        }
    </style>
    
</head>
<body>


          
@php
    $userRole = auth()->check() ? auth()->user()->role : null;
@endphp


@if(auth()->check())
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- يمين: اللوجو واسم الشركة -->
        <div class="d-flex align-items-center">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="/logo.png" alt="Logo" width="30" height="30" class="me-2">
                إدارة مكتب
            </a>
        </div>


@if($userRole == 'admin' || $userRole == 'accountant')
    <button class="btn btn-outline-dark me-2" id="menuButton">
        <i class="bi bi-list fs-4"></i> <!-- أيقونة القائمة -->
    </button>
@endif



        <!-- شمال: اسم المستخدم وزر تسجيل الخروج -->
        <div class="d-flex align-items-center">
            <span class="me-3">مرحبًا، {{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-left"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </div>
</nav>
@endif

@if(auth()->check() && auth()->user()->expires_at && now()->diffInHours(auth()->user()->expires_at, false) <= 3 && now()->lt(auth()->user()->expires_at))
    <div id="expiry-warning" class="alert alert-warning text-center fixed-top m-0" style="z-index: 9999; font-size: 16px;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        حسابك سينتهي خلال {{ now()->diffForHumans(auth()->user()->expires_at, ['parts' => 2]) }}.
        الرجاء التواصل مع الإدارة.
    </div>

    <!-- صوت تنبيهي -->
    <audio id="alertSound" autoplay>
        <source src="{{ asset('sounds/alert.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- تنبيه منبثق لمرة واحدة فقط -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!sessionStorage.getItem('expiryPopupShown')) {
                alert('⚠️ تنبيه: حسابك سينتهي خلال ساعات قليلة.\nيرجى التواصل مع الإدارة لتجديده.');
                sessionStorage.setItem('expiryPopupShown', '1');
            }

            // تأخير بسيط لتشغيل الصوت على بعض الأجهزة
            const audio = document.getElementById('alertSound');
            if (audio) {
                setTimeout(() => audio.play().catch(() => {}), 100);
            }
        });
    </script>
@endif

<!-- مسافة علشان الـ Navbar ما يغطيش المحتوى -->
<div style="height: 70px;"></div>



    <div class="container-fluid">
        <!-- زر فتح القائمة في الهاتف -->
        <div class="row">
 @auth
@php $userRole = auth()->user()->roles->first()?->name ?? null; @endphp

@if (!in_array(auth()->user()->role, ['shipping_agent', 'moderator']))
<div class="d-flex" id="layout">

<div class="sidebar" id="sidebar">

    <div class="sidebar-header d-flex justify-content-end p-2">
        <button id="toggleSidebarBtn" onclick="toggleSidebar()">
            <i id="toggleIcon" class="bi bi-chevron-double-left"></i>
        </button>
    </div>
    
    <div class="position-sticky pt-3">

        <div class="text-center mb-4">
        </div>
        <ul class="nav flex-column">
            
            @can('dashboard.view')
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>لوحة التحكم</a></li>
            @endcan

            @can('shipping_companies.view_any')
                <li class="nav-item"><a class="nav-link" href="{{ route('shipping-companies.index') }}"><i class="bi bi-building me-2"></i>شركات الشحن</a></li>
            @endcan

            @can('shipments.view_any')
                <li class="nav-item">
                    <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#shipmentsMenu" role="button" aria-expanded="false" aria-controls="shipmentsMenu">
                        <span><i class="bi bi-box-seam me-2"></i> الشحنات</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>
                    <div class="collapse {{ request()->is('shipments*') || request()->is('shipment-statuses*') ? 'show' : '' }}" id="shipmentsMenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('shipments.*') ? 'active' : '' }}" href="{{ route('shipments.index') }}">كل الشحنات</a>
                            </li>
                            @can('shipment_statuses.view_any')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('shipment-statuses.*') ? 'active' : '' }}" href="{{ route('shipment-statuses.index') }}">حالات الشحنة</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endcan

            @can('products.view_any')
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-bag me-2"></i>المنتجات</a></li>
            @endcan

            @can('inventories.view_any')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('inventories.*') ? 'active' : '' }}" href="{{ route('inventories.index') }}">
                        <i class="bi bi-boxes me-2"></i>المخازن
                    </a>
                </li>
            @endcan

            @can('delivery_agents.view_any')
                <li class="nav-item"><a class="nav-link" href="{{ route('delivery-agents.index') }}"><i class="bi bi-person-badge me-2"></i>المندوبين</a></li>
            @endcan

            {{-- الحسابات والتقارير --}}
            @canany(['accounting.view_treasury', 'reports.view_index'])
                @can('accounting.view_treasury')
                    <li class="nav-item"><a class="nav-link" href="{{ route('accounting.index') }}"><i class="bi bi-cash-stack me-2"></i>الحسابات</a></li>
                @endcan
                @can('reports.view_index')
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}"><i class="bi bi-bar-chart-line me-2"></i>التقارير</a></li>
                @endcan
            @endcanany

            @can('users.view_any')
                <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}"><i class="bi bi-people me-2"></i>المستخدمين</a></li>
            @endcan

            @can('roles.view_any')
                <li class="nav-item"><a class="nav-link" href="{{ route('filament.admin.resources.roles.index') }}"><i class="bi bi-shield-lock me-2"></i>الأدوار</a></li>
            @endcan

            @can('settings.view_any')
                <li class="nav-item"><a class="nav-link" href="{{ route('settings.index') }}"><i class="bi bi-gear me-2"></i>الإعدادات</a></li>
            @endcan

            @can('backup.view_any')
                <li class="nav-item"><a class="nav-link" href="{{ route('backup.index') }}"><i class="bi bi-cloud-arrow-down me-2"></i> النسخ الاحتياطي</a></li>
            @endcan
        </ul>
        
    </div>
    

</div>

@endif
@endauth

            <!-- المحتوى الرئيسي -->
            <main class="main-content" id="mainContent">
                <!-- شريط التنقل العلوي -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        @yield('actions')
                    </div>
                </div>

                <!-- رسائل النجاح والخطأ -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- المحتوى -->
                @yield('content')
            </main>
            
            </div>
        </div>
    </div>

    <!-- جافاسكريبت -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    @stack('scripts')

    @yield('scripts')
<script>
function toggleDesktopSidebar() {
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('toggleIcon');

    sidebar.classList.toggle('collapsed');

    // تغيير الأيقونة
    icon.classList.toggle('bi-chevron-double-left');
    icon.classList.toggle('bi-chevron-double-right');
}
</script>

<audio id="success-sound" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
<script>

    
    
    // إظهار أو إخفاء السايد بار عند الضغط على الأيقونة
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('toggleIcon');

    console.log("toggleSidebar called");

    if (!sidebar) {
        console.error("Sidebar element not found!");
        return;
    }
    
    if (window.innerWidth <= 1050) {
        sidebar.classList.toggle('show');
        console.log("Sidebar 'show' toggled. Current classes:", sidebar.className);
    } else {
        if (!icon) {
            console.error("Toggle icon element not found!");
            return;
        }
        sidebar.classList.toggle('collapsed');
        icon.classList.toggle('bi-chevron-double-left');
        icon.classList.toggle('bi-chevron-double-right');
        console.log("Sidebar collapsed toggled. Current classes:", sidebar.className);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const menuButton = document.getElementById('menuButton');
    if (menuButton) {
        menuButton.addEventListener('click', function() {
            console.log("menuButton clicked");
            toggleSidebar();
        });
    } else {
        console.error("menuButton element not found!");
    }
});



</script>

</html>
