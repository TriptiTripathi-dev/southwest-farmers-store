<x-app-layout title="Add New Product">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Fix for Select2 height inside Bootstrap forms */
        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(2.25rem + 2px); /* Standard Bootstrap form-control height */
            padding: 0.375rem 0.75rem;
        }
    </style>
    @endpush

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
                                <select name="department_id" id="department_select" class="form-select select2-enable" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_select" class="form-select select2-enable" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Subcategory</label>
                                    <select name="subcategory_id" id="subcategory_select" class="form-select select2-enable">
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
                                    <label>UPC Code <span class="text-danger">*</span></label>
                                    <input type="text" name="barcode" class="form-control" placeholder="Enter UPC" required>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 on elements with the class 'select2-enable'
            $('.select2-enable').select2({
                theme: 'bootstrap-5',
                width: '100%' // Ensure it takes full width of the container
            });
        });

        // Use jQuery for the change event to ensure compatibility with Select2
        $('#category_select').on('change', function() {
            var catId = this.value;
            var subSelect = $('#subcategory_select'); // jQuery object
            
            subSelect.html('<option value="">Loading...</option>').trigger('change');
            subSelect.prop('disabled', true);

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
                    subSelect.html('<option value="">Select Subcategory</option>');
                    if(data.length > 0) {
                        data.forEach(sub => {
                            subSelect.append(new Option(sub.name, sub.id));
                        });
                        subSelect.prop('disabled', false);
                    } else {
                        subSelect.html('<option value="">No subcategories found</option>');
                    }
                    // Inform Select2 that the DOM has changed
                    subSelect.trigger('change');
                })
                .catch(error => {
                    console.error('Error:', error);
                    subSelect.html('<option value="">Error fetching data</option>').trigger('change');
                });
            } else {
                subSelect.html('<option value="">Select Category First</option>').trigger('change');
            }
        });
    </script>
    @endpush
</x-app-layout>
