<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Instructor Studio - Learning Management System')</title>

    <!-- Offline Local CSS Assets -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        :root {
            --inst-sidebar-width: 250px;
            --inst-sidebar-bg: #14532d;
            --inst-primary: #16a34a;
            --inst-bg: #f8fafc;
        }

        body {
            background-color: var(--inst-bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
        }

        #sidebar {
            width: var(--inst-sidebar-width);
            min-height: 100vh;
            background-color: #064e3b;
            color: #a7f3d0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        #content {
            margin-left: var(--inst-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 20px 24px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            border-bottom: 1px solid #047857;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 11px 24px;
            color: #d1fae5;
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

        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: #047857;
            border-left-color: #34d399;
        }

        .topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--inst-sidebar-width));
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
    <nav id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-easel-fill text-warning"></i>
            <div>
                <div style="font-size: 0.95rem; line-height: 1.2;">Learning Management</div>
                <div style="font-size: 0.7rem; color: #a7f3d0; font-weight: 600;">INSTRUCTOR STUDIO</div>
            </div>
        </div>

        <div class="px-3 py-2 text-uppercase text-light opacity-50 fw-bold" style="font-size: 0.7rem;">Teaching</div>
        <a href="{{ route('instructor.dashboard') }}" class="sidebar-link {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('instructor.courses.index') }}" class="sidebar-link {{ request()->routeIs('instructor.courses.*') ? 'active' : '' }}">
            <i class="bi bi-journal-richtext"></i> My Courses
        </a>
        <a href="{{ route('instructor.courses.create') }}" class="sidebar-link {{ request()->routeIs('instructor.courses.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Create New Course
        </a>
        <a href="{{ route('instructor.students') }}" class="sidebar-link {{ request()->routeIs('instructor.students') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Enrolled Students
        </a>

        <div class="px-3 py-2 text-uppercase text-light opacity-50 fw-bold mt-3" style="font-size: 0.7rem;">Navigation</div>
        <a href="{{ route('student.dashboard') }}" class="sidebar-link">
            <i class="bi bi-person-workspace"></i> Student Portal
        </a>
        <a href="{{ route('home') }}" class="sidebar-link">
            <i class="bi bi-box-arrow-up-right"></i> Public Website
        </a>
    </nav>

    <div id="content">
        <header class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="fw-bold text-dark fs-5">@yield('page-title', 'Instructor Studio')</div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('instructor.courses.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> New Course
                </a>

                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="rounded-circle" width="28" height="28">
                        <span class="fw-semibold">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                        <li><a class="dropdown-item" href="{{ route('home') }}">View Website</a></li>
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

        <main class="p-4 flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
