<x-app-layout title="Edit Customer">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4 d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Edit Customer</h4>
                    <p class="text-muted small mb-0 mt-1">Update customer information</p>
                </div>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4">
                    <i class="mdi mdi-arrow-left me-2"></i> Back to List
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="mdi mdi-account-edit text-primary me-2"></i>Edit Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-account text-muted"></i>
                                    </span>
                                    <input type="text" name="name" class="form-control border-start-0 ps-0" 
                                           value="{{ old('name', $customer->name) }}" placeholder="Enter customer name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Phone <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-phone text-muted"></i>
                                    </span>
                                    <input type="text" name="phone" class="form-control border-start-0 ps-0" 
                                           value="{{ old('phone', $customer->phone) }}" placeholder="Enter phone number" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Party Type
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-account-group text-muted"></i>
                                    </span>
                                    <select name="party_type" class="form-select border-start-0 ps-0">
                                        <option value="" disabled>Select One</option>
                                        <option value="Retail" {{ old('party_type', $customer->party_type) == 'Retail' ? 'selected' : '' }}>Retail</option>
                                        <option value="Wholesale" {{ old('party_type', $customer->party_type) == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                                        <option value="Distributor" {{ old('party_type', $customer->party_type) == 'Distributor' ? 'selected' : '' }}>Distributor</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Email
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-email text-muted"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0" 
                                           value="{{ old('email', $customer->email) }}" placeholder="Enter email address">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-map-marker text-muted"></i>
                                    </span>
                                    <input type="text" name="address" class="form-control border-start-0 ps-0" 
                                           value="{{ old('address', $customer->address) }}" placeholder="Enter address">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-2">
                                    Due Amount
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="mdi mdi-currency-usd text-muted"></i>
                                    </span>
                                    <input type="number" step="0.01" name="due" class="form-control border-start-0 ps-0" 
                                           value="{{ old('due', $customer->due) }}" placeholder="0.00">
                                </div>
                            </div>

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
                                            <i class="mdi mdi-information-outline"></i> Leave blank to keep current image. Accepted formats: JPG, PNG, GIF (Max: 2MB)
                                        </small>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div class="border rounded-3 p-2 bg-light d-inline-block position-relative">
                                            @php
                                                $imageUrl = $customer->image ? Storage::url($customer->image) : 'https://placehold.co/80x80?text=IMG';
                                            @endphp
                                            <img id="preview" src="{{ $imageUrl }}" class="img-fluid rounded-2" width="80" height="80" alt="Preview">
                                        </div>
                                        <small class="text-muted d-block mt-1">Current</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex gap-3 justify-content-start">
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                                <i class="mdi mdi-close me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                                <i class="mdi mdi-content-save me-2"></i>Update Customer
                            </button>
                        </div>
                    </form>
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