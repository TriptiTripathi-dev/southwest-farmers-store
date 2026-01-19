<x-app-layout title="Product Categories">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Product Categories</h4>
                            <div>
                                <button type="button" class="btn btn-success btn-sm text-white me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="mdi mdi-upload"></i> Import
                                </button>
                                <a href="{{ route('store.categories.export') }}" class="btn btn-info btn-sm text-white me-2">
                                    <i class="mdi mdi-download"></i> Export
                                </a>
                                <a href="{{ route('store.categories.create') }}" class="btn btn-primary btn-sm text-white">
                                    <i class="mdi mdi-plus"></i> Add New
                                </a>
                            </div>
                        </div>

                        <form method="GET" class="row mb-4">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="warehouse" {{ request('type') == 'warehouse' ? 'selected' : '' }}>Warehouse (Global)</option>
                                    <option value="store" {{ request('type') == 'store' ? 'selected' : '' }}>My Store (Local)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-dark btn-sm h-100">Search</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $cat)
                                    <tr>
                                        <td>{{ $cat->name }}</td>
                                        <td>{{ $cat->code }}</td>
                                        <td>
                                            @if(is_null($cat->store_id))
                                                <span class="badge bg-info text-white">Warehouse</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Store</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(is_null($cat->store_id))
                                                <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            @else
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" type="checkbox" 
                                                        data-id="{{ $cat->id }}" {{ $cat->is_active ? 'checked' : '' }}>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('store.categories.analytics', $cat->id) }}" class="btn btn-sm btn-info py-1 me-1" title="Analytics">
    <i class="mdi mdi-chart-bar"></i>
</a>
                                            @if(is_null($cat->store_id))
                                                <span class="text-muted small"><i class="mdi mdi-lock"></i> Locked</span>
                                            @else
                                                <a href="{{ route('store.categories.edit', $cat->id) }}" class="btn btn-sm btn-primary py-1"><i class="mdi mdi-pencil"></i></a>
                                                <form action="{{ route('store.categories.destroy', $cat->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger py-1 delete-btn"><i class="mdi mdi-trash-can"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No categories found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('store.categories.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Categories</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Excel File (xlsx, csv)</label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted">Columns: name, code</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success text-white">Upload & Import</button>
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
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Status Toggle
        $('.status-toggle').change(function() {
            var status = $(this).prop('checked') ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('store.categories.status') }}",
                data: {'status': status, 'id': id, '_token': '{{ csrf_token() }}'},
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                    });
                    Toast.fire({ icon: 'success', title: 'Status updated successfully' });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>