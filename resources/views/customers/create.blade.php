<x-app-layout title="Add Customer">
    <div class="content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="py-4 d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Add New Customer</h4>
                    <p class="text-muted small mb-0 mt-1">Fill in the details to create a new customer</p>
                </div>
                <a href="{{ route('customers.index') }}" class="btn btn-success shadow-sm rounded-pill px-4">
                    <i class="mdi mdi-format-list-bulleted me-2"></i> Customer List
                </a>
            </div>

            <!-- Main Form Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="mdi mdi-account-plus text-success me-2"></i>Customer Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Name Field -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-account text-muted"></i>
                                    </span>
                                    <input type="text" name="name" class="form-control border-start-0 ps-0" placeholder="Enter customer name" required>
                                </div>
                            </div>

                            <!-- Phone Field -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Phone <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-phone text-muted"></i>
                                    </span>
                                    <input type="text" name="phone" class="form-control border-start-0 ps-0" placeholder="Enter phone number" required>
                                </div>
                            </div>

                          

                            <!-- Email Field -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Email
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-email text-muted"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="Enter email address">
                                </div>
                            </div>

                            <!-- Address Field -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-map-marker text-muted"></i>
                                    </span>
                                    <input type="text" name="address" class="form-control border-start-0 ps-0" placeholder="Enter address">
                                </div>
                            </div>

                            <!-- Due Field -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Due Amount
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-currency-usd text-muted"></i>
                                    </span>
                                    <input type="number" step="0.01" name="due" class="form-control border-start-0 ps-0" placeholder="0.00">
                                </div>
                            </div>

                            <!-- Image Upload Field -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Customer Image
                                </label>
                                <div class="row align-items-center">
                                    <div class="col-md-10">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-camera text-muted"></i>
                                            </span>
                                            <input type="file" name="image" class="form-control border-start-0 ps-0" id="customerImage" onchange="previewImage(this)">
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="mdi mdi-information-outline"></i> Accepted formats: JPG, PNG, GIF (Max: 2MB)
                                        </small>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="border rounded-3 p-2 bg-light d-inline-block">
                                            <img id="preview" src="https://placehold.co/80x80?text=IMG" class="img-fluid rounded-2" width="80" height="80" alt="Preview">
                                        </div>
                                        <small class="text-muted d-block mt-1">Preview</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-5 pt-4 border-top d-flex gap-3 justify-content-start">
                            <button type="reset" class="btn btn-outline-secondary px-4 rounded-pill">
                                <i class="mdi mdi-refresh me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-success px-5 rounded-pill shadow-sm">
                                <i class="mdi mdi-check-circle me-2"></i>Save Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card (Optional) -->
            <div class="card border-0 shadow-sm rounded-3 mt-4 bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="mdi mdi-lightbulb-on-outline text-warning fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-muted small">
                                <strong>Tip:</strong> Fields marked with <span class="text-danger">*</span> are required. Make sure to fill them before submitting.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-app-layout>