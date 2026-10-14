<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Learning Management System - Enterprise Learning Platform')</title>

    <!-- Offline Local CSS Assets -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        :root {
            --ol-primary: #2563eb;
            --ol-primary-hover: #1d4ed8;
            --ol-dark: #0f172a;
            --ol-surface: #ffffff;
            --ol-bg: #f8fafc;
            --ol-border: #e2e8f0;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--ol-bg);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--ol-dark) !important;
        }

        .navbar-brand .brand-badge {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-right: 6px;
        }

        .nav-link {
            font-weight: 500;
            color: #475569;
            transition: color 0.15s ease-in-out;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--ol-primary);
        }

        .card {
            border: 1px solid var(--ol-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .btn-primary {
            background-color: var(--ol-primary);
            border-color: var(--ol-primary);
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 18px;
        }

        .btn-primary:hover {
            background-color: var(--ol-primary-hover);
            border-color: var(--ol-primary-hover);
        }

        .btn-outline-primary {
            border-color: var(--ol-primary);
            color: var(--ol-primary);
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-outline-primary:hover {
            background-color: var(--ol-primary);
            color: white;
        }

        .badge-level {
            font-size: 0.75rem;
            padding: 5px 9px;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .rating-stars {
            color: #f59e0b;
        }

        .footer {
            margin-top: auto;
            background-color: #0f172a;
            color: #94a3b8;
            border-top: 1px solid #1e293b;
        }

        .quick-role-bar {
            background-color: #1e293b;
            color: #cbd5e1;
            font-size: 0.8rem;
            padding: 6px 0;
            border-bottom: 1px solid #334155;
        }

        .quick-role-bar a {
            color: #93c5fd;
            text-decoration: none;
            margin: 0 4px;
            padding: 2px 8px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.08);
            transition: background 0.15s ease;
        }

        .quick-role-bar a:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Local Demo Role Switcher Bar -->
    <div class="quick-role-bar">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="bi bi-shield-check me-1 text-warning"></i>
                <span class="d-none d-sm-inline">Demo Local Intranet Mode:</span>
                Switch Role:
                <a href="{{ route('auth.quick-login', 'admin') }}"><i class="bi bi-person-fill-gear"></i> Admin</a>
                <a href="{{ route('auth.quick-login', 'instructor') }}"><i class="bi bi-mortarboard-fill"></i> Instructor</a>
                <a href="{{ route('auth.quick-login', 'student') }}"><i class="bi bi-person-workspace"></i> Student</a>
            </div>
            <div>
                <span class="badge bg-secondary">PostgreSQL 18</span>
                <span class="badge bg-primary">Laravel 13</span>
                <span class="badge bg-success"><i class="bi bi-wifi-off"></i> Offline Intranet Ready</span>
            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="brand-badge"><i class="bi bi-mortarboard"></i></span>
                <span>Learning Management System</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">Browse Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('certificates.verify') ? 'active' : '' }}" href="{{ route('certificates.verify') }}">Verify Certificate</a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-center gap-2">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-speedometer2 me-1"></i> Admin Console
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->isInstructor() || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('instructor.dashboard') }}" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-easel me-1"></i> Instructor Studio
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-journal-bookmark me-1"></i> My Learning
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="rounded-circle" width="34" height="34">
                                <span class="d-none d-md-inline fw-semibold">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li class="dropdown-header">
                                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                                    <small class="text-muted">{{ auth()->user()->email }} ({{ ucfirst(auth()->user()->role) }})</small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('student.dashboard') }}"><i class="bi bi-grid me-2 text-primary"></i>Student Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('student.my-courses') }}"><i class="bi bi-book me-2 text-success"></i>My Enrolled Courses</a></li>
                                <li><a class="dropdown-item" href="{{ route('student.certificates') }}"><i class="bi bi-award me-2 text-warning"></i>Certificates</a></li>
                                
                                @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}"><i class="bi bi-gear-wide-connected me-2"></i>Admin Dashboard</a></li>
                                    <li><a class="dropdown-item text-danger" href="{{ route('admin.bulk-import.index') }}"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Bulk Import Center</a></li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-secondary"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-dark fw-semibold">Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Session Feedback Alerts -->
    <div class="container mt-3">
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

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main View Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="brand-badge"><i class="bi bi-mortarboard"></i></span>
                        <span class="h5 mb-0 text-white fw-bold">Learning Management System</span>
                    </div>
                    <p class="small text-slate-400">
                        Production-grade enterprise Learning Management System engineered with Laravel 13, PostgreSQL 18, and modern offline-capable UI architectures.
                    </p>
                    <div class="d-flex gap-2 text-white">
                        <span class="badge bg-dark border border-secondary"><i class="bi bi-hdd-network"></i> Air-Gapped Ready</span>
                        <span class="badge bg-dark border border-secondary"><i class="bi bi-shield-check"></i> Enterprise Auth</span>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">Platform</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('courses.index') }}" class="text-decoration-none text-light opacity-75">All Courses</a></li>
                        <li class="mb-2"><a href="{{ route('certificates.verify') }}" class="text-decoration-none text-light opacity-75">Certificate Verification</a></li>
                        <li class="mb-2"><a href="{{ route('student.dashboard') }}" class="text-decoration-none text-light opacity-75">Student Portal</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Admin & Staff</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-light opacity-75">Admin Control Panel</a></li>
                        <li class="mb-2"><a href="{{ route('admin.bulk-import.index') }}" class="text-decoration-none text-light opacity-75">Bulk Import Center</a></li>
                        <li class="mb-2"><a href="{{ route('instructor.dashboard') }}" class="text-decoration-none text-light opacity-75">Instructor Studio</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-3">System Environment</h6>
                    <p class="small text-light opacity-75 mb-1"><i class="bi bi-database me-1 text-primary"></i> Engine: PostgreSQL 18</p>
                    <p class="small text-light opacity-75 mb-1"><i class="bi bi-cpu me-1 text-danger"></i> Framework: Laravel 13</p>
                    <p class="small text-light opacity-75 mb-0"><i class="bi bi-palette me-1 text-warning"></i> UI: Bootstrap 5.3 + Local Icons</p>
                </div>
            </div>

            <hr class="my-4 border-secondary opacity-25">
            <div class="d-flex flex-wrap justify-content-between align-items-center small text-light opacity-50">
                <div>&copy; {{ date('Y') }} Learning Management System. Open, robust, enterprise learning software.</div>
                <div>Internal Intranet Local Release</div>
            </div>
        </div>
    </footer>

    <!-- Offline Local JS Assets -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
