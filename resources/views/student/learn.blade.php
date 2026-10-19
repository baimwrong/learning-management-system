@extends('layouts.learn')

@section('title', ($currentLesson ? $currentLesson->title . ' - ' : '') . $course->title . ' - Learning Management System')

@section('player-body')
<!-- Player Top Header -->
<header class="player-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('student.my-courses') }}" class="btn btn-outline-light btn-sm" title="Back to My Courses">
            <i class="bi bi-arrow-left me-1"></i> Exit
        </a>
        <div class="d-none d-md-block">
            <h6 class="mb-0 text-white fw-bold">{{ Str::limit($course->title, 45) }}</h6>
            <small class="text-secondary" style="font-size: 0.75rem;">
                <i class="bi bi-person me-1"></i> {{ $course->instructor?->name ?? 'Instructor' }}
            </small>
        </div>
    </div>

    <!-- Center Progress -->
    <div class="d-flex align-items-center gap-3">
        @if($enrollment)
            <div class="d-none d-lg-flex align-items-center gap-2">
                <span class="small text-secondary">Course Progress:</span>
                <div class="progress" style="width: 120px; height: 6px;">
                    <div class="progress-bar bg-success" style="width: {{ $enrollment->progress_percentage }}%"></div>
                </div>
                <span class="small fw-bold text-white">{{ $enrollment->progress_percentage }}%</span>
            </div>

            @if($enrollment->progress_percentage >= 100.00)
                <a href="{{ route('student.certificates') }}" class="btn btn-warning btn-sm fw-bold">
                    <i class="bi bi-award-fill me-1"></i> View Certificate
                </a>
            @endif
        @endif
    </div>

    <!-- Next / Previous Controls -->
    <div class="d-flex align-items-center gap-2">
        @if($prevLesson)
            <a href="{{ route('student.learn', [$course->slug, $prevLesson->slug]) }}" class="btn btn-dark btn-sm border-secondary text-light">
                <i class="bi bi-chevron-left"></i> Prev
            </a>
        @else
            <button class="btn btn-dark btn-sm border-secondary text-light opacity-50" disabled>
                <i class="bi bi-chevron-left"></i> Prev
            </button>
        @endif

        @if($nextLesson)
            <a href="{{ route('student.learn', [$course->slug, $nextLesson->slug]) }}" class="btn btn-primary btn-sm">
                Next <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <button class="btn btn-secondary btn-sm opacity-50" disabled>
                Next <i class="bi bi-chevron-right"></i>
            </button>
        @endif
    </div>
</header>

<!-- Player Content Container -->
<div class="player-container">
    <!-- Center Lesson Content Area -->
    <div class="player-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($currentLesson)
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4 pb-3 border-bottom">
                <div>
                    <span class="badge bg-secondary-subtle text-secondary text-uppercase mb-2" style="font-size: 0.7rem;">
                        {{ $currentLesson->module?->title }}
                    </span>
                    <h2 class="fw-bold text-dark mb-1">{{ $currentLesson->title }}</h2>
                    <div class="d-flex align-items-center gap-3 text-muted small">
                        <span><i class="bi bi-clock me-1"></i> {{ $currentLesson->duration_minutes }} minutes</span>
                        <span><i class="bi bi-tag me-1 text-capitalize"></i> {{ $currentLesson->content_type }}</span>
                    </div>
                </div>

                <div>
                    <form action="{{ route('student.lessons.complete', $currentLesson) }}" method="POST">
                        @csrf
                        @php
                            $isDone = in_array($currentLesson->id, $completedLessonIds);
                        @endphp
                        <button type="submit" class="btn btn-{{ $isDone ? 'success' : 'outline-success' }} btn-sm fw-semibold">
                            <i class="bi bi-{{ $isDone ? 'check-circle-fill' : 'circle' }} me-1"></i>
                            {{ $isDone ? 'Completed' : 'Mark as Complete' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Delivery Area -->
            <div class="mb-5">
                @if($currentLesson->content_type === 'video')
                    <div class="ratio ratio-16x9 mb-4 rounded-3 overflow-hidden bg-dark shadow-sm">
                        @if($currentLesson->safe_video_url)
                            <iframe src="{{ $currentLesson->safe_video_url }}" title="{{ $currentLesson->title }}" allowfullscreen></iframe>
                        @else
                            <div class="d-flex align-items-center justify-content-center text-white p-5">
                                <div class="text-center">
                                    <i class="bi bi-play-circle fs-1 mb-2"></i>
                                    <p>Video content is hosted locally on the internal network.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif($currentLesson->content_type === 'quiz' && $currentLesson->quiz)
                    <div class="card p-5 border-0 shadow-sm bg-light text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3" style="width: 72px; height: 72px;">
                            <i class="bi bi-patch-question-fill fs-1"></i>
                        </div>
                        <h3 class="fw-bold mb-2">{{ $currentLesson->quiz->title }}</h3>
                        <p class="text-muted col-lg-8 mx-auto mb-4">{{ $currentLesson->quiz->description }}</p>

                        <div class="d-flex justify-content-center gap-4 text-muted small mb-4">
                            <div><i class="bi bi-question-circle me-1 text-primary"></i> {{ $currentLesson->quiz->questions()->count() }} Questions</div>
                            <div><i class="bi bi-trophy me-1 text-warning"></i> {{ $currentLesson->quiz->passing_score_percentage }}% Passing Score</div>
                            <div><i class="bi bi-clock me-1 text-danger"></i> {{ $currentLesson->quiz->time_limit_minutes > 0 ? $currentLesson->quiz->time_limit_minutes . ' mins' : 'Untimed' }}</div>
                        </div>

                        <div>
                            <a href="{{ route('student.quiz.take', $currentLesson->quiz) }}" class="btn btn-warning btn-lg px-4 fw-bold">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Launch Assessment Quiz
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Lesson Reading Content -->
                @if($currentLesson->content)
                    <div class="card p-4 border bg-white shadow-sm mb-4">
                        <div class="lesson-article text-dark" style="line-height: 1.8; font-size: 1.05rem;">
                            {!! $currentLesson->sanitized_content !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tabs: Discussions / Q&A -->
            <div class="card border p-4 shadow-sm">
                <h5 class="fw-bold mb-3"><i class="bi bi-chat-square-text-fill text-primary me-2"></i>Lesson Q&A Discussion ({{ $discussions->count() }})</h5>

                <!-- Post Comment Form -->
                <form action="{{ route('student.discussions.store', $currentLesson) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Ask a question or share insight on this lesson..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-send-fill me-1"></i> Post Comment
                        </button>
                    </div>
                </form>

                <!-- Discussions Stream -->
                <div class="list-group list-group-flush">
                    @forelse($discussions as $disc)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex gap-3">
                                <img src="{{ $disc->user?->avatar_url }}" alt="avatar" class="rounded-circle" width="36" height="36">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold small text-dark">{{ $disc->user?->name }}</span>
                                        @if($disc->is_instructor_reply)
                                            <span class="badge bg-success" style="font-size: 0.65rem;">Instructor</span>
                                        @endif
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $disc->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="small text-secondary mb-2">{{ $disc->comment }}</p>

                                    <!-- Replies -->
                                    @if($disc->replies->isNotEmpty())
                                        <div class="ms-4 ps-3 border-start">
                                            @foreach($disc->replies as $reply)
                                                <div class="d-flex gap-2 my-2">
                                                    <img src="{{ $reply->user?->avatar_url }}" alt="avatar" class="rounded-circle" width="26" height="26">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <strong class="small text-dark">{{ $reply->user?->name }}</strong>
                                                            @if($reply->is_instructor_reply)
                                                                <span class="badge bg-success" style="font-size: 0.6rem;">Instructor</span>
                                                            @endif
                                                        </div>
                                                        <p class="small text-secondary mb-0">{{ $reply->comment }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Reply Form Toggle -->
                                    <details class="mt-2">
                                        <summary class="small text-primary" style="cursor: pointer;">Reply</summary>
                                        <form action="{{ route('student.discussions.store', $currentLesson) }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $disc->id }}">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="comment" class="form-control" placeholder="Write a reply..." required>
                                                <button type="submit" class="btn btn-outline-primary">Reply</button>
                                            </div>
                                        </form>
                                    </details>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            No questions yet. Be the first to start the discussion!
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                <h5>No Lessons Found</h5>
                <p class="text-muted small">This course curriculum has no lessons available yet.</p>
            </div>
        @endif
    </div>

    <!-- Curriculum Sidebar (Right) -->
    <div class="player-sidebar">
        <div class="p-3 border-bottom bg-light">
            <h6 class="fw-bold mb-1">Course Curriculum</h6>
            <div class="small text-muted">{{ $course->total_lessons_count }} Lessons • {{ count($completedLessonIds) }} Completed</div>
        </div>

        <div class="curriculum-list">
            @foreach($course->modules as $modIndex => $module)
                <div class="module-header">
                    Module {{ $modIndex + 1 }}: {{ $module->title }}
                </div>
                @foreach($module->lessons as $les)
                    @php
                        $isActive = $currentLesson && $currentLesson->id === $les->id;
                        $isComplete = in_array($les->id, $completedLessonIds);
                    @endphp
                    <a href="{{ route('student.learn', [$course->slug, $les->slug]) }}" class="lesson-item {{ $isActive ? 'active' : '' }} {{ $isComplete ? 'completed' : '' }}">
                        <i class="bi {{ $isComplete ? 'bi-check-circle-fill text-success' : 'bi-circle' }} check-circle"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-1">
                                @if($les->content_type === 'video')
                                    <i class="bi bi-play-circle text-primary" style="font-size: 0.8rem;"></i>
                                @elseif($les->content_type === 'quiz')
                                    <i class="bi bi-question-circle text-warning" style="font-size: 0.8rem;"></i>
                                @else
                                    <i class="bi bi-file-text text-secondary" style="font-size: 0.8rem;"></i>
                                @endif
                                <span>{{ $les->title }}</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">{{ $les->duration_minutes }}m</small>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
@endsection
