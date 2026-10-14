<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Console - Learning Management System')</title>

    <!-- Offline Local CSS Assets -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        :root {
            --admin-sidebar-width: 260px;
            --admin-sidebar-bg: #0f172a;
            --admin-sidebar-color: #94a3b8;
            --admin-sidebar-hover: #38bdf8;
            --admin-primary: #2563eb;
            --admin-bg: #f1f5f9;
        }

        body {
            background-color: var(--admin-bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
        }

        #sidebar {
            width: var(--admin-sidebar-width);
            min-height: 100vh;
            background-color: var(--admin-sidebar-bg);
            color: var(--admin-sidebar-color);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        #content {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 20px 24px;
            font-size: 1.2rem;
            font-weight: 700;
            color: #ffffff;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand i {
            color: #38bdf8;
            font-size: 1.4rem;
        }

        .nav-category {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 18px 24px 6px;
            font-weight: 700;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 24px;
            color: var(--admin-sidebar-color);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.15s ease-in-out;
        }

        .sidebar-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: #1e293b;
            border-left-color: var(--admin-sidebar-hover);
        }

        .sidebar-link.active {
            color: #ffffff;
            background-color: #1e293b;
            border-left-color: #38bdf8;
            font-weight: 600;
        }

        .admin-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .stat-card {
            border-radius: 12px;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--admin-sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Admin Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-mortarboard-fill"></i>
            <div>
                <div style="font-size: 0.95rem; line-height: 1.2;">Learning Management</div>
                <div style="font-size: 0.7rem; color: #38bdf8; font-weight: 600;">SYSTEM ADMIN</div>
            </div>
        </div>

        <div class="nav-category">Main Dashboard</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Overview
        </a>

        <div class="nav-category">Data & Ingestion</div>
        <a href="{{ route('admin.bulk-import.index') }}" class="sidebar-link {{ request()->routeIs('admin.bulk-import.*') ? 'active' : '' }}" style="color: #38bdf8; font-weight: 600;">
            <i class="bi bi-cloud-arrow-up-fill text-info"></i> Bulk Import Center
        </a>

        <div class="nav-category">Academic Management</div>
        <a href="{{ route('admin.courses.index') }}" class="sidebar-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <i class="bi bi-journal-album"></i> Courses Catalog
        </a>
        <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Categories
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <i class="bi bi-star"></i> Reviews Moderation
        </a>

        <div class="nav-category">Access & People</div>
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users & Roles
        </a>

        <div class="nav-category">System & Setup</div>
        <a href="{{ route('admin.system') }}" class="sidebar-link {{ request()->routeIs('admin.system') ? 'active' : '' }}">
            <i class="bi bi-database-check"></i> PostgreSQL 18 Engine
        </a>
        <a href="{{ route('home') }}" class="sidebar-link">
            <i class="bi bi-box-arrow-up-right"></i> View Public Site
        </a>
    </nav>

    <!-- Admin Content Area -->
    <div id="content">
        <!-- Top Navbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="fw-bold text-dark fs-5">@yield('page-title', 'Administration Console')</div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-md-inline">
                    <i class="bi bi-database me-1"></i> PostgreSQL 18 Ready
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle d-none d-md-inline">
                    <i class="bi bi-wifi-off me-1"></i> Local Offline Intranet
                </span>

                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="rounded-circle" width="28" height="28">
                        <span class="fw-semibold">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                        <li><a class="dropdown-item" href="{{ route('student.dashboard') }}"><i class="bi bi-person me-2"></i>Student Portal</a></li>
                        <li><a class="dropdown-item" href="{{ route('instructor.dashboard') }}"><i class="bi bi-mortarboard me-2"></i>Instructor Studio</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-4 flex-grow-1">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-x-circle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Offline Local JS Assets -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    @stack('scripts')
</body>
</html>
