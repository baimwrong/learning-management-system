@extends('layouts.app')

@section('title', 'Sign In - Learning Management System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-box-arrow-in-right fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Welcome Back</h3>
                    <p class="text-muted small">Sign in to your Learning Management System portal</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-semibold small mb-0">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input id="password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" name="password" required placeholder="••••••••">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">Remember me on this device</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </button>

                    <div class="text-center small text-muted">
                        Don't have an account? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">Sign Up</a>
                    </div>
                </form>

                @if(app()->isLocal())
                <!-- 1-Click Demo Login Shortcuts -->
                <div class="mt-4 pt-3 border-top">
                    <div class="text-center small fw-bold text-uppercase text-secondary mb-2" style="font-size: 0.75rem;">
                        <i class="bi bi-lightning-charge-fill text-warning"></i> One-Click Demo Accounts
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('auth.quick-login', 'admin') }}" class="btn btn-outline-danger btn-sm text-start d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-shield-lock-fill me-2"></i><strong>Admin</strong> (admin@lms.local)</span>
                            <span class="badge bg-danger">Full Control</span>
                        </a>
                        <a href="{{ route('auth.quick-login', 'instructor') }}" class="btn btn-outline-success btn-sm text-start d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-mortarboard-fill me-2"></i><strong>Instructor</strong> (instructor@lms.local)</span>
                            <span class="badge bg-success">Teacher</span>
                        </a>
                        <a href="{{ route('auth.quick-login', 'student') }}" class="btn btn-outline-primary btn-sm text-start d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-fill me-2"></i><strong>Student</strong> (student@lms.local)</span>
                            <span class="badge bg-primary">Learner</span>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
