@extends('layouts.app')

@section('title', $category->name . ' Courses - Learning Management System')

@section('content')
<div class="container py-4">
    <div class="p-4 p-md-5 mb-4 rounded-3 text-white" style="background: linear-gradient(135deg, {{ $category->color }} 0%, #0f172a 100%);">
        <div class="col-md-8">
            <span class="badge bg-white text-dark mb-2"><i class="bi {{ $category->icon }} me-1"></i> Specialization</span>
            <h1 class="display-5 fw-bold mb-2">{{ $category->name }}</h1>
            <p class="lead mb-0 opacity-90">{{ $category->description }}</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Courses in {{ $category->name }} ({{ $courses->total() }})</h4>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary btn-sm">All Courses</a>
    </div>

    <div class="row g-4">
        @forelse($courses as $course)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-secondary-subtle text-secondary badge-level">
                                {{ str_replace('_', ' ', $course->level) }}
                            </span>
                            <div class="small rating-stars">
                                <i class="bi bi-star-fill"></i>
                                <span class="text-dark fw-bold ms-1">{{ $course->average_rating > 0 ? $course->average_rating : '5.0' }}</span>
                            </div>
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
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-3 border">
                    <h5>No Courses in this Category Yet</h5>
                    <p class="text-muted">New courses are added regularly. Check back soon!</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $courses->links() }}
    </div>
</div>
@endsection
