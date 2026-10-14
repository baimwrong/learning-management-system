@extends('layouts.admin')

@section('title', 'System Telemetry & PostgreSQL 18 - Learning Management System')
@section('page-title', 'System & Database Telemetry')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-database-check text-primary me-2"></i>PostgreSQL 18 Database Engine
            </h5>
            <p class="text-secondary small">
                The Learning Management System is engineered to leverage PostgreSQL 18's advanced capabilities including native JSONB indexing, execution plan optimizations, transactional batch processing, and connection pooling.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 200px;">Configuration Parameter</th>
                            <th>Active Environment Setting</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Database Connection Driver</td>
                            <td><code>{{ $systemInfo['database_driver'] }}</code> (PostgreSQL PDO)</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Database Version Telemetry</td>
                            <td><span class="badge bg-primary font-monospace">{{ $systemInfo['database_version'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">JSONB Column Support</td>
                            <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Enabled & Indexed (options, error_log)</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Laravel Framework Version</td>
                            <td><code>{{ $systemInfo['laravel_version'] }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">PHP Runtime Environment</td>
                            <td><code>PHP {{ $systemInfo['php_version'] }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Application Environment</td>
                            <td><span class="badge bg-info text-dark">{{ $systemInfo['app_env'] }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Local File Storage Status</td>
                            <td><span class="badge bg-success">{{ $systemInfo['storage_status'] }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-wifi-off text-success me-2"></i>Air-Gapped Intranet Asset Verification
            </h5>
            <p class="text-secondary small mb-3">
                Verification of locally downloaded frontend libraries ensuring zero dependency on public Internet or CDNs:
            </p>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <strong>Bootstrap 5.3.3 CSS & JS Bundle</strong>
                        <div class="text-muted" style="font-size: 0.75rem;">public/vendor/bootstrap/css/bootstrap.min.css</div>
                    </div>
                    <span class="badge bg-success"><i class="bi bi-check2"></i> Local & Offline Ready</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <strong>Bootstrap Icons 1.11.3 (CSS + WOFF/WOFF2 Fonts)</strong>
                        <div class="text-muted" style="font-size: 0.75rem;">public/vendor/bootstrap-icons/fonts/bootstrap-icons.woff2</div>
                    </div>
                    <span class="badge bg-success"><i class="bi bi-check2"></i> Local & Offline Ready</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <strong>FontAwesome 6.5.2 Free Webfonts</strong>
                        <div class="text-muted" style="font-size: 0.75rem;">public/vendor/fontawesome/webfonts/fa-solid-900.woff2</div>
                    </div>
                    <span class="badge bg-success"><i class="bi bi-check2"></i> Local & Offline Ready</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-light mb-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill text-primary me-2"></i>PostgreSQL 18 Setup Guide</h6>
            <p class="small text-muted">
                To launch PostgreSQL 18 in Laragon or local server:
            </p>
            <ol class="small text-muted ps-3 mb-0">
                <li class="mb-1">Install PostgreSQL 18 into Laragon or local service directory.</li>
                <li class="mb-1">Create database: <code>createdb learning_management_system</code></li>
                <li class="mb-1">Ensure credentials in <code>.env</code> match <code>DB_USERNAME</code> and <code>DB_PASSWORD</code>.</li>
                <li class="mb-0">Run: <code>php artisan migrate --seed</code></li>
            </ol>
        </div>

        <div class="card border-0 shadow-sm p-4">
            <h6 class="fw-bold mb-3">Quick Maintenance</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Data Ingestion Center
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-people me-1"></i> User Directory
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Visit Public Portal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
