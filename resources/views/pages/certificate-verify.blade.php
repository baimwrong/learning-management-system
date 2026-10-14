@extends('layouts.app')

@section('title', 'Certificate Verification - Learning Management System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle mb-3" style="width: 70px; height: 70px;">
                    <i class="bi bi-patch-check-fill fs-2"></i>
                </div>
                <h2 class="fw-bold">Public Certificate Verification</h2>
                <p class="text-muted">Authenticate official course completion credentials issued by Learning Management System</p>
            </div>

            <!-- Verification Search Box -->
            <div class="card p-4 border-0 shadow-sm mb-4">
                <form action="{{ route('certificates.verify') }}" method="GET" class="row g-2">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-qr-code text-muted"></i></span>
                            <input type="text" name="code" class="form-control" placeholder="Enter Certificate Code (e.g. LMS-2026-PG18-9842)" value="{{ $searchCode }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-shield-check me-1"></i> Verify
                        </button>
                    </div>
                </form>
            </div>

            <!-- Verification Results -->
            @if($searchCode)
                @if($certificate)
                    <div class="card border-success shadow-sm p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 text-success mb-3">
                            <i class="bi bi-check-circle-fill fs-1"></i>
                            <div>
                                <h4 class="fw-bold mb-0 text-success">Valid & Authentic Certificate</h4>
                                <small class="text-muted">Issued by Learning Management System Academic Authority</small>
                            </div>
                        </div>

                        <hr>

                        <div class="row g-3 py-2">
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold">Student Name</div>
                                <div class="fs-5 fw-bold text-dark">{{ $certificate->user->name }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold">Certificate Code</div>
                                <div class="fs-5 fw-bold text-primary font-monospace">{{ $certificate->certificate_code }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold">Course Title</div>
                                <div class="fw-semibold text-dark">{{ $certificate->course->title }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold">Issue Date</div>
                                <div class="fw-semibold text-dark">{{ $certificate->issued_at->format('F d, Y') }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold">Instructor</div>
                                <div class="text-dark">{{ $certificate->course->instructor?->name ?? 'LMS Faculty' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold">Verification Status</div>
                                <span class="badge bg-success px-3 py-2"><i class="bi bi-shield-fill-check me-1"></i> Digitally Verified</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <a href="{{ route('certificates.show', $certificate) }}" class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-printer me-1"></i> View & Print Certificate
                            </a>
                        </div>
                    </div>
                @else
                    <div class="card border-danger shadow-sm p-4 text-center">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-1 mb-2"></i>
                        <h4 class="text-danger fw-bold">Certificate Not Found</h4>
                        <p class="text-muted small mb-0">No matching credential was located with code: <strong>{{ $searchCode }}</strong>. Please check the code for typos.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
