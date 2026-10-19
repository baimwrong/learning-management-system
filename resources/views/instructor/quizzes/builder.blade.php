@extends('layouts.instructor')

@section('title', 'Quiz Builder: ' . $quiz->title)
@section('page-title', 'Assessment Question Bank')

@section('content')
<div class="mb-4">
    <a href="{{ route('instructor.courses.curriculum', $quiz->course) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Curriculum
    </a>
</div>

<div class="row g-4">
    <!-- Quiz Info & Add Question Column -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 mb-2 align-self-start">
                <i class="bi bi-patch-question-fill me-1"></i> Quiz Configuration
            </span>
            <h4 class="fw-bold mb-1">{{ $quiz->title }}</h4>
            <p class="text-muted small mb-3">{{ $quiz->course->title }}</p>

            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Total Questions:</span>
                    <strong class="text-dark">{{ $quiz->questions->count() }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Total Points:</span>
                    <strong class="text-dark">{{ $quiz->total_points }} pts</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Passing Percentage:</span>
                    <span class="badge bg-success">{{ $quiz->passing_score_percentage }}%</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted">Time Limit:</span>
                    <span class="badge bg-light text-dark border">{{ $quiz->time_limit_minutes > 0 ? $quiz->time_limit_minutes . ' mins' : 'Unlimited' }}</span>
                </li>
            </ul>
        </div>

        <!-- Add Question Form -->
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle text-success me-2"></i>Add Question</h5>
            <form action="{{ route('instructor.questions.store', $quiz) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Question Prompt</label>
                    <textarea name="question_text" class="form-control" rows="3" required placeholder="e.g. Which command is used to optimize and analyze query execution in PostgreSQL?"></textarea>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Type</label>
                        <select name="question_type" class="form-select form-select-sm" id="questionTypeSelect" required>
                            <option value="single_choice" selected>Single Choice</option>
                            <option value="true_false">True / False</option>
                            <option value="multiple_choice">Multiple Select</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Points</label>
                        <input type="number" name="points" class="form-control form-control-sm" value="1" min="1" required>
                    </div>
                </div>

                <div id="optionsContainer">
                    <label class="form-label small fw-semibold mb-1">Answer Options</label>
                    <div class="mb-2 input-group input-group-sm">
                        <span class="input-group-text">A</span>
                        <input type="text" name="options[]" class="form-control" placeholder="Option A" required>
                    </div>
                    <div class="mb-2 input-group input-group-sm">
                        <span class="input-group-text">B</span>
                        <input type="text" name="options[]" class="form-control" placeholder="Option B" required>
                    </div>
                    <div class="mb-2 input-group input-group-sm">
                        <span class="input-group-text">C</span>
                        <input type="text" name="options[]" class="form-control" placeholder="Option C">
                    </div>
                    <div class="mb-2 input-group input-group-sm">
                        <span class="input-group-text">D</span>
                        <input type="text" name="options[]" class="form-control" placeholder="Option D">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Correct Answer Key</label>
                    <input type="text" name="correct_answers" class="form-control form-control-sm" required placeholder="e.g. A (or A,C for multiple select, or True/False)">
                    <small class="text-muted" style="font-size: 0.72rem;">Enter key letter (e.g. A, B) matching the correct option.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Explanation (shown after submission)</label>
                    <textarea name="explanation" class="form-control form-control-sm" rows="2" placeholder="Explain why this answer is correct..."></textarea>
                </div>

                <button type="submit" class="btn btn-success btn-sm w-100 py-2">
                    <i class="bi bi-plus-lg me-1"></i> Append Question
                </button>
            </form>
        </div>
    </div>

    <!-- Questions Bank List Column -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Question Bank ({{ $quiz->questions->count() }})</h5>
                <span class="badge bg-light text-dark border">Auto-Graded</span>
            </div>

            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($quiz->questions as $index => $q)
                        <div class="list-group-item p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary me-2">Q{{ $index + 1 }} • {{ $q->points }} pt{{ $q->points > 1 ? 's' : '' }}</span>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $q->id }}" title="Edit Question">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('instructor.questions.destroy', $q) }}" method="POST" onsubmit="return confirm('Remove question?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Edit Question Modal -->
                            <div class="modal fade" id="editQuestionModal{{ $q->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('instructor.questions.update', $q) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Question Q{{ $index + 1 }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Question Prompt</label>
                                                    <textarea name="question_text" class="form-control" rows="3" required>{{ $q->question_text }}</textarea>
                                                </div>

                                                <div class="row g-2 mb-3">
                                                    <div class="col-4">
                                                        <label class="form-label small fw-semibold">Type</label>
                                                        <select name="question_type" class="form-select form-select-sm" required>
                                                            <option value="single_choice" {{ $q->question_type === 'single_choice' ? 'selected' : '' }}>Single Choice</option>
                                                            <option value="true_false" {{ $q->question_type === 'true_false' ? 'selected' : '' }}>True / False</option>
                                                            <option value="multiple_choice" {{ $q->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Select</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label small fw-semibold">Points</label>
                                                        <input type="number" name="points" class="form-control form-control-sm" value="{{ $q->points }}" min="1" required>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label small fw-semibold">Order Number</label>
                                                        <input type="number" name="order_number" class="form-control form-control-sm" value="{{ $q->order_number }}" min="1">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold mb-1">Answer Options</label>
                                                    @php
                                                        $optKeys = ['A', 'B', 'C', 'D'];
                                                        $optsMap = collect($q->options ?? [])->keyBy('key');
                                                    @endphp
                                                    @foreach($optKeys as $letter)
                                                        <div class="mb-2 input-group input-group-sm">
                                                            <span class="input-group-text">{{ $letter }}</span>
                                                            <input type="text" name="options[]" class="form-control" value="{{ $optsMap[$letter]['text'] ?? '' }}" placeholder="Option {{ $letter }}">
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Correct Answer Key</label>
                                                    <input type="text" name="correct_answers" class="form-control form-control-sm" value="{{ is_array($q->correct_answers) ? implode(',', $q->correct_answers) : $q->correct_answers }}" required placeholder="e.g. A or A,C">
                                                    <small class="text-muted" style="font-size: 0.72rem;">Enter key letter matching the correct option.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Explanation</label>
                                                    <textarea name="explanation" class="form-control form-control-sm" rows="2">{{ $q->explanation }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <h6 class="fw-bold text-dark mb-3">{{ $q->question_text }}</h6>

                            @if(!empty($q->options) && is_array($q->options))
                                <div class="row g-2 mb-3">
                                    @foreach($q->options as $opt)
                                        @php
                                            $isCorrect = in_array($opt['key'], (array)$q->correct_answers);
                                        @endphp
                                        <div class="col-sm-6">
                                            <div class="p-2 border rounded-2 small {{ $isCorrect ? 'bg-success bg-opacity-10 border-success text-success fw-bold' : 'bg-light text-secondary' }}">
                                                <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-secondary' }} me-1">{{ $opt['key'] }}</span>
                                                {{ $opt['text'] }}
                                                @if($isCorrect)
                                                    <i class="bi bi-check-circle-fill ms-1"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($q->explanation)
                                <div class="p-2 bg-light rounded small text-muted">
                                    <strong>Explanation:</strong> {{ $q->explanation }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-question-circle fs-1 mb-2 d-block text-muted"></i>
                            <h6>No Questions in this Assessment</h6>
                            <p class="small">Use the form on the left to add questions, or use Admin Bulk Import to ingest a CSV question bank.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
