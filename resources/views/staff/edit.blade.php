<x-app-layout title="Edit Staff">
    <div class="content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="py-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Edit Staff Member</h4>
                    <p class="text-muted mb-0 mt-1 small">Update staff member information</p>
                </div>
                <a href="{{ route('staff.index') }}" class="btn btn-light border">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('staff.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Staff Info Card -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                        <span class="text-primary fw-bold h5 mb-0">{{ strtoupper(substr($staff->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-semibold">{{ $staff->name }}</h5>
                                        <small class="text-muted">Staff ID: #{{ str_pad($staff->id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                   value="{{ old('name', $staff->name) }}"
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
                                                   value="{{ old('email', $staff->email) }}"
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
                                                   value="{{ old('phone', $staff->phone) }}"
                                                   placeholder="+1234567890">
                                        </div>
                                        @error('phone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role & Status Card -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="mdi mdi-shield-account text-primary me-2"></i>Role & Status
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
                                                    <option value="{{ $role->id }}" 
                                                            {{ old('role_id', $currentRoleId) == $role->id ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('role_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            Current Role: 
                                            @foreach($staff->roles as $role)
                                                <span class="badge bg-info bg-opacity-10 text-info">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                        </small>
                                    </div>

                                    <div class="col-md-12 mb-0">
                                        <label class="form-label fw-semibold mb-3">Account Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="is_active" 
                                                   id="statusSwitch" 
                                                   value="1" 
                                                   {{ old('is_active', $staff->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="statusSwitch">
                                                Active Account
                                                @if($staff->is_active)
                                                    <span class="badge bg-success bg-opacity-10 text-success ms-2">Currently Active</span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger ms-2">Currently Inactive</span>
                                                @endif
                                            </label>
                                            <div class="text-muted small mt-1">
                                                Toggle to activate or deactivate this staff member's account
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="mdi mdi-lock-reset text-primary me-2"></i>Change Password
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-3">
                                    <i class="mdi mdi-information me-2"></i>
                                    <small>Leave password field blank if you don't want to change the current password</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            New Password
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-lock text-muted"></i>
                                            </span>
                                            <input type="password" 
                                                   name="password" 
                                                   class="form-control border-start-0 @error('password') is-invalid @enderror" 
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
                                            Confirm New Password
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="mdi mdi-lock-check text-muted"></i>
                                            </span>
                                            <input type="password" 
                                                   name="password_confirmation" 
                                                   class="form-control border-start-0" 
                                                   placeholder="********">
                                        </div>
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
                                <i class="mdi mdi-check-circle me-1"></i> Update Staff Member
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Activity Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="mdi mdi-information text-primary me-1"></i> Staff Information
                            </h6>
                            
                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Created Date</small>
                                <span class="fw-semibold">{{ $staff->created_at->format('M d, Y') }}</span>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Last Updated</small>
                                <span class="fw-semibold">{{ $staff->updated_at->format('M d, Y') }}</span>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <small class="text-muted d-block mb-1">Account Status</small>
                                @if($staff->is_active)
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                                        <i class="mdi mdi-check-circle me-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">
                                        <i class="mdi mdi-close-circle me-1"></i>Inactive
                                    </span>
                                @endif
                            </div>

                            <div class="mb-0">
                                <small class="text-muted d-block mb-2">Current Roles</small>
                                @forelse($staff->roles as $role)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 me-1 mb-1">
                                        <i class="mdi mdi-shield-account me-1"></i>{{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="text-muted small">No roles assigned</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card border-0 shadow-sm bg-light mt-3">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="mdi mdi-help-circle text-warning me-1"></i> Update Guidelines
                            </h6>
                            
                            <div class="mb-3">
                                <h6 class="fw-semibold small mb-2">
                                    <i class="mdi mdi-circle-small text-primary"></i> Email Changes
                                </h6>
                                <p class="small text-muted mb-0">
                                    Changing the email will update the login credentials for this staff member.
                                </p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-semibold small mb-2">
                                    <i class="mdi mdi-circle-small text-primary"></i> Password Reset
                                </h6>
                                <p class="small text-muted mb-0">
                                    Only fill the password fields if you want to change the current password.
                                </p>
                            </div>

                            <div class="mb-0">
                                <h6 class="fw-semibold small mb-2">
                                    <i class="mdi mdi-circle-small text-primary"></i> Deactivating Account
                                </h6>
                                <p class="small text-muted mb-0">
                                    Inactive accounts cannot login but data is preserved for reactivation.
                                </p>
                            </div>
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