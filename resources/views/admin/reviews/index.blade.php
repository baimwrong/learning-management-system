@extends('layouts.admin')

@section('title', 'Reviews Moderation - Learning Management System')
@section('page-title', 'Student Reviews Moderation')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Ratings & Feedback</h5>
            <small class="text-muted">Review, approve, or hide public ratings submitted by students</small>
        </div>
        <span class="badge bg-light text-dark border">{{ $reviews->total() }} Reviews Total</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Rating</th>
                    <th>Feedback Review</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($reviews as $rev)
                    <tr>
                        <td class="fw-semibold">{{ $rev->user->name }}</td>
                        <td>{{ Str::limit($rev->course->title, 30) }}</td>
                        <td>
                            <div class="rating-stars small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $rev->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="text-secondary" style="max-width: 300px;">{{ $rev->review ?? 'No written comment' }}</td>
                        <td>
                            <span class="badge bg-{{ $rev->is_approved ? 'success' : 'warning' }}">
                                {{ $rev->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $rev->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.reviews.toggle', $rev) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-{{ $rev->is_approved ? 'warning' : 'success' }} btn-sm me-1" title="Toggle Approval">
                                    <i class="bi bi-{{ $rev->is_approved ? 'eye-slash' : 'check-lg' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.destroy', $rev) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Review">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No student reviews recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
        <div class="card-footer bg-white py-3 d-flex justify-content-center">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
