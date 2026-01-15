<x-app-layout title="Add Staff">
    <div class="content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="py-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Add New Staff</h4>
                    <p class="text-muted mb-0 mt-1 small">Create a new staff member account</p>
                </div>
                <a href="{{ route('staff.index') }}" class="btn btn-light border">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('staff.store') }}" method="POST">
                        @csrf
                        
                        <!-- Personal Information Card -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="mdi mdi-account-circle text-primary me-2"></i>Personal Information
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-account text-muted"></i>
                                            </span>
                                            <input type="text" 
                                                   name="name" 
                                                   class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                                   value="{{ old('name') }}"
                                                   required 
                                                   placeholder="e.g. John Doe">
                                        </div>
                                        @error('name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-email text-muted"></i>
                                            </span>
                                            <input type="email" 
                                                   name="email" 
                                                   class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                                   value="{{ old('email') }}"
                                                   required 
                                                   placeholder="john@example.com">
                                        </div>
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            Phone Number
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-phone text-muted"></i>
                                            </span>
                                            <input type="text" 
                                                   name="phone" 
                                                   class="form-control border-start-0 @error('phone') is-invalid @enderror" 
                                                   value="{{ old('phone') }}"
                                                   placeholder="+1234567890">
                                        </div>
                                        @error('phone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role & Security Card -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="mdi mdi-shield-account text-primary me-2"></i>Role & Security
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold">
                                            Assign Role <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-shield-check text-muted"></i>
                                            </span>
                                            <select name="role_id" 
                                                    class="form-select border-start-0 @error('role_id') is-invalid @enderror" 
                                                    required>
                                                <option value="">-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('role_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Select the appropriate role for this staff member</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-lock text-muted"></i>
                                            </span>
                                            <input type="password" 
                                                   name="password" 
                                                   class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                                   required 
                                                   placeholder="********"
                                                   id="password">
                                            <button class="btn btn-light border border-start-0" 
                                                    type="button" 
                                                    id="togglePassword">
                                                <i class="mdi mdi-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            Confirm Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-lock-check text-muted"></i>
                                            </span>
                                            <input type="password" 
                                                   name="password_confirmation" 
                                                   class="form-control border-start-0" 
                                                   required 
                                                   placeholder="********">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="mdi mdi-toggle-switch text-primary me-2"></i>Account Status
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="is_active" 
                                           id="isActive" 
                                           value="1" 
                                           checked>
                                    <label class="form-check-label fw-semibold" for="isActive">
                                        Active Account
                                    </label>
                                    <div class="text-muted small mt-1">
                                        Enable this to allow the staff member to login immediately
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <a href="{{ route('staff.index') }}" class="btn btn-light border px-4">
                                <i class="mdi mdi-close me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="mdi mdi-check-circle me-1"></i> Create Staff Member
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Help Card Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="mdi mdi-information text-primary me-1"></i> Quick Guide
                            </h6>
                            
                            <div class="mb-3">
                                <h6 class="fw-semibold small mb-2">
                                    <i class="mdi mdi-circle-small text-primary"></i> Required Fields
                                </h6>
                                <p class="small text-muted mb-0">
                                    Fields marked with <span class="text-danger">*</span> are mandatory and must be filled.
                                </p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-semibold small mb-2">
                                    <i class="mdi mdi-circle-small text-primary"></i> Role Assignment
                                </h6>
                                <p class="small text-muted mb-0">
                                    Assign the appropriate role based on the staff member's responsibilities.
                                </p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-semibold small mb-2">
                                    <i class="mdi mdi-circle-small text-primary"></i> Password Security
                                </h6>
                                <p class="small text-muted mb-0">
                                    Create a strong password with at least 8 characters. The staff member can change it later.
                                </p>
                            </div>

                            <div class="mb-0">
                                <h6 class="fw-semibold small mb-2">
                                    <i class="mdi mdi-circle-small text-primary"></i> Account Status
                                </h6>
                                <p class="small text-muted mb-0">
                                    You can activate or deactivate the account anytime from the staff list.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Info Card -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="mdi mdi-shield-account text-info me-1"></i> Available Roles
                            </h6>
                            @foreach ($roles as $role)
                                <div class="d-flex align-items-start mb-2">
                                    <span class="badge bg-info bg-opacity-10 text-info me-2 mt-1">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword')?.addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('mdi-eye');
                icon.classList.add('mdi-eye-off');
            } else {
                password.type = 'password';
                icon.classList.remove('mdi-eye-off');
                icon.classList.add('mdi-eye');
            }
        });
    </script>
    @endpush
</x-app-layout>