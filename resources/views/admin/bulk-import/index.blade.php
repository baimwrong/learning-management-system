@extends('layouts.admin')

@section('title', 'Bulk Data Ingestion Center - Learning Management System')
@section('page-title', 'Bulk Data Ingestion Center')

@section('content')
<!-- Header Intro -->
<div class="card border-0 shadow-sm p-4 mb-4 bg-white">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Admin Bulk Data Import Center
            </h4>
            <p class="text-secondary small mb-0">
                Accelerate LMS initialization by uploading standardized CSV datasets. Ingest hundreds of user accounts, curriculum courses, question banks, or student enrollments in high-speed transactional batches with row-level validation.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <span class="badge bg-primary px-3 py-2 fs-6">
                <i class="bi bi-database me-1"></i> {{ number_format($stats['total_rows_imported']) }} Rows Ingested
            </span>
        </div>
    </div>
</div>

<!-- 4 Ingestion Modals / Panels -->
<div class="row g-4 mb-5">
    <!-- 1. Users Import Card -->
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="badge bg-light text-primary border">Type: users</span>
                </div>

                <h5 class="fw-bold mb-1">Users & Accounts</h5>
                <p class="text-muted small flex-grow-1">
                    Bulk import students, instructors, and administrators with auto-hashing of passwords and role assignments.
                </p>

                <div class="p-2 bg-light rounded-2 small text-muted font-monospace mb-3" style="font-size: 0.72rem;">
                    Headers: name, email, role, password, phone, bio
                </div>

                <div class="d-grid gap-2 mt-auto">
                    <a href="{{ route('admin.bulk-import.template', 'users') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal" data-type="users" data-label="Users & Accounts">
                        <i class="bi bi-upload me-1"></i> Upload Users CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Courses Import Card -->
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-journal-album"></i>
                    </div>
                    <span class="badge bg-light text-success border">Type: courses</span>
                </div>

                <h5 class="fw-bold mb-1">Courses & Catalog</h5>
                <p class="text-muted small flex-grow-1">
                    Batch publish courses with category resolution, pricing, difficulty level, and assigned instructor email.
                </p>

                <div class="p-2 bg-light rounded-2 small text-muted font-monospace mb-3" style="font-size: 0.72rem;">
                    Headers: title, category, instructor_email, level, price, summary, description, is_published
                </div>

                <div class="d-grid gap-2 mt-auto">
                    <a href="{{ route('admin.bulk-import.template', 'courses') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal" data-type="courses" data-label="Courses & Catalog">
                        <i class="bi bi-upload me-1"></i> Upload Courses CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Quiz Questions Import Card -->
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-question-diamond-fill"></i>
                    </div>
                    <span class="badge bg-light text-warning border">Type: questions</span>
                </div>

                <h5 class="fw-bold mb-1">Quiz Question Bank</h5>
                <p class="text-muted small flex-grow-1">
                    Populate assessments with single-choice, multiple-choice, true/false questions and explanations.
                </p>

                <div class="p-2 bg-light rounded-2 small text-muted font-monospace mb-3" style="font-size: 0.72rem;">
                    Headers: course, quiz_title, question_text, question_type, options, correct_answer, points, explanation
                </div>

                <div class="d-grid gap-2 mt-auto">
                    <a href="{{ route('admin.bulk-import.template', 'questions') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal" data-type="questions" data-label="Quiz Questions Bank">
                        <i class="bi bi-upload me-1"></i> Upload Questions CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Enrollments Import Card -->
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <span class="badge bg-light text-info border">Type: enrollments</span>
                </div>

                <h5 class="fw-bold mb-1">Student Enrollments</h5>
                <p class="text-muted small flex-grow-1">
                    Batch enroll registered students into designated courses by student email and course identifier.
                </p>

                <div class="p-2 bg-light rounded-2 small text-muted font-monospace mb-3" style="font-size: 0.72rem;">
                    Headers: student_email, course, status
                </div>

                <div class="d-grid gap-2 mt-auto">
                    <a href="{{ route('admin.bulk-import.template', 'enrollments') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#uploadModal" data-type="enrollments" data-label="Student Enrollments">
                        <i class="bi bi-upload me-1"></i> Upload Enrollments CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ingestion Audit History Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark mb-0">Import History & Audit Telemetry</h5>
            <small class="text-muted">Row-level execution logs tracked with PostgreSQL JSONB</small>
        </div>
        <span class="badge bg-light text-dark border">{{ $imports->total() }} Logged Jobs</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase small">
                <tr>
                    <th>Job ID</th>
                    <th>Import Type</th>
                    <th>Original Filename</th>
                    <th>Processed / Total</th>
                    <th>Success Rate</th>
                    <th>Status</th>
                    <th>Uploaded At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($imports as $imp)
                    <tr>
                        <td class="fw-bold">#{{ $imp->id }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase px-2 py-1">
                                {{ $imp->import_type }}
                            </span>
                        </td>
                        <td>
                            <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i>
                            <span class="fw-semibold">{{ $imp->original_filename }}</span>
                        </td>
                        <td>
                            <strong>{{ $imp->successful_rows }}</strong> / {{ $imp->total_rows }}
                            @if($imp->failed_rows > 0)
                                <span class="badge bg-danger-subtle text-danger ms-1">{{ $imp->failed_rows }} failed</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $rate = $imp->total_rows > 0 ? round(($imp->successful_rows / $imp->total_rows) * 100, 1) : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; min-width: 50px;">
                                    <div class="progress-bar bg-{{ $rate == 100 ? 'success' : ($rate > 50 ? 'warning' : 'danger') }}" style="width: {{ $rate }}%"></div>
                                </div>
                                <span>{{ $rate }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($imp->status === 'completed')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
                            @elseif($imp->status === 'processing')
                                <span class="badge bg-warning"><i class="bi bi-hourglass-split me-1"></i>Processing</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Failed</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $imp->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.bulk-import.show', $imp) }}" class="btn btn-outline-primary btn-sm me-1" title="View Audit Details">
                                <i class="bi bi-eye"></i> Details
                            </a>
                            <form action="{{ route('admin.bulk-import.destroy', $imp) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this import history log?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Log">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                            No bulk import operations executed yet. Use the cards above to upload CSV datasets.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($imports->hasPages())
        <div class="card-footer bg-white py-3 d-flex justify-content-center">
            {{ $imports->links() }}
        </div>
    @endif
</div>

<!-- Upload CSV Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.bulk-import.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="import_type" id="modalImportType" value="users">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="uploadModalLabel">Upload CSV Dataset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-info small d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>Uploading to: <strong id="modalLabelText">Users & Accounts</strong></div>
                    </div>

                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-semibold small">Select CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,text/csv,text/plain" required>
                        <div class="form-text small">Maximum file size: 10MB. Must include valid CSV header row.</div>
                    </div>

                    <div class="p-3 bg-light rounded-3 small">
                        <div class="fw-bold mb-1"><i class="bi bi-shield-check text-success me-1"></i> Transactional Ingestion</div>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">
                            Rows are validated sequentially. Invalid rows will be recorded in the PostgreSQL JSONB error telemetry log without interrupting valid records.
                        </p>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Start Processing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const uploadModal = document.getElementById('uploadModal');
    if (uploadModal) {
        uploadModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const type = button.getAttribute('data-type');
            const label = button.getAttribute('data-label');

            document.getElementById('modalImportType').value = type;
            document.getElementById('modalLabelText').textContent = label;
        });
    }
</script>
@endpush
