<x-app-layout title="Store Roles">
    @push('styles')
    <style>
        body { font-family: 'Manrope', sans-serif; }
        
        .hover-lift { transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important; }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .letter-spacing-1 { letter-spacing: 0.5px; }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-shield-account-outline fs-5"></i>
                        </div>
                        Store Roles
                    </h4>
                    <p class="text-muted small mb-0 mt-1 ms-5">Manage staff roles and their associated system permissions</p>
                </div>
                
                <div class="d-flex">
                    @if(Auth::user()->hasPermission('manage_roles'))
                    <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold d-flex align-items-center transition-all hover-lift">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Create Role
                    </a>
                    @endif
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-3 p-md-4">
                    <h6 class="mb-0 fw-bold text-dark">Role Management</h6>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive custom-scrollbar">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1" style="width: 60px;">#</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Role Name</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Permissions Count</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase letter-spacing-1">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold fs-6">
                                                <i class="mdi mdi-shield-check-outline me-1"></i> {{ ucfirst($role->name) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold border border-info border-opacity-25 me-2" style="width: 32px; height: 32px;">
                                                {{ $role->permissions->count() }}
                                            </div>
                                            <span class="text-muted small fw-medium">Assigned Permissions</span>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        @if(Auth::user()->hasPermission('manage_roles'))
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" data-bs-toggle="tooltip" title="Edit Role">
                                                <i class="mdi mdi-pencil fs-6"></i>
                                            </a>
                                            
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" data-bs-toggle="tooltip" title="Delete Role">
                                                    <i class="mdi mdi-trash-can fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-shield-off-outline display-4"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark">No Roles Found</h5>
                                        <p class="text-muted small mb-0">Start by creating a new role for your staff members.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- If pagination exists in the future, it will render here seamlessly --}}
                @if(method_exists($roles, 'hasPages') && $roles->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $roles->links() }}
                </div>
                @endif
                
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // SweetAlert Delete Confirmation
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Delete this Role?',
                        text: "Users assigned to this role might lose access. You cannot revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete it!',
                        customClass: {
                            confirmButton: 'btn btn-danger rounded-pill px-4 fw-bold',
                            cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2 fw-bold'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>