<x-app-layout title="Edit Product">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .letter-spacing-1 { letter-spacing: 0.5px; }

        /* Custom Select2 sizing to match Bootstrap 5 forms */
        .select2-container--bootstrap-5 .select2-selection--single {
            min-height: calc(3rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            border: 0;
            background-color: #f8f9fa; /* bg-light */
        }
        
        /* Disabled state styling for Select2 */
        .select2-container--bootstrap-5.select2-container--disabled .select2-selection--single {
            background-color: #e9ecef;
            opacity: 0.8;
            cursor: not-allowed;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            color: #475569;
            padding-left: 0;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection--single {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
        }

        /* Image Preview Box */
        .image-upload-wrapper {
            border: 2px dashed #cbd5e1;
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s;
            background-color: #f8fafc;
        }
        .image-upload-wrapper:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    
                    {{-- HEADER SECTION --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('store.products.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Go Back">
                                <i class="mdi mdi-arrow-left fs-5 text-dark"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark d-flex align-items-center">
                                    Edit Product
                                    @if($product->store_id)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 ms-3 fs-6 rounded-pill px-3 py-1">
                                            <i class="mdi mdi-storefront me-1"></i> Local Product
                                        </span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 ms-3 fs-6 rounded-pill px-3 py-1">
                                            <i class="mdi mdi-earth me-1"></i> Warehouse Product
                                        </span>
                                    @endif
                                </h4>
                                <p class="text-muted small mb-0 mt-1">Update product details and pricing</p>
                            </div>
                        </div>
                    </div>

                    {{-- ALERT FOR WAREHOUSE PRODUCTS --}}
                    @if(!$product->store_id)
                        <div class="alert alert-warning bg-warning bg-opacity-10 border-warning border-opacity-25 text-dark rounded-4 mb-4 d-flex align-items-center p-3 shadow-sm">
                            <i class="mdi mdi-lock fs-4 me-3 text-warning"></i>
                            <div>
                                This is a <strong>Global Warehouse Product</strong>. You only have permission to edit your local <strong>Selling Price</strong>. Name, SKU, Category, and Description are locked and managed by the central Admin.
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('store.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            {{-- LEFT COLUMN: Primary Details --}}
                            <div class="col-12 col-lg-8">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header bg-white border-bottom p-4">
                                        <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-tag-text-outline text-primary me-2"></i>Primary Details</h6>
                                    </div>
                                    <div class="card-body p-4">
                                        
                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Product Name <span class="text-danger">*</span></label>
                                                <input type="text" name="product_name" class="form-control bg-light border-0 shadow-sm py-3 px-3 fs-6 rounded-3" value="{{ $product->product_name }}" {{ !$product->store_id ? 'readonly' : '' }} required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">SKU (Unique Code) <span class="text-danger">*</span></label>
                                                <input type="text" name="sku" class="form-control bg-light border-0 shadow-sm py-3 px-3 fs-6 rounded-3 font-monospace" value="{{ $product->sku }}" {{ !$product->store_id ? 'readonly' : '' }} required>
                                            </div>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Category <span class="text-danger">*</span></label>
                                                <div class="shadow-sm rounded-3">
                                                    <select name="category_id" id="category_select" class="form-select border-0 bg-light" required {{ !$product->store_id ? 'disabled' : '' }} style="width: 100%;">
                                                        <option value="">Search Category...</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                                {{ $cat->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if(!$product->store_id)
                                                        <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Subcategory</label>
                                                <div class="shadow-sm rounded-3">
                                                    <select name="subcategory_id" id="subcategory_select" class="form-select border-0 bg-light" {{ !$product->store_id ? 'disabled' : '' }} style="width: 100%;">
                                                        <option value="">Loading...</option>
                                                    </select>
                                                    @if(!$product->store_id)
                                                        <input type="hidden" name="subcategory_id" value="{{ $product->subcategory_id }}">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Description</label>
                                            <textarea name="description" class="form-control bg-light border-0 shadow-sm p-3 rounded-3" rows="4" {{ !$product->store_id ? 'readonly' : '' }}>{{ $product->description }}</textarea>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT COLUMN: Pricing & Image --}}
                            <div class="col-12 col-lg-4">
                                <div class="row g-4">
                                    
                                    {{-- Pricing Card --}}
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm rounded-4">
                                            <div class="card-header bg-white border-bottom p-4">
                                                <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-cash-multiple text-success me-2"></i>Pricing & Inventory</h6>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold text-success small text-uppercase letter-spacing-1">Selling Price ($) <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                                        <span class="input-group-text bg-success bg-opacity-10 border-0 text-success fw-bold px-3">$</span>
                                                        <input type="number" step="0.01" name="selling_price" class="form-control bg-light border-0 py-2 fs-5 fw-bold text-dark" value="{{ $product->price }}" required>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Unit</label>
                                                    <input type="text" name="unit" class="form-control bg-light border-0 shadow-sm py-2 px-3 rounded-3" value="{{ $product->unit }}" {{ !$product->store_id ? 'readonly' : '' }}>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">UPC Code</label>
                                                    <input type="text" name="barcode" class="form-control bg-light border-0 shadow-sm py-2 px-3 rounded-3 font-monospace" value="{{ $product->barcode }}" {{ !$product->store_id ? 'readonly' : '' }} {{ $product->store_id ? 'required' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Media Card (Only for Local Products) --}}
                                    @if($product->store_id)
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm rounded-4">
                                            <div class="card-header bg-white border-bottom p-4">
                                                <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-image-outline text-info me-2"></i>Product Media</h6>
                                            </div>
                                            <div class="card-body p-4">
                                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1 mb-3">Update Image</label>
                                                
                                                <div class="image-upload-wrapper mb-2 position-relative">
                                                    @if($product->image)
                                                        <img id="imagePreview" src="{{ Storage::url($product->image) }}" alt="Preview" class="img-fluid rounded-3 mb-3" style="max-height: 180px; object-fit: contain;">
                                                        <div id="uploadPrompt" class="d-none">
                                                            <i class="mdi mdi-cloud-upload-outline display-4 text-muted opacity-50"></i>
                                                            <p class="text-muted small mt-2 mb-0">Click or drag image to update</p>
                                                        </div>
                                                    @else
                                                        <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded-3 mb-3 d-none" style="max-height: 180px; object-fit: contain;">
                                                        <div id="uploadPrompt">
                                                            <i class="mdi mdi-cloud-upload-outline display-4 text-muted opacity-50"></i>
                                                            <p class="text-muted small mt-2 mb-0">Click or drag image to upload</p>
                                                        </div>
                                                    @endif
                                                    
                                                    <input type="file" name="image" id="imageInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </div>

                        </div>

                        {{-- BOTTOM ACTION BAR --}}
                        <div class="card border-0 shadow-sm rounded-4 mt-4">
                            <div class="card-body p-4 d-flex flex-column flex-sm-row justify-content-end gap-3">
                                <a href="{{ route('store.products.index') }}" class="btn btn-light border rounded-pill px-5 py-3 fw-bold shadow-sm order-2 order-sm-1 text-muted">
                                    Cancel
                                </a>
                                @if(Auth::user()->hasPermission('edit_product'))
                                <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm order-1 order-sm-2 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-content-save-outline me-2 fs-5"></i> Update Product
                                </button>
                                @endif
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Searchable Dropdown for Category
            $('#category_select').select2({
                theme: 'bootstrap-5',
                placeholder: "Search for a category...",
                allowClear: true
            });

            // Initialize Subcategory Select2
            $('#subcategory_select').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity // hides search box if options are few
            });

            // AJAX Variables
            var catSelect = $('#category_select');
            var subSelect = $('#subcategory_select');
            var currentCatId = "{{ $product->category_id }}";
            var currentSubId = "{{ $product->subcategory_id }}";

            // Function to fetch subcategories
            function fetchSubcategories(catId, selectedSubId = null) {
                subSelect.empty().append('<option value="">Loading...</option>').trigger('change');
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
                        subSelect.empty().append('<option value="">Select Subcategory</option>');
                        if(data.length > 0) {
                            data.forEach(sub => {
                                var selected = (selectedSubId && sub.id == selectedSubId) ? true : false;
                                var option = new Option(sub.name, sub.id, selected, selected);
                                subSelect.append(option);
                            });
                            
                            // Re-enable only if it's a Local Product
                            @if($product->store_id)
                                subSelect.prop('disabled', false);
                            @endif
                            
                            // Enable search box since we have data
                            subSelect.select2({ theme: 'bootstrap-5' }); 
                        } else {
                            subSelect.empty().append('<option value="">No subcategories found</option>');
                        }
                        subSelect.trigger('change');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        subSelect.empty().append('<option value="">Error fetching data</option>').trigger('change');
                    });
                } else {
                    subSelect.empty().append('<option value="">Select Category First</option>').trigger('change');
                }
            }

            // 1. Initial Load
            if(currentCatId) {
                fetchSubcategories(currentCatId, currentSubId);
            }

            // 2. Change Event (Only for Local Products)
            @if($product->store_id)
                catSelect.on('change', function() {
                    fetchSubcategories(this.value);
                });
            @endif

            // Live Image Preview Logic
            @if($product->store_id)
            document.getElementById('imageInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('imagePreview');
                const prompt = document.getElementById('uploadPrompt');
                
                if (file) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('d-none');
                    prompt.classList.add('d-none');
                }
            });
            @endif
        });
    </script>
    @endpush
</x-app-layout>
