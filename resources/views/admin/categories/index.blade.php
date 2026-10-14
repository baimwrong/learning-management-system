@extends('layouts.admin')

@section('title', 'Categories Management - Learning Management System')
@section('page-title', 'Course Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold text-dark mb-1">Curriculum Classifications</h5>
        <p class="text-muted small mb-0">Organize courses by industry domain and technology tags</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="bi bi-plus-lg me-1"></i> Add Category
    </button>
</div>

<div class="row g-4">
    @forelse($categories as $category)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 p-3" style="background-color: {{ $category->color }}15; color: {{ $category->color }};">
                        <i class="bi {{ $category->icon }} fs-3"></i>
                    </div>
                    <span class="badge bg-light text-dark border">{{ $category->courses_count }} Courses</span>
                </div>

                <h5 class="fw-bold text-dark mb-1">{{ $category->name }}</h5>
                <p class="text-muted small font-monospace mb-2" style="font-size: 0.75rem;">slug: {{ $category->slug }}</p>
                <p class="text-secondary small mb-3 flex-grow-1">{{ $category->description ?? 'No description provided.' }}</p>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}-subtle text-{{ $category->is_active ? 'success' : 'secondary' }}">
                        {{ $category->is_active ? 'Active' : 'Disabled' }}
                    </span>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete category? Associated courses will become uncategorized.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Category: {{ $category->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Category Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Icon Class (Bootstrap Icons)</label>
                                    <input type="text" name="icon" class="form-control" value="{{ $category->icon }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Theme Color</label>
                                    <input type="color" name="color" class="form-control form-control-color w-100" value="{{ $category->color }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="act{{ $category->id }}" {{ $category->is_active ? 'checked' : '' }}>
                                <label class="form-check-label small" for="act{{ $category->id }}">Active in catalog</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm px-3">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-3 border">
                <h5>No Categories Configured</h5>
                <p class="text-muted small">Create categories to classify your course catalog.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Data Science">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Bootstrap Icon</label>
                            <input type="text" name="icon" class="form-control" value="bi-folder" placeholder="bi-folder">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Color Accent</label>
                            <input type="color" name="color" class="form-control form-control-color w-100" value="#2563eb">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief explanation of courses in this topic"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
