@extends('layouts.app')

@section('title', 'Learning Management System - Enterprise Learning Portal')

@section('content')
<!-- Hero Section -->
<section class="py-5 bg-white border-bottom position-relative overflow-hidden">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-pill small fw-bold mb-3">
                    <i class="bi bi-rocket-takeoff-fill"></i> Modern LMS Engine • Laravel 13 & PostgreSQL 18
                </div>
                <h1 class="display-4 fw-extrabold text-dark tracking-tight mb-3">
                    Empower Your Workforce with <span class="text-primary">Learning Management System</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    Enterprise-ready learning management architecture engineered for high-concurrency intranet environments, hands-on technical curriculum, automated quiz assessments, and verified certifications.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-search me-1"></i> Explore Courses
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-person-plus me-1"></i> Join as Learner
                        </a>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-speedometer2 me-1"></i> My Dashboard
                        </a>
                    @endguest
                </div>
                <div class="d-flex align-items-center gap-4 text-muted small">
                    <div><i class="bi bi-check-circle-fill text-success me-1"></i> 100% Offline Asset Delivery</div>
                    <div><i class="bi bi-check-circle-fill text-success me-1"></i> PostgreSQL JSONB Engine</div>
                    <div><i class="bi bi-check-circle-fill text-success me-1"></i> Verifiable Certificates</div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-lg overflow-hidden p-2 bg-gradient" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary">System Telemetry</span>
                            <span class="badge bg-success"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Online</span>
                        </div>
                        <h4 class="fw-bold mb-1">Architecture Highlights</h4>
                        <p class="small text-light opacity-75 mb-4">Production-grade enterprise features included out of the box.</p>

                        <div class="list-group list-group-flush rounded-3 overflow-hidden text-dark">
                            <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3"><i class="bi bi-database-fill-gear fs-4"></i></div>
                                <div>
                                    <div class="fw-bold small">PostgreSQL 18 Optimized</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">Full JSONB schema mapping for quizzes & bulk imports.</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3"><i class="bi bi-cloud-arrow-up-fill fs-4"></i></div>
                                <div>
                                    <div class="fw-bold small">Admin Bulk Import Data Center</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">Ingest users, courses, quiz banks, and enrollments via CSV.</div>
                                </div>
                            </div>
                            <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3"><i class="bi bi-wifi-off fs-4"></i></div>
                                <div>
                                    <div class="fw-bold small">Air-Gapped & Offline Ready</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">Zero external CDN dependencies; local Bootstrap 5 & FontAwesome.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- System Stats Counter Bar -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row g-3 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="h3 fw-bold text-primary mb-0">{{ number_format($stats['total_students']) }}</div>
                    <div class="text-muted small">Registered Students</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="h3 fw-bold text-success mb-0">{{ number_format($stats['total_courses']) }}</div>
                    <div class="text-muted small">Published Courses</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="h3 fw-bold text-info mb-0">{{ number_format($stats['total_instructors']) }}</div>
                    <div class="text-muted small">Expert Instructors</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="h3 fw-bold text-warning mb-0">{{ number_format($stats['total_enrollments']) }}</div>
                    <div class="text-muted small">Course Enrollments</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Categories Section -->
<section class="py-5">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Explore by Category</h3>
                <p class="text-muted small mb-0">Browse specialized enterprise and development learning paths</p>
            </div>
            <a href="{{ route('courses.index') }}" class="text-decoration-none text-primary fw-semibold small">
                All Categories <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($categories as $cat)
                <div class="col-md-4 col-lg">
                    <a href="{{ route('categories.show', $cat->slug) }}" class="text-decoration-none">
                        <div class="card h-100 p-3 border hover-shadow text-center">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 mx-auto mb-2" style="width: 52px; height: 52px; background-color: {{ $cat->color }}15; color: {{ $cat->color }};">
                                <i class="bi {{ $cat->icon }} fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $cat->name }}</h6>
                            <span class="badge bg-light text-secondary border">{{ $cat->courses_count }} Courses</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
@if($featuredCourses->isNotEmpty())
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-danger mb-2"><i class="bi bi-fire me-1"></i> Featured Masterclasses</span>
                <h3 class="fw-bold mb-1">Top-Rated Engineering Courses</h3>
                <p class="text-muted small mb-0">Carefully curated curriculum with comprehensive quizzes and certifications</p>
            </div>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm">
                View All Courses
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredCourses as $course)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    {{ $course->category?->name ?? 'General' }}
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary badge-level">
                                    {{ str_replace('_', ' ', $course->level) }}
                                </span>
                            </div>

                            <h5 class="card-title fw-bold text-dark mb-2">
                                <a href="{{ route('courses.detail', $course->slug) }}" class="text-dark text-decoration-none">
                                    {{ $course->title }}
                                </a>
                            </h5>

                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($course->summary, 90) }}
                            </p>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $course->instructor?->avatar_url }}" alt="avatar" class="rounded-circle" width="28" height="28">
                                    <small class="text-muted fw-semibold">{{ $course->instructor?->name }}</small>
                                </div>
                                <div class="fw-bold text-primary fs-5">
                                    {{ $course->formatted_price }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Latest Courses Section -->
<section class="py-5">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Recently Added Courses</h3>
                <p class="text-muted small mb-0">Continuous learning modules designed for modern engineers</p>
            </div>
            <a href="{{ route('courses.index') }}" class="text-decoration-none text-primary fw-semibold small">
                Explore Full Library <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($latestCourses as $course)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-dark border">
                                    {{ $course->category?->name ?? 'General' }}
                                </span>
                                <div class="small rating-stars">
                                    <i class="bi bi-star-fill"></i>
                                    <span class="text-dark fw-bold ms-1">{{ $course->average_rating > 0 ? $course->average_rating : '5.0' }}</span>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">
                                <a href="{{ route('courses.detail', $course->slug) }}" class="text-dark text-decoration-none">
                                    {{ $course->title }}
                                </a>
                            </h6>

                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($course->summary, 80) }}
                            </p>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <div class="small text-muted">
                                    <i class="bi bi-clock me-1"></i> {{ $course->formatted_duration }}
                                </div>
                                <div class="fw-bold text-dark">
                                    {{ $course->formatted_price }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Value Proposition Banner -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center py-4">
        <h2 class="fw-extrabold mb-3">Air-Gapped & Internal Intranet Ready</h2>
        <p class="lead text-light opacity-90 col-lg-8 mx-auto mb-4">
            This Learning Management System operates seamlessly in completely isolated corporate networks. With local Bootstrap 5 distribution, bundled icon webfonts, and self-contained asset delivery, you never have to depend on external CDNs.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('courses.index') }}" class="btn btn-light text-primary btn-lg fw-bold px-4">
                <i class="bi bi-book-half me-1"></i> Start Learning Now
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg fw-bold px-4">
                    Create Free Account
                </a>
            @endguest
        </div>
    </div>
</section>
@endsection
