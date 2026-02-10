<x-app-layout title="Add New Product">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Add New Product</h4>
                            <span class="badge bg-warning text-dark">Local Product</span>
                        </div>

                        <div class="alert alert-info small">
                            <i class="mdi mdi-information-outline"></i> 
                            Products created here are <strong>Local</strong> to your store. You can fully edit them later.
                        </div>

                        <form class="forms-sample" action="{{ route('store.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- NEW: Department Dropdown --}}
                            <div class="form-group mb-3">
                                <label>Department <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_select" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Subcategory</label>
                                    <select name="subcategory_id" id="subcategory_select" class="form-select">
                                        <option value="">Select Category First</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="product_name" class="form-control" placeholder="e.g. Fresh Samosa" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>SKU (Unique Code) <span class="text-danger">*</span></label>
                                    <input type="text" name="sku" class="form-control" placeholder="e.g. STR-001" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-success fw-bold">Selling Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="selling_price" class="form-control border-success fw-bold" placeholder="0.00" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Barcode (Optional)</label>
                                    <input type="text" name="barcode" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Unit</label>
                                    <input type="text" name="unit" class="form-control" placeholder="pcs, kg, box" value="pcs">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Product Image</label>
                                <input type="file" name="image" class="form-control">
                            </div>

                            <div class="form-group mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter product details..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary text-white me-2">Submit</button>
                            <a href="{{ route('store.products.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('category_select').addEventListener('change', function() {
            var catId = this.value;
            var subSelect = document.getElementById('subcategory_select');
            
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
                            subSelect.appendChild(option);
                        });
                        subSelect.disabled = false;
                    } else {
                        subSelect.innerHTML = '<option value="">No subcategories found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    subSelect.innerHTML = '<option value="">Error fetching data</option>';
                });
            } else {
                subSelect.innerHTML = '<option value="">Select Category First</option>';
            }
        });
    </script>
    @endpush
</x-app-layout>