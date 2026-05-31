<x-app-layout title="Edit Department">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    
                    {{-- HEADER --}}
                    <div class="d-flex align-items-center mb-4">
                        <a href="{{ route('departments.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;" title="Go Back">
                            <i class="mdi mdi-arrow-left fs-5 text-dark"></i>
                        </a>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Edit Department</h4>
                            <p class="text-muted small mb-0 mt-1">Update local department details</p>
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
                                    <h6 class="mb-0 fw-bold text-dark">Department Details</h6>
                                    <small class="text-muted">Update the information below.</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Department Name <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-format-title"></i></span>
                                        <input type="text" name="name" class="form-control bg-light border-start-0 py-2 fs-6" required value="{{ $department->name }}">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Department Code <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-barcode"></i></span>
                                        <input type="text" name="code" class="form-control bg-light border-start-0 py-2 fs-6" required value="{{ $department->code }}">
                                    </div>
                                </div>

                                {{-- ACTION BUTTONS --}}
                                <div class="d-flex justify-content-end gap-2 pt-4 mt-5 border-top">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center">
                                        <i class="mdi mdi-check-circle-outline me-2"></i> Update Department
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
