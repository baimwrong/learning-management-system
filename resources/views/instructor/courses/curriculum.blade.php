@extends('layouts.instructor')

@section('title', 'Curriculum Builder: ' . $course->title)
@section('page-title', 'Curriculum Studio')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}" class="text-decoration-none">Courses</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($course->title, 30) }}</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">Curriculum Builder</h4>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModuleModal">
            <i class="bi bi-folder-plus me-1"></i> Add New Module
        </button>
        <a href="{{ route('courses.detail', $course->slug) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="bi bi-eye me-1"></i> Public Preview
        </a>
    </div>
</div>

<!-- Modules & Lessons Container -->
<div class="d-flex flex-column gap-4">
    @forelse($course->modules as $modIndex => $module)
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-light py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark">Module {{ $modIndex + 1 }}</span>
                    <h5 class="fw-bold mb-0 text-dark">{{ $module->title }}</h5>
                    @if($module->description)
                        <small class="text-muted ms-2 d-none d-md-inline">— {{ $module->description }}</small>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#addLessonModal{{ $module->id }}">
                        <i class="bi bi-plus-circle me-1"></i> Add Lesson
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModuleModal{{ $module->id }}" title="Edit Module">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('instructor.modules.destroy', $module) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this module and all its lessons?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Module">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($module->lessons as $lessonIndex => $lesson)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted fw-bold small" style="width: 24px;">{{ $lessonIndex + 1 }}.</span>
                                <div>
                                    @if($lesson->content_type === 'video')
                                        <i class="bi bi-play-circle-fill text-primary fs-5 me-2"></i>
                                    @elseif($lesson->content_type === 'quiz')
                                        <i class="bi bi-question-diamond-fill text-warning fs-5 me-2"></i>
                                    @else
                                        <i class="bi bi-file-earmark-text-fill text-secondary fs-5 me-2"></i>
                                    @endif

                                    <span class="fw-semibold text-dark">{{ $lesson->title }}</span>

                                    @if($lesson->is_preview)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-2" style="font-size: 0.65rem;">Free Preview</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-muted border">{{ $lesson->duration_minutes }} mins</span>

                                @if($lesson->content_type === 'quiz' && $lesson->quiz)
                                    <a href="{{ route('instructor.quizzes.builder', $lesson->quiz) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Edit Quiz ({{ $lesson->quiz->questions()->count() }} Questions)
                                    </a>
                                @endif

                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editLessonModal{{ $lesson->id }}" title="Edit Lesson">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('instructor.lessons.destroy', $lesson) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove lesson?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Lesson">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-muted small">
                            No lessons added to this module yet. Click <strong>Add Lesson</strong> above.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Add Lesson Modal for this Module -->
        <div class="modal fade" id="addLessonModal{{ $module->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('instructor.lessons.store', $module) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Add Lesson to: {{ $module->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Lesson Title</label>
                                <input type="text" name="title" class="form-control" required placeholder="e.g. Introduction to Process Models">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Content Type</label>
                                    <select name="content_type" class="form-select" required>
                                        <option value="reading" selected>Reading / Article</option>
                                        <option value="video">Video Lecture</option>
                                        <option value="quiz">Interactive Quiz Assessment</option>
                                        <option value="document">Documentation / Resource</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Duration (Minutes)</label>
                                    <input type="number" min="1" name="duration_minutes" class="form-control" value="15" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Video URL (optional)</label>
                                <input type="text" name="video_url" class="form-control" placeholder="https://www.youtube.com/embed/... or local video mp4 path">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Lesson Article / Content (HTML allowed)</label>
                                <textarea name="content" class="form-control font-monospace" rows="6" placeholder="<p>Write lesson content here...</p>"></textarea>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" name="is_preview" value="1" class="form-check-input" id="prev{{ $module->id }}">
                                <label class="form-check-label small" for="prev{{ $module->id }}">Allow free preview without enrollment</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm px-4">Create Lesson</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Module Modal -->
        <div class="modal fade" id="editModuleModal{{ $module->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('instructor.modules.update', $module) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Module</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Module Title</label>
                                <input type="text" name="title" class="form-control" value="{{ $module->title }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Short Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $module->description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Order Number</label>
                                <input type="number" name="order_number" class="form-control" value="{{ $module->order_number }}" min="1">
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

        <!-- Edit Lesson Modals -->
        @foreach($module->lessons as $lesson)
            <div class="modal fade" id="editLessonModal{{ $lesson->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('instructor.lessons.update', $lesson) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Edit Lesson: {{ $lesson->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Lesson Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ $lesson->title }}" required>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Content Type</label>
                                        <select name="content_type" class="form-select" required>
                                            <option value="reading" {{ $lesson->content_type === 'reading' ? 'selected' : '' }}>Reading / Article</option>
                                            <option value="video" {{ $lesson->content_type === 'video' ? 'selected' : '' }}>Video Lecture</option>
                                            <option value="quiz" {{ $lesson->content_type === 'quiz' ? 'selected' : '' }}>Interactive Quiz Assessment</option>
                                            <option value="document" {{ $lesson->content_type === 'document' ? 'selected' : '' }}>Documentation / Resource</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Duration (Minutes)</label>
                                        <input type="number" min="1" name="duration_minutes" class="form-control" value="{{ $lesson->duration_minutes }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Order Number</label>
                                        <input type="number" min="1" name="order_number" class="form-control" value="{{ $lesson->order_number }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Video URL (optional)</label>
                                    <input type="text" name="video_url" class="form-control" value="{{ $lesson->video_url }}" placeholder="https://www.youtube.com/embed/... or local video mp4 path">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Lesson Article / Content (HTML allowed)</label>
                                    <textarea name="content" class="form-control font-monospace" rows="6">{{ $lesson->content }}</textarea>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="is_preview" value="1" class="form-check-input" id="editPrev{{ $lesson->id }}" {{ $lesson->is_preview ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="editPrev{{ $lesson->id }}">Allow free preview without enrollment</label>
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
        @endforeach
    @empty
        <div class="card p-5 text-center border-0 shadow-sm bg-white">
            <i class="bi bi-folder-plus fs-1 text-muted mb-3 d-block"></i>
            <h4>No Curriculum Modules Created Yet</h4>
            <p class="text-muted small">Organize your course into learning modules or chapters to get started.</p>
            <div>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                    <i class="bi bi-plus-lg me-1"></i> Add First Module
                </button>
            </div>
        </div>
    @endforelse
</div>

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('instructor.modules.store', $course) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Curriculum Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Module Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Module 1: Foundational Database Internals">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Short Description (optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief outline of topics covered in this module"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">Create Module</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
