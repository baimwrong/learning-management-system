@extends('layouts.instructor')

@section('title', 'Enrolled Students - Instructor Studio')
@section('page-title', 'Student Enrollment Tracking')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Enrolled Students</h5>
            <small class="text-muted">Learners active across your authored courses</small>
        </div>
        <span class="badge bg-light text-dark border">{{ $enrollments->total() }} Enrollments</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Student</th>
                    <th>Course Title</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Enrolled Date</th>
                    <th>Completed Date</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($enrollments as $enr)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $enr->user?->avatar_url }}" alt="avatar" class="rounded-circle" width="34" height="34">
                                <div>
                                    <div class="fw-bold text-dark">{{ $enr->user?->name }}</div>
                                    <small class="text-muted">{{ $enr->user?->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="fw-semibold text-dark">{{ $enr->course?->title }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                                    <div class="progress-bar bg-success" style="width: {{ $enr->progress_percentage }}%"></div>
                                </div>
                                <span class="fw-bold">{{ $enr->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $enr->status === 'completed' ? 'success' : 'primary' }}">
                                {{ ucfirst($enr->status) }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $enr->enrolled_at ? $enr->enrolled_at->format('M d, Y') : '—' }}</td>
                        <td class="text-muted">{{ $enr->completed_at ? $enr->completed_at->format('M d, Y') : 'In progress' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-muted"></i>
                            No students enrolled in your courses yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($enrollments->hasPages())
        <div class="card-footer bg-white py-3 d-flex justify-content-center">
            {{ $enrollments->links() }}
        </div>
    @endif
</div>
@endsection
