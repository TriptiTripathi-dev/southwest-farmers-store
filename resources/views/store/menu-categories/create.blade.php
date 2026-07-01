<x-app-layout title="Create Menu Category">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="mb-4">
                <a href="{{ route('menu-categories.index') }}" class="btn btn-link text-muted p-0 text-decoration-none small">
                    <i class="mdi mdi-arrow-left"></i> Back to Categories
                </a>
                <h4 class="fw-bold text-dark mt-2">Create Menu Category</h4>
            </div>

            <div class="card border-0 shadow-sm rounded-4 col-lg-8">
                <div class="card-body p-4">
                    <form action="{{ route('menu-categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light border-0 shadow-sm py-2" placeholder="e.g. Appetizers, Soups, Stews" value="{{ old('name') }}" required>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea name="description" class="form-control bg-light border-0 shadow-sm py-2" rows="4" placeholder="Briefly describe the category...">{{ old('description') }}</textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Category Image</label>
                            <input type="file" name="image" class="form-control bg-light border-0 shadow-sm py-2" accept="image/*">
                            @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('menu-categories.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
