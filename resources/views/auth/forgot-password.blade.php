@extends('layouts.app')

@section('title', 'Forgot Password - Learning Management System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-key-fill fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Reset Password</h3>
                    <p class="text-muted small">Enter your email and we'll send you a password reset link.</p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success d-flex align-items-center small py-2 mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">Registered Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-send-fill me-1"></i> Send Password Reset Link
                    </button>

                    <div class="text-center small text-muted">
                        Remember your password? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
