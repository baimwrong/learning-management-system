@extends('layouts.app')

@section('title', 'Student Dashboard - Learning Management System')

@section('content')
<div class="container py-4">
    <!-- Welcome Header -->
    <div class="card border-0 shadow-sm p-4 mb-4 bg-white">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="rounded-circle border" width="60" height="60">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Welcome back, {{ auth()->user()->name }}!</h4>
                        <p class="text-muted small mb-0">Track your curriculum progress, active coursework, and credentials</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-search me-1"></i> Browse More Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Enrolled</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['enrolled_count'] }}</h3>
                        <small class="text-muted">Total Courses</small>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 fs-3">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">In Progress</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['in_progress_count'] }}</h3>
                        <small class="text-info">Ongoing Learning</small>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-3 fs-3">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Completed</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['completed_count'] }}</h3>
                        <small class="text-success">100% Finished</small>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 fs-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Certificates</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['certificates_count'] }}</h3>
                        <small class="text-warning">Earned Badges</small>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 fs-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Courses In Progress -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Continue Learning</h5>
            <a href="{{ route('student.my-courses') }}" class="small text-decoration-none">View All My Courses</a>
        </div>

        <div class="row g-4">
            @forelse($activeCourses as $enr)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden">
                        <img src="{{ $enr->course->thumbnail_url }}" alt="thumb" class="card-img-top" style="height: 140px; object-fit: cover;">
                        <div class="card-body p-4 d-flex flex-column">
                            <span class="badge bg-light text-dark border align-self-start mb-2">
                                {{ $enr->course->category?->name ?? 'Course' }}
                            </span>
                            <h6 class="fw-bold text-dark mb-2">{{ $enr->course->title }}</h6>

                            <div class="mt-auto pt-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Course Progress</span>
                                    <span class="fw-bold text-dark">{{ $enr->progress_percentage }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $enr->progress_percentage }}%"></div>
                                </div>
                                <a href="{{ route('student.learn', $enr->course->slug) }}" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-play-circle-fill me-1"></i> Resume Course
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="p-4 bg-white rounded-3 border text-center">
                        <p class="text-muted small mb-2">You don't have any ongoing courses at the moment.</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary btn-sm">Explore Course Catalog</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Earned Certificates -->
    @if($certificates->isNotEmpty())
        <div>
            <h5 class="fw-bold mb-3">Earned Certifications</h5>
            <div class="row g-3">
                @foreach($certificates as $cert)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm p-4 bg-white">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle">
                                    <i class="bi bi-patch-check-fill fs-2"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $cert->course->title }}</h6>
                                    <small class="text-muted">Issued on {{ $cert->issued_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="font-monospace small text-primary fw-bold">{{ $cert->certificate_code }}</span>
                                <a href="{{ route('certificates.show', $cert) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="bi bi-printer me-1"></i> View Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
