<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 Session Expired - Learning Management System</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <style>
        body {
            background-color: #f8fafc;
            color: #334155;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .error-card {
            max-width: 540px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            padding: 2.5rem;
            text-align: center;
        }
        .error-code {
            font-size: 5rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -2px;
            color: #0284c7;
        }
        .brand-badge {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="error-card mx-auto">
                    <a href="{{ url('/') }}" class="brand-badge">
                        <i class="bi bi-mortarboard"></i>
                        <span>Learning Management System</span>
                    </a>
                    <div class="error-code mb-3">419</div>
                    <h3 class="fw-bold mb-2">Page Security Session Expired</h3>
                    <p class="text-muted mb-4">
                        Your secure verification token timed out due to inactivity. Please refresh the page to obtain a fresh session token and continue.
                    </p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button onclick="window.location.reload()" class="btn btn-primary px-4">
                            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Page
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Log In Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
