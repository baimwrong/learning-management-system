@extends('layouts.instructor')

@section('title', 'Edit Course: ' . $course->title . ' - Instructor Studio')
@section('page-title', 'Edit Course Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Edit Course</h4>
                    <p class="text-muted small mb-0">{{ $course->title }}</p>
                </div>
                <a href="{{ route('instructor.courses.curriculum', $course) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-list-nested me-1"></i> Edit Curriculum
                </a>
            </div>

            <form action="{{ route('instructor.courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold small">Course Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $course->title) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-semibold small">Category</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $course->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="level" class="form-label fw-semibold small">Difficulty Level</label>
                        <select name="level" id="level" class="form-select" required>
                            <option value="all_levels" {{ $course->level == 'all_levels' ? 'selected' : '' }}>All Levels</option>
                            <option value="beginner" {{ $course->level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ $course->level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ $course->level == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="price" class="form-label fw-semibold small">Price ($)</label>
                        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" value="{{ old('price', $course->price) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label fw-semibold small">Course Summary</label>
                    <textarea name="summary" id="summary" rows="2" class="form-control" required>{{ old('summary', $course->summary) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold small">Description</label>
                    <textarea name="description" id="description" rows="5" class="form-control" required>{{ old('description', $course->description) }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="requirements" class="form-label fw-semibold small">Prerequisites (One per line)</label>
                        <textarea name="requirements" id="requirements" rows="3" class="form-control">{{ old('requirements', is_array($course->requirements) ? implode("\n", $course->requirements) : '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="learning_outcomes" class="form-label fw-semibold small">Learning Outcomes (One per line)</label>
                        <textarea name="learning_outcomes" id="learning_outcomes" rows="3" class="form-control">{{ old('learning_outcomes', is_array($course->learning_outcomes) ? implode("\n", $course->learning_outcomes) : '') }}</textarea>
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is_published" {{ old('is_published', $course->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="is_published">
                        Published (visible to students)
                    </label>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-outline-secondary">Back to List</a>
                    <button type="submit" class="btn btn-success px-4">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
