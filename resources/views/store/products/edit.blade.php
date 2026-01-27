<x-app-layout title="Edit Product">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Edit Product</h4>
                            @if($product->store_id)
                                <span class="badge bg-warning text-dark">Local Product</span>
                            @else
                                <span class="badge bg-info text-white">Warehouse Product</span>
                            @endif
                        </div>

                        {{-- Alert for Warehouse Products --}}
                        @if(!$product->store_id)
                            <div class="alert alert-warning small">
                                <i class="mdi mdi-lock"></i> 
                                This is a <strong>Warehouse Product</strong>. You can only edit your <strong>Selling Price</strong>. 
                                Name, SKU, and Description are managed by the Admin.
                            </div>
                        @endif

                        <form class="forms-sample" action="{{ route('store.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_select" class="form-select" required {{ !$product->store_id ? 'disabled' : '' }}>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- If disabled, we need a hidden input to submit the value --}}
                                    @if(!$product->store_id)
                                        <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Subcategory</label>
                                    <select name="subcategory_id" id="subcategory_select" class="form-select" {{ !$product->store_id ? 'disabled' : '' }}>
                                        <option value="">Loading...</option>
                                    </select>
                                    @if(!$product->store_id)
                                        <input type="hidden" name="subcategory_id" value="{{ $product->subcategory_id }}">
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Product Name</label>
                                    <input type="text" name="product_name" class="form-control" value="{{ $product->product_name }}" {{ !$product->store_id ? 'readonly' : '' }} required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>SKU</label>
                                    <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" {{ !$product->store_id ? 'readonly' : '' }} required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-success fw-bold">Selling Price ($) <span class="text-danger">*</span></label>
                                    {{-- Note: For Warehouse products, ensure your controller passes the store-specific price if it differs from base price --}}
                                    <input type="number" step="0.01" name="selling_price" class="form-control border-success fw-bold" 
                                           value="{{ $product->price }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Barcode</label>
                                    <input type="text" name="barcode" class="form-control" value="{{ $product->barcode }}" {{ !$product->store_id ? 'readonly' : '' }}>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Unit</label>
                                    <input type="text" name="unit" class="form-control" value="{{ $product->unit }}" {{ !$product->store_id ? 'readonly' : '' }}>
                                </div>
                            </div>

                            @if($product->store_id)
                                <div class="form-group mb-3">
                                    <label>Product Image</label>
                                    <input type="file" name="image" class="form-control">
                                    @if($product->image)
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($product->icon) }}" alt="Current Image" width="60" class="rounded">
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                                </div>
                            @else
                                {{-- Hidden fields to prevent validation errors if these are required in controller --}}
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea class="form-control bg-light" rows="3" readonly>{{ $product->description }}</textarea>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary text-white me-2">Update Changes</button>
                            <a href="{{ route('store.products.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var catSelect = document.getElementById('category_select');
            var subSelect = document.getElementById('subcategory_select');
            
            // Current values from DB
            var currentCatId = "{{ $product->category_id }}";
            var currentSubId = "{{ $product->subcategory_id }}";

            // Function to fetch subcategories
            function fetchSubcategories(catId, selectedSubId = null) {
                subSelect.innerHTML = '<option value="">Loading...</option>';
                subSelect.disabled = true;

                if(catId) {
                    fetch("{{ route('store.subcategories.get') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ category_id: catId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        subSelect.innerHTML = '<option value="">Select Subcategory</option>';
                        if(data.length > 0) {
                            data.forEach(sub => {
                                var option = document.createElement('option');
                                option.value = sub.id;
                                option.text = sub.name;
                                if(selectedSubId && sub.id == selectedSubId) {
                                    option.selected = true;
                                }
                                subSelect.appendChild(option);
                            });
                            // Re-enable only if it's a Local Product
                            @if($product->store_id)
                                subSelect.disabled = false;
                            @endif
                        } else {
                            subSelect.innerHTML = '<option value="">No subcategories found</option>';
                        }
                    })
                    .catch(error => console.error('Error:', error));
                } else {
                    subSelect.innerHTML = '<option value="">Select Category First</option>';
                }
            }

            // 1. Initial Load
            if(currentCatId) {
                fetchSubcategories(currentCatId, currentSubId);
            }

            // 2. Change Event (Only for Local Products)
            @if($product->store_id)
                catSelect.addEventListener('change', function() {
                    fetchSubcategories(this.value);
                });
            @endif
        });
    </script>
    @endpush
</x-app-layout>