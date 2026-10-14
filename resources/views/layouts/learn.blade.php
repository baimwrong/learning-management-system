<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Learning Portal - Learning Management System')</title>

    <!-- Offline Local CSS Assets -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .player-header {
            background-color: #0f172a;
            color: #ffffff;
            height: 60px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #1e293b;
            flex-shrink: 0;
        }

        .player-container {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
        }

        .player-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 30px;
            background-color: #ffffff;
        }

        .player-sidebar {
            width: 380px;
            flex-shrink: 0;
            background-color: #ffffff;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .curriculum-list {
            overflow-y: auto;
            flex-grow: 1;
        }

        .lesson-item {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            color: #334155;
            text-decoration: none;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.15s ease;
            font-size: 0.9rem;
        }

        .lesson-item:hover {
            background-color: #f8fafc;
            color: #2563eb;
        }

        .lesson-item.active {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
            border-left: 4px solid #2563eb;
        }

        .lesson-item.completed .check-circle {
            color: #10b981;
        }

        .check-circle {
            color: #cbd5e1;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .module-header {
            background-color: #f1f5f9;
            padding: 12px 18px;
            font-weight: 700;
            font-size: 0.85rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        @media (max-width: 991.98px) {
            .player-container {
                flex-direction: column;
            }
            .player-sidebar {
                width: 100%;
                height: 300px;
                border-left: none;
                border-top: 1px solid #e2e8f0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('player-body')

    <!-- Offline Local JS Assets -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
