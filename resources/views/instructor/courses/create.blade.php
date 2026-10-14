@extends('layouts.instructor')

@section('title', 'Author New Course - Instructor Studio')
@section('page-title', 'Create New Course')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <h4 class="fw-bold mb-1">Course Fundamentals</h4>
            <p class="text-muted small mb-4">Set up course details, classification, prerequisites, and learning outcomes.</p>

            <form action="{{ route('instructor.courses.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold small">Course Title</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="e.g. Distributed Database Engineering with PostgreSQL 18">
                    @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-semibold small">Category</label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="level" class="form-label fw-semibold small">Difficulty Level</label>
                        <select name="level" id="level" class="form-select" required>
                            <option value="all_levels">All Levels</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate" selected>Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="price" class="form-label fw-semibold small">Tuition / Price ($)</label>
                        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" value="{{ old('price', '0.00') }}" required>
                        <span class="text-muted" style="font-size: 0.72rem;">Set 0 for free enrollment</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label fw-semibold small">Course Summary (1-2 sentences)</label>
                    <textarea name="summary" id="summary" rows="2" class="form-control @error('summary') is-invalid @enderror" required placeholder="A brief teaser describing the primary purpose and takeaway of this curriculum...">{{ old('summary') }}</textarea>
                    @error('summary') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold small">In-Depth Description</label>
                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" required placeholder="Detailed curriculum breakdown, technical background, and course goals...">{{ old('description') }}</textarea>
                    @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="requirements" class="form-label fw-semibold small">Prerequisites / Requirements (One per line)</label>
                        <textarea name="requirements" id="requirements" rows="3" class="form-control" placeholder="Basic SQL knowledge&#10;Familiarity with CLI environments">{{ old('requirements') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="learning_outcomes" class="form-label fw-semibold small">What You Will Master (One per line)</label>
                        <textarea name="learning_outcomes" id="learning_outcomes" rows="3" class="form-control" placeholder="Design high-throughput JSONB schemas&#10;Configure zero-downtime replication">{{ old('learning_outcomes') }}</textarea>
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is_published" {{ old('is_published') ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="is_published">
                        Publish course immediately (visible in public catalog)
                    </label>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success px-4">
                        Save Course & Build Curriculum <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
