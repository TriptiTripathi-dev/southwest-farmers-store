<x-app-layout title="Edit Menu Item">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="mb-4">
                <a href="{{ route('menu-items.index') }}" class="btn btn-link text-muted p-0 text-decoration-none small">
                    <i class="mdi mdi-arrow-left"></i> Back to Menu Items
                </a>
                <h4 class="fw-bold text-dark mt-2">Edit Menu Item</h4>
            </div>

            <div class="card border-0 shadow-sm rounded-4 col-lg-8">
                <div class="card-body p-4">
                    <form action="{{ route('menu-items.update', $menuItem->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Menu Category <span class="text-danger">*</span></label>
                            <select name="menu_category_id" class="form-select bg-light border-0 shadow-sm py-2" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('menu_category_id', $menuItem->menu_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('menu_category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light border-0 shadow-sm py-2" value="{{ old('name', $menuItem->name) }}" required>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" class="form-control bg-light border-0 shadow-sm py-2" value="{{ old('price', $menuItem->price) }}" required>
                            @error('price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea name="description" class="form-control bg-light border-0 shadow-sm py-2" rows="4">{{ old('description', $menuItem->description) }}</textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Dish Image</label>
                            @if($menuItem->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::disk('r2')->url($menuItem->image) }}" alt="Dish Image" style="max-height: 100px; border-radius: 8px;">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control bg-light border-0 shadow-sm py-2" accept="image/*">
                            @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">Status</label>
                            <select name="is_active" class="form-select bg-light border-0 shadow-sm py-2">
                                <option value="1" {{ old('is_active', $menuItem->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $menuItem->is_active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('menu-items.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Update Menu Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
