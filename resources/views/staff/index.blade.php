<x-app-layout title="Staff Management">
    <div class="content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="py-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="h3 fw-bold m-0 text-dark">Staff Management</h4>
                    <p class="text-muted mb-0 mt-2">Manage your team members and their roles</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light border" id="refreshBtn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                    <a href="{{ route('staff.create') }}" class="btn btn-primary shadow-sm px-4">
                        <i class="mdi mdi-plus-circle me-2"></i> Add New Staff
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4 g-3">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                 <div class="d-flex align-items-center justify-content-center rounded-circle text-white" 
     style="width: 60px; height: 60px; background-color: rgba(72, 187, 120, 0.2);">
    <i class="mdi mdi-account-group" style="font-size: 24px; color: #48bb78;"></i>
</div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1 small fw-medium text-uppercase">Total Staff</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $staffMembers->total() }}</h3>
                                    <small class="text-success">
                                        <i class="mdi mdi-trending-up"></i> All members
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" 
     style="width: 48px; height: 48px; background-color: rgba(72, 187, 120, 0.1);">
    <i class="mdi mdi-account-check" style="font-size: 20px; color: #48bb78;"></i>
</div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1 small fw-medium text-uppercase">Active Staff</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $staffMembers->where('is_active', 1)->count() }}</h3>
                                    <small class="text-success">
                                        <i class="mdi mdi-check-circle"></i> Currently active
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                  <div class="d-flex align-items-center justify-content-center rounded-circle" 
     style="width: 48px; height: 48px; background-color: rgba(245, 101, 101, 0.1);">
    <i class="mdi mdi-account-off" style="font-size: 20px; color: #f56565;"></i>
</div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1 small fw-medium text-uppercase">Inactive Staff</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $staffMembers->where('is_active', 0)->count() }}</h3>
                                    <small class="text-warning">
                                        <i class="mdi mdi-minus-circle"></i> Not active
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" 
     style="width: 48px; height: 48px; background-color: rgba(99, 102, 241, 0.1);">
    <i class="mdi mdi-shield-account" style="font-size: 20px; color: #6366f1;"></i>
</div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-1 small fw-medium text-uppercase">Roles</p>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $roles->count() }}</h3>
                                    <small class="text-info">
                                        <i class="mdi mdi-information"></i> Total roles
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5">
                            <label class="form-label small text-muted mb-2 fw-semibold text-uppercase">Search Staff</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 pe-0">
                                    <i class="mdi mdi-magnify text-primary fs-5"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0 ps-2 shadow-none" 
                                       placeholder="Search by name, email or phone..." 
                                       id="searchInput"
                                       value="{{ request('search') }}">
                                <button class="btn btn-primary px-4" type="button" onclick="applyFilters()">
                                    <i class="mdi mdi-magnify me-1"></i> Search
                                </button>
                            </div>
                          
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small text-muted mb-2 fw-semibold text-uppercase">Filter by Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 pe-0">
                                    <i class="mdi mdi-shield-account text-muted"></i>
                                </span>
                                <select class="form-select border-start-0 ps-2 shadow-none" id="roleFilter">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small text-muted mb-2 fw-semibold text-uppercase">Filter by Status</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 pe-0">
                                    <i class="mdi mdi-toggle-switch text-muted"></i>
                                </span>
                                <select class="form-select border-start-0 ps-2 shadow-none" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-12">
                            <button class="btn btn-light border w-100 d-flex align-items-center justify-content-center gap-2" onclick="clearFilters()" data-bs-toggle="tooltip" title="Clear all filters">
                                <i class="mdi mdi-refresh"></i>
                                <span class="d-none d-lg-inline">Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0 fw-semibold d-flex align-items-center">
                            <i class="mdi mdi-account-multiple text-primary me-2 fs-4"></i>
                            Staff Members List
                        </h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill">
                            <i class="mdi mdi-account-group me-1"></i>
                            {{ $staffMembers->total() }} Total
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">#</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase">Staff Member</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase">Contact Info</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase">Role</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase">Status</th>
                                    <th class="text-end pe-4 py-3 text-muted small fw-bold text-uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($staffMembers as $staff)
                                    <tr class="border-bottom">
                                        <td class="ps-4">
                                            <span class="badge bg-light text-dark fw-semibold">{{ $staffMembers->firstItem() + $loop->index }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center py-2">
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3 flex-shrink-0 shadow-sm border border-primary border-opacity-25 fs-5 fw-bold text-primary" style="width: 48px; height: 48px;">
                                                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-semibold text-dark">{{ $staff->name }}</h6>
                                                    <span class="badge bg-light text-muted border small">
                                                        <i class="mdi mdi-identifier"></i> #{{ str_pad($staff->id, 4, '0', STR_PAD_LEFT) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-email-outline text-primary me-2"></i>
                                                    <span class="text-dark small">{{ $staff->email }}</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-phone-outline text-success me-2"></i>
                                                    <span class="text-muted small">{{ $staff->phone ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @forelse($staff->roles as $role)
                                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2">
                                                        <i class="mdi mdi-shield-account me-1"></i>{{ ucfirst($role->name) }}
                                                    </span>
                                                @empty
                                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2">
                                                        <i class="mdi mdi-alert-circle-outline me-1"></i>No Role
                                                    </span>
                                                @endforelse
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="form-check form-switch d-flex align-items-center mb-0">
                                                <input class="form-check-input status-toggle fs-5 me-2" 
                                                       type="checkbox" 
                                                       role="switch" 
                                                       id="status_{{ $staff->id }}"
                                                       data-id="{{ $staff->id }}"
                                                       {{ $staff->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold mb-0" for="status_{{ $staff->id }}">
                                                    <span class="status-text badge {{ $staff->is_active ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 border {{ $staff->is_active ? 'border-success text-success' : 'border-secondary text-secondary' }} px-2 py-1">
                                                        {{ $staff->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </label>
                                                <div class="spinner-border text-primary ms-2 spinner-border-sm d-none status-spinner" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm" role="group">
                                                <a href="{{ route('staff.edit', $staff->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit Staff">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $staff->id }}" 
                                                        title="Delete Staff">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $staff->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header border-0 pb-0 bg-danger bg-opacity-10">
                                                            <h5 class="modal-title fw-bold text-danger d-flex align-items-center">
                                                                <i class="mdi mdi-alert-circle me-2"></i>
                                                                Confirm Deletion
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start py-4">
                                                            <p class="mb-2">Are you sure you want to delete this staff member?</p>
                                                            <div class="bg-light p-3 rounded border">
                                                                <strong class="text-dark">{{ $staff->name }}</strong>
                                                                <div class="small text-muted mt-1">{{ $staff->email }}</div>
                                                            </div>
                                                            <div class="alert alert-warning mt-3 mb-0 d-flex align-items-start">
                                                                <i class="mdi mdi-alert me-2 mt-1"></i>
                                                                <small>This action cannot be undone.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0">
                                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('staff.destroy', $staff->id) }}" method="POST" class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-danger px-4">
                                                                    <i class="mdi mdi-delete me-1"></i> Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center gap-3">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                                    <i class="mdi mdi-account-off text-muted display-2"></i>
                                                </div>
                                                <div>
                                                    <h5 class="text-muted mb-2 fw-semibold">No Staff Members Found</h5>
                                                    <p class="text-muted small mb-0">Try adjusting your search or filter criteria</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($staffMembers->isNotEmpty())
                    <div class="card-footer bg-white border-top py-3 px-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="text-muted small">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Showing <strong>{{ $staffMembers->firstItem() ?? 0 }}</strong> to <strong>{{ $staffMembers->lastItem() ?? 0 }}</strong> of <strong>{{ $staffMembers->total() }}</strong> entries
                            </div>
                            <div>
                                {{ $staffMembers->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.03);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Filter functions
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const role = document.getElementById('roleFilter').value;
            const status = document.getElementById('statusFilter').value;
            const url = new URL(window.location.href);
            
            if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
            if (role) url.searchParams.set('role', role); else url.searchParams.delete('role');
            if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
            
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function clearFilters() { 
            window.location.href = '{{ route('staff.index') }}'; 
        }

        // Event listeners
        document.getElementById('searchInput')?.addEventListener('keyup', function(e) { 
            if (e.key === 'Enter') applyFilters(); 
        });
        
        document.getElementById('roleFilter')?.addEventListener('change', applyFilters);
        document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
        document.getElementById('refreshBtn')?.addEventListener('click', function() { 
            window.location.reload(); 
        });

        // Status toggle AJAX
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const staffId = this.dataset.id;
                const isChecked = this.checked;
                const statusBadge = this.nextElementSibling.querySelector('.status-text');
                const spinner = this.parentElement.querySelector('.status-spinner');

                // Show spinner and disable toggle
                spinner.classList.remove('d-none');
                this.disabled = true;

                fetch('{{ route("staff.update-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: staffId,
                        status: isChecked ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    spinner.classList.add('d-none');
                    this.disabled = false;

                    if (data.success) {
                        // Update badge
                        if (isChecked) {
                            statusBadge.textContent = 'Active';
                            statusBadge.className = 'status-text badge bg-success bg-opacity-10 border border-success text-success px-2 py-1';
                        } else {
                            statusBadge.textContent = 'Inactive';
                            statusBadge.className = 'status-text badge bg-secondary bg-opacity-10 border border-secondary text-secondary px-2 py-1';
                        }
                        
                        // Show success notification (if toastr is available)
                        if (typeof toastr !== 'undefined') {
                            toastr.success(data.message);
                        }
                    } else {
                        // Revert switch if failed
                        this.checked = !isChecked;
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to update status');
                        } else {
                            alert('Failed to update status');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    spinner.classList.add('d-none');
                    this.disabled = false;
                    this.checked = !isChecked; // Revert
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Something went wrong');
                    } else {
                        alert('Something went wrong');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>