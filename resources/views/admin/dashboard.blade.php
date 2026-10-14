@extends('layouts.admin')

@section('title', 'Admin Dashboard - Learning Management System')
@section('page-title', 'System Administrator Overview')

@section('content')
<!-- Metric Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Accounts</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($stats['total_users']) }}</h3>
                    <small class="text-success"><i class="bi bi-person-badge"></i> {{ $stats['total_students'] }} Students • {{ $stats['total_instructors'] }} Faculty</small>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Course Catalog</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($stats['total_courses']) }}</h3>
                    <small class="text-primary"><i class="bi bi-check-circle"></i> {{ $stats['published_courses'] }} Published</small>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-journal-album"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Enrollments</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($stats['total_enrollments']) }}</h3>
                    <small class="text-info"><i class="bi bi-check2-all"></i> {{ $stats['completed_enrollments'] }} Completed</small>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Bulk Ingestion</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ number_format($stats['total_imports']) }}</h3>
                    <small class="text-warning"><i class="bi bi-file-earmark-spreadsheet"></i> Batch Jobs Logged</small>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Administration Actions -->
<div class="card p-4 mb-4 border-0 shadow-sm bg-white">
    <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Management Actions</h5>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-primary">
            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Open Bulk Import Center
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-person-plus me-1"></i> Manage Users
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-journal-check me-1"></i> Moderate Courses
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-tags me-1"></i> Configure Categories
        </a>
        <a href="{{ route('admin.system') }}" class="btn btn-outline-info">
            <i class="bi bi-database me-1"></i> PostgreSQL 18 Telemetry
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Enrollments -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0">Recent Course Enrollments</h6>
                <span class="badge bg-light text-muted border">Live Activity</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Progress</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($recentEnrollments as $enr)
                            <tr>
                                <td class="fw-semibold">{{ $enr->user?->name }}</td>
                                <td>{{ Str::limit($enr->course?->title, 25) }}</td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 60px;">
                                        <div class="progress-bar bg-success" style="width: {{ $enr->progress_percentage }}%"></div>
                                    </div>
                                    <span style="font-size: 0.7rem;">{{ $enr->progress_percentage }}%</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $enr->status === 'completed' ? 'success' : 'primary' }}">
                                        {{ ucfirst($enr->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">No enrollments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Bulk Import Logs -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0">Recent Bulk Import Operations</h6>
                <a href="{{ route('admin.bulk-import.index') }}" class="small text-primary text-decoration-none">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Type</th>
                            <th>File</th>
                            <th>Success / Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($recentImports as $imp)
                            <tr>
                                <td class="fw-bold text-uppercase text-primary">{{ $imp->import_type }}</td>
                                <td>{{ Str::limit($imp->original_filename, 20) }}</td>
                                <td>{{ $imp->successful_rows }} / {{ $imp->total_rows }}</td>
                                <td>
                                    <span class="badge bg-{{ $imp->status === 'completed' ? 'success' : ($imp->status === 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($imp->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">No bulk imports logged yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
