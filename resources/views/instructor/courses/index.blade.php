@extends('layouts.instructor')

@section('title', 'My Courses - Instructor Studio')
@section('page-title', 'My Authoring Courses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Created Courses</h5>
        <p class="text-muted small mb-0">Build, modify, and monitor course curriculum</p>
    </div>
    <a href="{{ route('instructor.courses.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Author New Course
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Course</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Price</th>
                    <th>Enrollments</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($courses as $course)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $course->thumbnail_url }}" alt="thumb" class="rounded-2" width="48" height="34" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $course->title }}</div>
                                    <small class="text-muted">{{ $course->modules_count }} Modules • {{ $course->total_lessons_count }} Lessons</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $course->category?->name ?? 'General' }}</span></td>
                        <td><span class="badge bg-secondary-subtle text-secondary badge-level">{{ str_replace('_', ' ', $course->level) }}</span></td>
                        <td class="fw-bold text-primary">{{ $course->formatted_price }}</td>
                        <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $course->enrollments_count }} Students</span></td>
                        <td>
                            <span class="badge bg-{{ $course->is_published ? 'success' : 'secondary' }}">
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('instructor.courses.curriculum', $course) }}" class="btn btn-outline-success btn-sm me-1" title="Curriculum Builder">
                                <i class="bi bi-list-nested me-1"></i> Curriculum
                            </a>
                            <a href="{{ route('instructor.courses.edit', $course) }}" class="btn btn-outline-secondary btn-sm me-1" title="Edit Course Details">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('courses.detail', $course->slug) }}" class="btn btn-outline-primary btn-sm" target="_blank" title="Preview Public Page">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-plus fs-1 d-block mb-2 text-muted"></i>
                            You have not created any courses yet. Click "Author New Course" above!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($courses->hasPages())
        <div class="card-footer bg-white py-3 d-flex justify-content-center">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
