<x-app-layout title="Edit Subcategory">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .letter-spacing-1 { letter-spacing: 0.5px; }

        /* Fix for Select2 height inside Bootstrap 5 Input Group */
        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(3rem + 2px);
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border: 0;
            background-color: #f8f9fa;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            color: #475569;
            padding-left: 0;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection--single {
            box-shadow: none !important;
            border: 0;
        }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    
                    {{-- HEADER SECTION --}}
                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('store.subcategories.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;" title="Go Back">
                            <i class="mdi mdi-arrow-left fs-5 text-dark"></i>
                        </a>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Edit Subcategory</h4>
                            <p class="text-muted small mb-0 mt-1">Update details for this local store subcategory</p>
                        </div>
                    </div>

                    {{-- FORM CARD --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="mdi mdi-pencil text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Subcategory Details</h6>
                                    <small class="text-muted">Modify the required information below.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            {{-- Info Alert --}}
                            <div class="alert alert-warning bg-warning bg-opacity-10 border-warning border-opacity-25 text-dark small rounded-3 mb-4 d-flex align-items-start">
                                <i class="mdi mdi-alert-circle-outline me-2 fs-5 mt-n1 text-warning"></i>
                                <div>You are editing a <strong>Local (Store)</strong> subcategory. Changes made here will only reflect in your store's inventory.</div>
                            </div>

                            <form class="forms-sample" action="{{ route('store.subcategories.update', $subcategory->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                {{-- Parent Category Dropdown (Select2) --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Parent Category <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden d-flex flex-nowrap">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-sitemap"></i></span>
                                        <div class="flex-grow-1 bg-light">
                                            <select name="category_id" id="categorySelect" class="form-select border-0 bg-transparent" required style="width: 100%;">
                                                <option value="">Select Category...</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ $subcategory->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Subcategory Name --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Subcategory Name <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-format-title"></i></span>
                                        <input type="text" name="name" class="form-control bg-light border-start-0 py-2 fs-6" value="{{ $subcategory->name }}" required placeholder="e.g. Smartphones, T-Shirts">
                                    </div>
                                </div>
                                
                                {{-- Subcategory Code --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Subcategory Code <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-barcode"></i></span>
                                        <input type="text" name="code" class="form-control bg-light border-start-0 py-2 fs-6" value="{{ $subcategory->code }}" required placeholder="e.g. SMRT-PHN">
                                    </div>
                                    <div class="form-text mt-2"><i class="mdi mdi-help-circle-outline me-1"></i>A unique short code to identify this subcategory.</div>
                                </div>

                                {{-- ACTION BUTTONS --}}
                                <div class="d-flex justify-content-end gap-2 pt-4 mt-5 border-top">
                                    <a href="{{ route('store.subcategories.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center">
                                        <i class="mdi mdi-content-save-outline me-2"></i> Update Subcategory
                                    </button>
                                </div>
                            </form>
                        </div>
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
            // Initialize Searchable Dropdown
            $('#categorySelect').select2({
                theme: 'bootstrap-5',
                placeholder: "Search for a parent category...",
                allowClear: true
            });
        });
    </script>
    @endpush
</x-app-layout>