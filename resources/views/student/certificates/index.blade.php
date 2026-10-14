@extends('layouts.app')

@section('title', 'My Certificates - Learning Management System')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">My Certifications & Badges</h3>
            <p class="text-muted small mb-0">Official verified credentials awarded upon 100% course curriculum completion</p>
        </div>
        <a href="{{ route('certificates.verify') }}" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="bi bi-patch-check me-1"></i> Public Verification Portal
        </a>
    </div>

    <div class="row g-4">
        @forelse($certificates as $cert)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm p-4 bg-white position-relative overflow-hidden">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle">
                            <i class="bi bi-award-fill fs-2"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            Verified Active
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $cert->course->title }}</h5>
                    <p class="text-muted small mb-3">
                        Instructor: {{ $cert->course->instructor?->name ?? 'Faculty Authority' }}
                    </p>

                    <div class="mt-auto pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Credential ID:</span>
                            <span class="font-monospace fw-bold text-primary small">{{ $cert->certificate_code }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Date Awarded:</span>
                            <span class="text-dark small fw-semibold">{{ $cert->issued_at->format('M d, Y') }}</span>
                        </div>

                        <a href="{{ route('certificates.show', $cert) }}" class="btn btn-outline-primary btn-sm w-100 fw-semibold" target="_blank">
                            <i class="bi bi-printer me-1"></i> View & Print Credential
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-3 border">
                    <i class="bi bi-award fs-1 text-muted mb-2 d-block"></i>
                    <h4>No Certificates Earned Yet</h4>
                    <p class="text-muted small">Complete 100% of any enrolled course curriculum to automatically earn and verify your official certificate.</p>
                    <a href="{{ route('student.my-courses') }}" class="btn btn-primary btn-sm">Resume Learning</a>
                </div>
            </div>
        @endforelse
    </div>

    @if($certificates->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $certificates->links() }}
        </div>
    @endif
</div>
@endsection
