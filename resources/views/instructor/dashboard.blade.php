@extends('layouts.instructor')

@section('title', 'Instructor Dashboard - Learning Management System')
@section('page-title', 'Instructor Teaching Studio')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">My Courses</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_courses'] }}</h3>
                    <small class="text-success"><i class="bi bi-check-circle"></i> {{ $stats['published_courses'] }} Active</small>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 fs-3">
                    <i class="bi bi-journal-album"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Enrolled Students</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_students'] }}</h3>
                    <small class="text-muted">Unique Learners</small>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 fs-3">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Enrollments</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_enrollments'] }}</h3>
                    <small class="text-info">Across All Courses</small>
                </div>
                <div class="p-3 bg-info bg-opacity-10 text-info rounded-3 fs-3">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm bg-gradient text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-white small opacity-75">Quick Action</span>
                    <h5 class="fw-bold mt-1 mb-2">New Course</h5>
                    <a href="{{ route('instructor.courses.create') }}" class="btn btn-light btn-sm text-success fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Author Now
                    </a>
                </div>
                <div class="fs-1 opacity-50"><i class="bi bi-pencil-square"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Instructor Courses List -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">My Teaching Curriculum</h6>
                <a href="{{ route('instructor.courses.index') }}" class="small text-decoration-none">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Course</th>
                            <th>Curriculum</th>
                            <th>Enrollments</th>
                            <th class="text-end">Curriculum</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($recentCourses as $course)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ Str::limit($course->title, 30) }}</div>
                                    <span class="badge bg-{{ $course->is_published ? 'success' : 'secondary' }}-subtle text-{{ $course->is_published ? 'success' : 'secondary' }}">
                                        {{ $course->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>{{ $course->modules_count }} Modules</td>
                                <td>{{ $course->enrollments_count }} Students</td>
                                <td class="text-end">
                                    <a href="{{ route('instructor.courses.curriculum', $course) }}" class="btn btn-outline-success btn-sm" title="Curriculum Builder">
                                        <i class="bi bi-list-nested me-1"></i> Curriculum
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">You haven't authored any courses yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Enrolled Students -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Recent Student Activity</h6>
                <a href="{{ route('instructor.students') }}" class="small text-decoration-none">View All</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentEnrollments as $enr)
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $enr->user?->avatar_url }}" alt="avatar" class="rounded-circle" width="32" height="32">
                            <div>
                                <div class="fw-semibold small text-dark">{{ $enr->user?->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($enr->course?->title, 25) }}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $enr->status === 'completed' ? 'success' : 'primary' }}-subtle text-{{ $enr->status === 'completed' ? 'success' : 'primary' }}">
                                {{ $enr->progress_percentage }}%
                            </span>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $enr->enrolled_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted small">No student enrollments yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
