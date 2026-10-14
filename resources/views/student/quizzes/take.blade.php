@extends('layouts.app')

@section('title', 'Assessment: ' . $quiz->title . ' - Learning Management System')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 mb-2">
                            Interactive Assessment
                        </span>
                        <h3 class="fw-bold text-dark mb-1">{{ $quiz->title }}</h3>
                        <p class="text-muted small mb-0">{{ $quiz->course->title }}</p>
                    </div>

                    @if($quiz->time_limit_minutes > 0)
                        <div class="text-end">
                            <span class="text-muted small d-block">Time Remaining</span>
                            <div class="h4 fw-bold text-danger font-monospace mb-0" id="timerDisplay">
                                {{ sprintf('%02d:00', $quiz->time_limit_minutes) }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-flex flex-wrap gap-4 mt-3 pt-3 border-top small text-muted">
                    <div><i class="bi bi-question-circle me-1 text-primary"></i> <strong>{{ $quiz->questions->count() }}</strong> Questions</div>
                    <div><i class="bi bi-award me-1 text-warning"></i> <strong>{{ $quiz->total_points }}</strong> Total Points</div>
                    <div><i class="bi bi-shield-check me-1 text-success"></i> <strong>{{ $quiz->passing_score_percentage }}%</strong> Required to Pass</div>
                </div>
            </div>

            <!-- Quiz Taking Form -->
            <form action="{{ route('student.quiz.submit', $quiz) }}" method="POST" id="quizForm">
                @csrf

                @foreach($quiz->questions as $index => $q)
                    <div class="card border-0 shadow-sm p-4 mb-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary px-3 py-2">Question {{ $index + 1 }}</span>
                            <span class="badge bg-light text-muted border">{{ $q->points }} point{{ $q->points > 1 ? 's' : '' }}</span>
                        </div>

                        <h5 class="fw-bold text-dark mb-4">{{ $q->question_text }}</h5>

                        @if(!empty($q->options) && is_array($q->options))
                            <div class="d-flex flex-column gap-2">
                                @foreach($q->options as $opt)
                                    @php
                                        $inputType = ($q->question_type === 'multiple_choice') ? 'checkbox' : 'radio';
                                        $inputName = ($q->question_type === 'multiple_choice') ? "answers[{$q->id}][]" : "answers[{$q->id}]";
                                        $inputId = "q{$q->id}_opt_{$opt['key']}";
                                    @endphp
                                    <label class="p-3 border rounded-3 d-flex align-items-center gap-3 bg-light hover-shadow" for="{{ $inputId }}" style="cursor: pointer;">
                                        <input class="form-check-input mt-0 fs-5" type="{{ $inputType }}" name="{{ $inputName }}" id="{{ $inputId }}" value="{{ $opt['key'] }}">
                                        <div>
                                            <span class="fw-bold me-2 text-primary">{{ $opt['key'] }}.</span>
                                            <span>{{ $opt['text'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="mb-3">
                                <input type="text" name="answers[{{ $q->id }}]" class="form-control" placeholder="Type your answer here..." required>
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Submit Button -->
                <div class="card p-3 border-0 shadow-sm bg-white sticky-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('student.learn', $quiz->course->slug) }}" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Exit quiz? Answers will not be submitted.')">
                            Cancel & Exit
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-send-fill me-1"></i> Submit Assessment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if($quiz->time_limit_minutes > 0)
@push('scripts')
<script>
    let totalSeconds = {{ $quiz->time_limit_minutes * 60 }};
    const timerDisplay = document.getElementById('timerDisplay');
    const quizForm = document.getElementById('quizForm');

    const timerInterval = setInterval(function() {
        if (totalSeconds <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! Your quiz will now be submitted automatically.');
            quizForm.submit();
            return;
        }

        totalSeconds--;
        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;
        timerDisplay.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }, 1000);
</script>
@endpush
@endif
