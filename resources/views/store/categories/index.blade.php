<x-app-layout title="Product Categories">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Product Categories</h4>
                    <p class="text-muted small mb-0 mt-1">Manage local and global catalog classifications</p>
                </div>
                
                {{-- ACTIONS --}}
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success text-white shadow-sm fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="mdi mdi-upload fs-5 me-1"></i> Import
                    </button>
                    <a href="{{ route('store.categories.export') }}" class="btn btn-info text-white shadow-sm fw-bold d-flex align-items-center">
                        <i class="mdi mdi-download fs-5 me-1"></i> Export
                    </a>
                    <a href="{{ route('store.categories.create') }}" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Add New
                    </a>
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                
                {{-- FILTER BAR --}}
                <div class="card-header bg-white border-bottom p-3 p-md-4 rounded-top-4">
                    <form method="GET" class="row g-2 align-items-center m-0">
                        <div class="col-12 col-md-5">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-magnify fs-5"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-start-0 py-2" placeholder="Search category name or code..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-muted px-3"><i class="mdi mdi-filter-variant"></i></span>
                                <select name="type" class="form-select bg-light py-2" onchange="this.form.submit()">
                                    <option value="">All Category Types</option>
                                    <option value="warehouse" {{ request('type') == 'warehouse' ? 'selected' : '' }}>Warehouse (Global)</option>
                                    <option value="store" {{ request('type') == 'store' ? 'selected' : '' }}>My Store (Local)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow-sm">Search</button>
                        </div>
                    </form>
                </div>

                {{-- DATA TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Category Info</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Type</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-center">Status</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase letter-spacing-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $cat)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark fs-6">{{ $cat->name }}</div>
                                        <div class="text-muted font-monospace small"><i class="mdi mdi-barcode me-1"></i>{{ $cat->code }}</div>
                                    </td>
                                    <td>
                                        @if(is_null($cat->store_id))
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                <i class="mdi mdi-earth me-1"></i> Warehouse
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                <i class="mdi mdi-storefront me-1"></i> Store
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(is_null($cat->store_id))
                                            <span class="badge {{ $cat->is_active ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                                {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @else
                                            <div class="form-check form-switch d-flex justify-content-center m-0 fs-5">
                                                <input class="form-check-input status-toggle" type="checkbox" role="switch" style="cursor: pointer;"
                                                    data-id="{{ $cat->id }}" {{ $cat->is_active ? 'checked' : '' }}>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('store.categories.analytics', $cat->id) }}" class="btn btn-sm btn-light border shadow-sm text-info" title="Analytics">
                                                <i class="mdi mdi-chart-line fs-6"></i>
                                            </a>
                                            
                                            @if(is_null($cat->store_id))
                                                <div class="btn btn-sm btn-light border text-muted opacity-75 pe-none" title="Global Category (Locked)">
                                                    <i class="mdi mdi-lock fs-6"></i>
                                                </div>
                                            @else
                                                <a href="{{ route('store.categories.edit', $cat->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit">
                                                    <i class="mdi mdi-pencil fs-6"></i>
                                                </a>
                                                <form action="{{ route('store.categories.destroy', $cat->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" title="Delete">
                                                        <i class="mdi mdi-trash-can fs-6"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-shape-outline" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Categories Found</h6>
                                        <p class="text-muted small mb-0">Try adjusting your search filters or add a new category.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if($categories->hasPages())
                <div class="card-footer bg-white border-top p-3 rounded-bottom-4">
                    {{ $categories->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('store.categories.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-primary"><i class="mdi mdi-upload me-2"></i>Import Categories</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info small rounded-3 mb-3">
                            <i class="mdi mdi-information me-1"></i> Ensure your file contains the columns: <strong>name, code</strong>.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Excel File (.xlsx, .csv) <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control bg-light border-0 shadow-sm py-2" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success text-white rounded-pill px-4 fw-bold shadow-sm">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Delete Confirmation
        $('.delete-btn').click(function() {
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this category deletion!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-danger rounded-pill px-4',
                    cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Status Toggle AJAX
        $('.status-toggle').change(function() {
            var status = $(this).prop('checked') ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('store.categories.status') }}",
                data: {'status': status, 'id': id, '_token': '{{ csrf_token() }}'},
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 3000,
                        customClass: { popup: 'rounded-4 shadow-sm' }
                    });
                    Toast.fire({ icon: 'success', title: 'Status updated successfully' });
                },
                error: function() {
                    const Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                    });
                    Toast.fire({ icon: 'error', title: 'Failed to update status' });
                    // Revert the toggle state on failure
                    setTimeout(() => location.reload(), 1500); 
                }
            });
        });
    </script>
    @endpush
</x-app-layout>