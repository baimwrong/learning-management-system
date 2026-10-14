@extends('layouts.app')

@section('title', 'Assessment Results - Learning Management System')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Score Overview Card -->
            <div class="card border-0 shadow-sm p-4 p-md-5 mb-4 text-center bg-white">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3 {{ $attempt->is_passed ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}" style="width: 80px; height: 80px;">
                    <i class="bi bi-{{ $attempt->is_passed ? 'check-circle-fill' : 'x-circle-fill' }} fs-1"></i>
                </div>

                <h3 class="fw-bold mb-1 {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                    {{ $attempt->is_passed ? 'Assessment Passed!' : 'Assessment Not Passed' }}
                </h3>
                <p class="text-muted small mb-4">
                    {{ $attempt->quiz->title }} • {{ $attempt->quiz->course->title }}
                </p>

                <div class="row g-3 justify-content-center col-md-10 mx-auto mb-4">
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted small">Your Score</span>
                            <div class="h3 fw-bold {{ $attempt->is_passed ? 'text-success' : 'text-danger' }} mb-0">{{ $attempt->score_percentage }}%</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted small">Passing Threshold</span>
                            <div class="h3 fw-bold text-dark mb-0">{{ $attempt->quiz->passing_score_percentage }}%</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                            <span class="text-muted small">Points Earned</span>
                            <div class="h3 fw-bold text-primary mb-0">{{ $attempt->earned_points }} / {{ $attempt->total_points }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('student.learn', $attempt->quiz->course->slug) }}" class="btn btn-primary px-4">
                        <i class="bi bi-play-circle-fill me-1"></i> Return to Course Player
                    </a>
                    @if(!$attempt->is_passed)
                        <a href="{{ route('student.quiz.take', $attempt->quiz) }}" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-clockwise me-1"></i> Retake Assessment
                        </a>
                    @endif
                </div>
            </div>

            <!-- Detailed Question Breakdown -->
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-3">Diagnostic Breakdown & Answers</h5>

                <div class="list-group list-group-flush">
                    @foreach($attempt->quiz->questions as $index => $q)
                        @php
                            $sub = $attempt->answers_submitted[$q->id] ?? null;
                            $isCorrect = $sub['is_correct'] ?? false;
                        @endphp
                        <div class="list-group-item px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }} px-2 py-1">
                                    Q{{ $index + 1 }} • {{ $isCorrect ? 'Correct (+'.$q->points.' pts)' : 'Incorrect (0 pts)' }}
                                </span>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">{{ $q->question_text }}</h6>

                            <div class="row g-2 mb-2 small">
                                <div class="col-sm-6">
                                    <div class="p-2 rounded bg-light">
                                        <span class="text-muted">Your Submission:</span>
                                        <strong class="{{ $isCorrect ? 'text-success' : 'text-danger' }}">
                                            {{ is_array($sub['submitted'] ?? null) ? implode(', ', $sub['submitted']) : ($sub['submitted'] ?? 'No answer') }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-2 rounded bg-success bg-opacity-10 text-success">
                                        <span>Correct Key:</span>
                                        <strong>{{ implode(', ', (array)$q->correct_answers) }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if($q->explanation)
                                <div class="p-2 bg-light rounded small text-muted">
                                    <i class="bi bi-info-circle me-1 text-primary"></i> <strong>Explanation:</strong> {{ $q->explanation }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
