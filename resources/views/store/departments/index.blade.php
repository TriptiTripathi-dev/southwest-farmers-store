<x-app-layout title="Departments Management">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Departments</h4>
                    <p class="text-muted small mb-0 mt-1">Manage local and global catalog departments</p>
                </div>
                
                {{-- ACTIONS --}}
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('departments.create') }}" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Add New
                    </a>
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                
                {{-- FILTER BAR --}}
                <div class="card-header bg-white border-bottom p-3 p-md-4 rounded-top-4">
                    <form method="GET" class="row g-2 align-items-center m-0">
                        <div class="col-12 col-md-9">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-magnify fs-5"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-start-0 py-2" placeholder="Search department name or code..." value="{{ request('search') }}">
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
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Department Info</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Type</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-center">Status</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase letter-spacing-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $dept)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark fs-6">{{ $dept->name }}</div>
                                        <div class="text-muted font-monospace small"><i class="mdi mdi-barcode me-1"></i>{{ $dept->code }}</div>
                                    </td>
                                    <td>
                                        @if(is_null($dept->store_id))
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
                                        @if(is_null($dept->store_id))
                                            <span class="badge {{ $dept->is_active ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                                {{ $dept->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @else
                                            <div class="form-check form-switch d-flex justify-content-center m-0 fs-5">
                                                <input class="form-check-input status-toggle" type="checkbox" role="switch" style="cursor: pointer;"
                                                    data-id="{{ $dept->id }}" {{ $dept->is_active ? 'checked' : '' }}>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if(!is_null($dept->store_id))
                                                <a href="{{ route('departments.edit', $dept->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit">
                                                    <i class="mdi mdi-pencil fs-6"></i>
                                                </a>
                                                <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" title="Delete">
                                                        <i class="mdi mdi-trash-can fs-6"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="btn btn-sm btn-light border text-muted opacity-75 pe-none" title="Global Department (Locked)">
                                                    <i class="mdi mdi-lock fs-6"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-domain" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Departments Found</h6>
                                        <p class="text-muted small mb-0">Try adjusting your search filters or add a new department.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if($departments->hasPages())
                <div class="card-footer bg-white border-top p-3 rounded-bottom-4">
                    {{ $departments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Status Toggle AJAX
        $('.status-toggle').change(function() {
            var status = $(this).prop('checked') ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('departments.status') }}",
                data: {'status': status, 'id': id, '_token': '{{ csrf_token() }}'},
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true, 
                        position: 'top-end', 
                        showConfirmButton: false, 
                        timer: 3000,
                        customClass: { popup: 'rounded-4 shadow-sm' }
                    });
                    Toast.fire({ icon: 'success', title: data.message });
                },
                error: function(xhr) {
                    const Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                    });
                    Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Failed to update status' });
                    // Revert the toggle state on failure
                    setTimeout(() => location.reload(), 1500); 
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
