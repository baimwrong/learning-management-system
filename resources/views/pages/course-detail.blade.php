@extends('layouts.app')

@section('title', $course->title . ' - Learning Management System')

@section('content')
<!-- Course Header Banner -->
<section class="bg-dark text-white py-5">
    <div class="container py-2">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-light text-decoration-none opacity-75">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-light text-decoration-none opacity-75">Courses</a></li>
                        <li class="breadcrumb-item active text-light" aria-current="page">{{ $course->category?->name ?? 'Course' }}</li>
                    </ol>
                </nav>

                <h1 class="fw-bold mb-3">{{ $course->title }}</h1>
                <p class="lead opacity-90 mb-4">{{ $course->summary }}</p>

                <div class="d-flex flex-wrap align-items-center gap-4 text-light small">
                    <div>
                        <i class="bi bi-person-fill me-1 text-primary"></i>
                        Instructor: <span class="fw-semibold text-white">{{ $course->instructor?->name }}</span>
                    </div>
                    <div>
                        <i class="bi bi-clock me-1 text-warning"></i>
                        Total Duration: <span class="fw-semibold text-white">{{ $course->formatted_duration }}</span>
                    </div>
                    <div>
                        <i class="bi bi-journal-text me-1 text-info"></i>
                        Lessons: <span class="fw-semibold text-white">{{ $course->total_lessons_count }}</span>
                    </div>
                    <div>
                        <i class="bi bi-bar-chart-fill me-1 text-success"></i>
                        Level: <span class="fw-semibold text-white text-capitalize">{{ str_replace('_', ' ', $course->level) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-lg p-3 text-dark">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="rounded-3 mb-3" style="width: 100%; height: 180px; object-fit: cover;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-3 fw-bold text-primary">{{ $course->formatted_price }}</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">Verified Certificate</span>
                    </div>

                    @auth
                        @if($isEnrolled)
                            <div class="alert alert-info py-2 mb-3 small d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                <div>You are currently enrolled. Progress: <strong>{{ $enrollment->progress_percentage }}%</strong></div>
                            </div>
                            <a href="{{ route('student.learn', $course->slug) }}" class="btn btn-success btn-lg w-100 fw-bold">
                                <i class="bi bi-play-circle-fill me-1"></i> Continue Learning
                            </a>
                        @else
                            <form action="{{ route('student.enroll', $course) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                    <i class="bi bi-mortarboard-fill me-1"></i> Enroll In Course Now
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 fw-bold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Enroll
                        </a>
                    @endauth

                    <ul class="list-unstyled small mt-4 mb-0 text-muted">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Full lifetime access in local intranet</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Includes automated quiz assessments</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Downloadable certificate on 100% completion</li>
                        <li class="mb-0"><i class="bi bi-check2 text-success me-2"></i>Interactive lesson Q&A discussion</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Details & Curriculum Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <!-- What You'll Learn -->
                @if(!empty($course->learning_outcomes) && is_array($course->learning_outcomes))
                    <div class="card p-4 mb-4 border">
                        <h4 class="fw-bold mb-3"><i class="bi bi-award-fill text-primary me-2"></i>What You Will Master</h4>
                        <div class="row g-2">
                            @foreach($course->learning_outcomes as $outcome)
                                <div class="col-md-6 d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                    <span class="small">{{ $outcome }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Course Description -->
                <div class="card p-4 mb-4 border">
                    <h4 class="fw-bold mb-3">Course Description</h4>
                    <div class="text-secondary" style="line-height: 1.8;">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                </div>

                <!-- Course Curriculum Accordion -->
                <div class="card p-4 mb-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0">Curriculum Outline</h4>
                        <span class="text-muted small">{{ $course->modules->count() }} Modules • {{ $course->total_lessons_count }} Lessons</span>
                    </div>

                    <div class="accordion" id="curriculumAccordion">
                        @forelse($course->modules as $modIndex => $module)
                            <div class="accordion-item mb-2 border rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="heading{{ $module->id }}">
                                    <button class="accordion-button {{ $modIndex > 0 ? 'collapsed' : '' }} bg-light fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $module->id }}" aria-expanded="{{ $modIndex === 0 ? 'true' : 'false' }}">
                                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                            <span>{{ $module->title }}</span>
                                            <span class="badge bg-white text-secondary border small">{{ $module->lessons->count() }} Lessons</span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $module->id }}" class="accordion-collapse collapse {{ $modIndex === 0 ? 'show' : '' }}" data-bs-parent="#curriculumAccordion">
                                    <div class="accordion-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @foreach($module->lessons as $lesson)
                                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($lesson->content_type === 'video')
                                                            <i class="bi bi-play-circle-fill text-primary"></i>
                                                        @elseif($lesson->content_type === 'quiz')
                                                            <i class="bi bi-question-circle-fill text-warning"></i>
                                                        @else
                                                            <i class="bi bi-file-text-fill text-secondary"></i>
                                                        @endif
                                                        <span class="small fw-semibold">{{ $lesson->title }}</span>
                                                        @if($lesson->is_preview)
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">Preview</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-muted small">{{ $lesson->duration_minutes }}m</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted text-center py-4">Curriculum outline is currently being assembled by the instructor.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Instructor Card -->
                <div class="card p-4 mb-4 border">
                    <h4 class="fw-bold mb-3">Meet Your Instructor</h4>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $course->instructor?->avatar_url }}" alt="avatar" class="rounded-circle border" width="64" height="64">
                        <div>
                            <h5 class="fw-bold mb-0">{{ $course->instructor?->name }}</h5>
                            <span class="text-muted small">Course Author & Engineering Specialist</span>
                        </div>
                    </div>
                    <p class="text-secondary small mb-0">{{ $course->instructor?->bio }}</p>
                </div>

                <!-- Reviews and Ratings -->
                <div class="card p-4 mb-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold mb-0">Student Reviews</h4>
                        <div class="rating-stars fw-bold">
                            <i class="bi bi-star-fill"></i> {{ $course->average_rating }} / 5.0 ({{ $course->reviews->count() }} ratings)
                        </div>
                    </div>

                    @auth
                        @if($isEnrolled)
                            <div class="p-3 bg-light rounded-3 mb-4">
                                <h6 class="fw-bold mb-2">Leave Your Course Review</h6>
                                <form action="{{ route('student.reviews.store', $course) }}" method="POST">
                                    @csrf
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-3">
                                            <select name="rating" class="form-select form-select-sm" required>
                                                <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                                                <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                                                <option value="3">⭐⭐⭐ (3 Stars)</option>
                                                <option value="2">⭐⭐ (2 Stars)</option>
                                                <option value="1">⭐ (1 Star)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="review" class="form-control form-control-sm" placeholder="Write a brief review of your learning experience...">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                                </form>
                            </div>
                        @endif
                    @endauth

                    <div class="list-group list-group-flush">
                        @forelse($course->reviews as $rev)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold small">{{ $rev->user->name }}</div>
                                    <div class="rating-stars small">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $rev->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-secondary small mb-0">{{ $rev->review }}</p>
                            </div>
                        @empty
                            <div class="text-muted small py-2">No reviews submitted yet. Be the first to complete this course and leave feedback!</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Related Courses Sidebar -->
            <div class="col-lg-4">
                <div class="card p-4 border shadow-sm">
                    <h5 class="fw-bold mb-3">Related Courses</h5>
                    <div class="d-flex flex-column gap-3">
                        @forelse($relatedCourses as $rel)
                            <div class="d-flex gap-2 align-items-center">
                                <img src="{{ $rel->thumbnail_url }}" alt="{{ $rel->title }}" class="rounded-2" width="70" height="50" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-1" style="font-size: 0.88rem;">
                                        <a href="{{ route('courses.detail', $rel->slug) }}" class="text-dark text-decoration-none fw-semibold">
                                            {{ Str::limit($rel->title, 40) }}
                                        </a>
                                    </h6>
                                    <span class="text-primary fw-bold small">{{ $rel->formatted_price }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No other courses in this category yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
