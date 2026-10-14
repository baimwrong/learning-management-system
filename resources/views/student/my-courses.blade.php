@extends('layouts.app')

@section('title', 'My Enrolled Courses - Learning Management System')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Enrolled Courses</h3>
            <p class="text-muted small mb-0">Continue learning and achieve course certifications</p>
        </div>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Enroll In Another Course
        </a>
    </div>

    <div class="row g-4">
        @forelse($enrollments as $enr)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <img src="{{ $enr->course->thumbnail_url }}" alt="thumb" class="card-img-top" style="height: 160px; object-fit: cover;">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-light text-dark border">{{ $enr->course->category?->name ?? 'Course' }}</span>
                            <span class="badge bg-{{ $enr->status === 'completed' ? 'success' : 'primary' }}">
                                {{ ucfirst($enr->status) }}
                            </span>
                        </div>

                        <h5 class="fw-bold text-dark mb-2">
                            <a href="{{ route('student.learn', $enr->course->slug) }}" class="text-dark text-decoration-none">
                                {{ $enr->course->title }}
                            </a>
                        </h5>

                        <div class="small text-muted mb-3">
                            <i class="bi bi-person me-1"></i> {{ $enr->course->instructor?->name }}
                        </div>

                        <div class="mt-auto pt-3 border-top">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Progress</span>
                                <span class="fw-bold text-dark">{{ $enr->progress_percentage }}%</span>
                            </div>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar bg-{{ $enr->progress_percentage == 100 ? 'success' : 'primary' }}" style="width: {{ $enr->progress_percentage }}%"></div>
                            </div>

                            <a href="{{ route('student.learn', $enr->course->slug) }}" class="btn btn-{{ $enr->status === 'completed' ? 'outline-success' : 'primary' }} btn-sm w-100 fw-semibold">
                                <i class="bi bi-play-circle-fill me-1"></i>
                                {{ $enr->status === 'completed' ? 'Review Course Material' : 'Continue Course' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-3 border">
                    <i class="bi bi-journal-plus fs-1 text-muted mb-3 d-block"></i>
                    <h4>You have not enrolled in any courses yet</h4>
                    <p class="text-muted small">Explore our extensive technical catalog to begin your training.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary btn-sm">Browse Catalog</a>
                </div>
            </div>
        @endforelse
    </div>

    @if($enrollments->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $enrollments->links() }}
        </div>
    @endif
</div>
@endsection
