@extends('layouts.app')

@section('title', 'Course Catalog - Learning Management System')

@section('content')
<div class="container py-4">
    <!-- Header & Search -->
    <div class="row align-items-center mb-4">
        <div class="col-lg-6 mb-3 mb-lg-0">
            <h2 class="fw-bold mb-1">Course Catalog</h2>
            <p class="text-muted small mb-0">Browse and enroll in high-impact technical curricula</p>
        </div>
        <div class="col-lg-6">
            <form action="{{ route('courses.index') }}" method="GET" class="d-flex gap-2">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('level'))
                    <input type="hidden" name="level" value="{{ request('level') }}">
                @endif
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Search by title, keywords..." value="{{ request('q') }}">
                </div>
                <button type="submit" class="btn btn-primary px-3">Filter</button>
                @if(request()->anyFilled(['q', 'category', 'level', 'price_type', 'sort']))
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm p-3 mb-4 bg-white">
        <div class="row g-2 align-items-center">
            <!-- Categories -->
            <div class="col-md-4">
                <form action="{{ route('courses.index') }}" method="GET" id="filterForm">
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    <div class="input-group input-group-sm">
                        <label class="input-group-text bg-light text-muted small" for="catFilter">Category</label>
                        <select name="category" id="catFilter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->courses_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Level -->
            <div class="col-md-3">
                <form action="{{ route('courses.index') }}" method="GET">
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    <div class="input-group input-group-sm">
                        <label class="input-group-text bg-light text-muted small" for="levelFilter">Level</label>
                        <select name="level" id="levelFilter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all">All Levels</option>
                            <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Price Type -->
            <div class="col-md-2">
                <form action="{{ route('courses.index') }}" method="GET">
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('level')) <input type="hidden" name="level" value="{{ request('level') }}"> @endif
                    <div class="input-group input-group-sm">
                        <label class="input-group-text bg-light text-muted small" for="priceFilter">Price</label>
                        <select name="price_type" id="priceFilter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>Free</option>
                            <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Sort By -->
            <div class="col-md-3">
                <form action="{{ route('courses.index') }}" method="GET">
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('level')) <input type="hidden" name="level" value="{{ request('level') }}"> @endif
                    @if(request('price_type')) <input type="hidden" name="price_type" value="{{ request('price_type') }}"> @endif
                    <div class="input-group input-group-sm">
                        <label class="input-group-text bg-light text-muted small" for="sortFilter">Sort</label>
                        <select name="sort" id="sortFilter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Course Title (A-Z)</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row g-4">
        @forelse($courses as $course)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $course->category?->name ?? 'Engineering' }}
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary badge-level">
                                {{ str_replace('_', ' ', $course->level) }}
                            </span>
                        </div>

                        <h5 class="card-title fw-bold text-dark mb-2">
                            <a href="{{ route('courses.detail', $course->slug) }}" class="text-dark text-decoration-none">
                                {{ $course->title }}
                            </a>
                        </h5>

                        <p class="card-text text-muted small flex-grow-1">
                            {{ Str::limit($course->summary, 90) }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $course->instructor?->avatar_url }}" alt="avatar" class="rounded-circle" width="28" height="28">
                                <small class="text-muted fw-semibold">{{ $course->instructor?->name }}</small>
                            </div>
                            <div class="fw-bold text-primary fs-5">
                                {{ $course->formatted_price }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 border">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                    <h4>No Courses Found</h4>
                    <p class="text-muted">Try adjusting your search criteria or clearing active filters.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm">Clear All Filters</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-5 d-flex justify-content-center">
        {{ $courses->links() }}
    </div>
</div>
@endsection
