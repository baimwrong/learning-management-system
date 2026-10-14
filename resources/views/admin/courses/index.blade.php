@extends('layouts.admin')

@section('title', 'Course Moderation - Learning Management System')
@section('page-title', 'Course Moderation & Catalog')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-md-6">
                <form action="{{ route('admin.courses.index') }}" method="GET" class="d-flex gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search courses by title..." value="{{ request('q') }}">
                    </div>
                    <select name="status" class="form-select form-select-sm" style="max-width: 140px;" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request()->anyFilled(['q', 'status']))
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-outline-info btn-sm me-2">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Bulk Ingest Courses
                </a>
                <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Author New Course
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Course</th>
                    <th>Category</th>
                    <th>Instructor</th>
                    <th>Enrollments</th>
                    <th>Price</th>
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
                                    <small class="text-muted">{{ $course->slug }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $course->category?->name ?? 'General' }}</span></td>
                        <td class="text-muted">{{ $course->instructor?->name }}</td>
                        <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $course->enrollments->count() }} Enrolled</span></td>
                        <td class="fw-bold text-primary">{{ $course->formatted_price }}</td>
                        <td>
                            <form action="{{ route('admin.courses.toggle-publish', $course) }}" method="POST">
                                @csrf
                                <button type="submit" class="badge border-0 bg-{{ $course->is_published ? 'success' : 'secondary' }}" style="cursor: pointer;" title="Click to toggle publish status">
                                    {{ $course->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('courses.detail', $course->slug) }}" class="btn btn-outline-secondary btn-sm me-1" target="_blank" title="View Public Page">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('instructor.courses.curriculum', $course) }}" class="btn btn-outline-primary btn-sm me-1" title="Curriculum Builder">
                                <i class="bi bi-list-nested"></i>
                            </a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this course and all associated modules/lessons?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Course">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No courses found.</td></tr>
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
