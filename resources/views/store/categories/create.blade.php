<x-app-layout title="Create Category">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    
                    {{-- HEADER --}}
                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('store.categories.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;" title="Go Back">
                            <i class="mdi mdi-arrow-left fs-5 text-dark"></i>
                        </a>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Add New Category</h4>
                            <p class="text-muted small mb-0 mt-1">Create a new local category for your store</p>
                        </div>
                    </div>

                    {{-- FORM CARD --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="mdi mdi-shape-plus text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Category Details</h6>
                                    <small class="text-muted">Fill in the required information below.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            
                            {{-- Info Alert --}}
                            <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info small rounded-3 mb-4 d-flex align-items-start">
                                <i class="mdi mdi-information me-2 fs-5 mt-n1"></i>
                                <div>Categories created here will be designated as <strong>Local (Store)</strong> categories and will not affect the global warehouse catalog.</div>
                            </div>

                            <form action="{{ route('store.categories.store') }}" method="POST">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Category Name <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-format-title"></i></span>
                                        <input type="text" name="name" class="form-control bg-light border-start-0 py-2 fs-6" required placeholder="e.g. Beverages, Electronics">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Category Code <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-barcode"></i></span>
                                        <input type="text" name="code" class="form-control bg-light border-start-0 py-2 fs-6" required placeholder="e.g. BEV-01, ELEC-100">
                                    </div>
                                    <div class="form-text mt-2"><i class="mdi mdi-help-circle-outline me-1"></i>A unique short code to identify this category.</div>
                                </div>

                                {{-- ACTION BUTTONS --}}
                                <div class="d-flex justify-content-end gap-2 pt-4 mt-5 border-top">
                                   
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center">
                                        <i class="mdi mdi-check-circle-outline me-2"></i> Save Category
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>