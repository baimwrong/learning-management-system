@extends('layouts.app')

@section('title', 'Sign Up - Learning Management System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-sm p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-plus fs-3"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Create Account</h3>
                    <p class="text-muted small">Join thousands of students and engineers on Learning Management System</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input id="name" type="text" class="form-control border-start-0 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input id="email" type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimum 8 characters">
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password-confirm" class="form-label fw-semibold small">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Repeat password">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Account Role</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="roleStudent" value="student" checked>
                                <label class="form-check-label small" for="roleStudent">
                                    <i class="bi bi-book me-1 text-primary"></i> Student (I want to learn)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="roleInstructor" value="instructor" {{ old('role') === 'instructor' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="roleInstructor">
                                    <i class="bi bi-mortarboard me-1 text-success"></i> Instructor (I want to teach)
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-check-lg me-1"></i> Register Account
                    </button>

                    <div class="text-center small text-muted">
                        Already have an account? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
