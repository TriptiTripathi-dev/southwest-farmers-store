<x-app-layout title="Create Menu Item">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="mb-4">
                <a href="{{ route('menu-items.index') }}" class="btn btn-link text-muted p-0 text-decoration-none small">
                    <i class="mdi mdi-arrow-left"></i> Back to Menu Items
                </a>
                <h4 class="fw-bold text-dark mt-2">Create Menu Item</h4>
            </div>

            <div class="card border-0 shadow-sm rounded-4 col-lg-8">
                <div class="card-body p-4">
                    <form action="{{ route('menu-items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Menu Category <span class="text-danger">*</span></label>
                            <select name="menu_category_id" class="form-select bg-light border-0 shadow-sm py-2" required>
                                <option value="">Select a Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('menu_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('menu_category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light border-0 shadow-sm py-2" placeholder="e.g. Jollof Rice (Large)" value="{{ old('name') }}" required>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" class="form-control bg-light border-0 shadow-sm py-2" placeholder="0.00" value="{{ old('price') }}" required>
                            @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea name="description" class="form-control bg-light border-0 shadow-sm py-2" rows="4" placeholder="Briefly describe the ingredients, size, spice level...">{{ old('description') }}</textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Dish Image</label>
                            <input type="file" name="image" class="form-control bg-light border-0 shadow-sm py-2" accept="image/*">
                            @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('menu-items.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save Menu Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
