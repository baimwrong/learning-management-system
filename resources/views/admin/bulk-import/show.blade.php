@extends('layouts.admin')

@section('title', 'Import Job #' . $bulkImport->id . ' Details - Learning Management System')
@section('page-title', 'Bulk Ingestion Job #' . $bulkImport->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Bulk Import Center
    </a>
</div>

<!-- Job Summary Card -->
<div class="card border-0 shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <span class="badge bg-secondary-subtle text-secondary text-uppercase px-3 py-1 mb-2">
                Type: {{ $bulkImport->import_type }}
            </span>
            <h4 class="fw-bold text-dark mb-0">Job #{{ $bulkImport->id }} — {{ $bulkImport->original_filename }}</h4>
        </div>
        <div>
            @if($bulkImport->status === 'completed')
                <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Completed</span>
            @elseif($bulkImport->status === 'processing')
                <span class="badge bg-warning fs-6 px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Processing</span>
            @else
                <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-exclamation-triangle me-1"></i>Failed</span>
            @endif
        </div>
    </div>

    <div class="row g-3 py-2">
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 bg-light rounded-3">
                <div class="text-muted small">Total CSV Rows</div>
                <div class="fs-4 fw-bold text-dark">{{ $bulkImport->total_rows }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 bg-success bg-opacity-10 rounded-3">
                <div class="text-success small fw-semibold">Successful Ingestions</div>
                <div class="fs-4 fw-bold text-success">{{ $bulkImport->successful_rows }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 bg-danger bg-opacity-10 rounded-3">
                <div class="text-danger small fw-semibold">Failed Rows</div>
                <div class="fs-4 fw-bold text-danger">{{ $bulkImport->failed_rows }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                <div class="text-primary small fw-semibold">Success Rate</div>
                <div class="fs-4 fw-bold text-primary">
                    {{ $bulkImport->total_rows > 0 ? round(($bulkImport->successful_rows / $bulkImport->total_rows) * 100, 1) : 0 }}%
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-4 mt-3 pt-3 border-top text-muted small">
        <div><i class="bi bi-person me-1"></i> Initiated by: <strong>{{ $bulkImport->user?->name ?? 'Admin' }}</strong></div>
        <div><i class="bi bi-calendar me-1"></i> Executed on: <strong>{{ $bulkImport->created_at->format('M d, Y H:i:s') }}</strong></div>
        <div><i class="bi bi-hdd me-1"></i> Stored at: <code>{{ $bulkImport->filename }}</code></div>
    </div>
</div>

<!-- Row-Level Error Telemetry (PostgreSQL JSONB) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-bug me-2 text-danger"></i>Row-Level Validation Error Log
        </h5>
        <span class="badge bg-danger-subtle text-danger">{{ count($bulkImport->error_log ?? []) }} Recorded Errors</span>
    </div>

    @if(!empty($bulkImport->error_log) && count($bulkImport->error_log) > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 100px;">CSV Line</th>
                        <th>Error Diagnostic Message</th>
                        <th>Submitted Row Data Payload</th>
                    </tr>
                </thead>
                <tbody class="small font-monospace">
                    @foreach($bulkImport->error_log as $err)
                        <tr>
                            <td class="fw-bold text-danger">Line {{ $err['row'] ?? '?' }}</td>
                            <td class="text-danger fw-semibold">{{ $err['error'] ?? 'Validation failure' }}</td>
                            <td>
                                @if(!empty($err['data']))
                                    <pre class="mb-0 bg-light p-2 rounded small text-dark" style="max-height: 120px; overflow-y: auto;">{{ json_encode($err['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-5 text-center">
            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-check-circle-fill fs-2"></i>
            </div>
            <h5 class="fw-bold text-success">Zero Validation Errors</h5>
            <p class="text-muted small mb-0">All records in this CSV dataset were verified and committed cleanly to PostgreSQL 18.</p>
        </div>
    @endif
</div>
@endsection
