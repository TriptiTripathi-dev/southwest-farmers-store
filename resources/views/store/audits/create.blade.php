<x-app-layout title="Start Audit">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .letter-spacing-1 { letter-spacing: 0.5px; }
        
        /* Fix for Select2 height to match Bootstrap sizing */
        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(3.5rem + 2px);
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 0.5rem;
            border: 2px solid #e2e8f0;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.8;
            color: #475569;
        }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    
                    {{-- HEADER SECTION --}}
                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('store.audits.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;" title="Back to Audits">
                            <i class="mdi mdi-arrow-left fs-5 text-dark"></i>
                        </a>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Start New Audit</h4>
                            <p class="text-muted small mb-0 mt-1">Initiate a physical stock count for your inventory</p>
                        </div>
                    </div>

                    {{-- FORM CARD --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 48px; height: 48px;">
                                    <i class="mdi mdi-clipboard-text-outline fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Audit Scope & Details</h6>
                                    <small class="text-muted">Define the sections of the store you want to count.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            {{-- Informational Alert --}}
                            <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info small rounded-3 mb-4 d-flex align-items-start">
                                <i class="mdi mdi-information me-2 fs-5 mt-n1"></i>
                                <div>Leaving the category scope blank will generate a comprehensive audit sheet containing <strong>all active products</strong> in your store.</div>
                            </div>

                            <form action="{{ route('store.audits.store') }}" method="POST">
                                @csrf
                                
                                {{-- Audit Scope Dropdown --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1 mb-2">
                                        <i class="mdi mdi-shape-outline text-primary me-1"></i> Target Category (Optional)
                                    </label>
                                    <select name="category_id" id="categorySelect" class="form-select" style="width: 100%;">
                                        <option value="">-- All Categories (Full Store Audit) --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text mt-2 text-muted">
                                        Select a specific category to perform a localized cycle count.
                                    </div>
                                </div>

                                {{-- Notes Textarea --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1 mb-2">
                                        <i class="mdi mdi-note-text-outline text-primary me-1"></i> Audit Notes
                                    </label>
                                    <textarea name="notes" class="form-control bg-light border-0 shadow-sm p-3" rows="3" 
                                        placeholder="e.g., Routine monthly check, verifying discrepancies in the Frozen section..."></textarea>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex flex-column flex-sm-row gap-3 pt-4 mt-2 border-top">
                                    <a href="{{ route('store.audits.index') }}" class="btn btn-light border rounded-pill px-4 py-3 fw-bold shadow-sm order-2 order-sm-1 text-muted">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-3 fw-bold shadow-sm flex-grow-1 order-1 order-sm-2 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-file-document-check-outline me-2 fs-5"></i> Generate Audit Sheet
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
                placeholder: "Search for a category...",
                allowClear: true
            });
        });
    </script>
    @endpush
</x-app-layout>