<x-app-layout title="Subcategories">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Subcategories</h4>
                            <div>
                                <button class="btn btn-success btn-sm text-white me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="mdi mdi-upload"></i> Import
                                </button>
                                <a href="{{ route('store.subcategories.export') }}" class="btn btn-info btn-sm text-white me-2">
                                    <i class="mdi mdi-download"></i> Export
                                </a>
                                <a href="{{ route('store.subcategories.create') }}" class="btn btn-primary btn-sm text-white">
                                    <i class="mdi mdi-plus"></i> Add New
                                </a>
                            </div>
                        </div>

                        <form method="GET" class="row mb-4">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="category_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Filter by Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-dark btn-sm h-100">Filter</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Subcategory</th>
                                        <th>Parent Category</th>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subcategories as $sub)
                                    <tr>
                                        <td>{{ $sub->name }}</td>
                                        <td><span class="badge bg-secondary text-white">{{ $sub->category->name ?? 'N/A' }}</span></td>
                                        <td>{{ $sub->code }}</td>
                                        <td>
                                            @if(is_null($sub->store_id))
                                                <span class="badge bg-info text-white">Warehouse</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Store</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(is_null($sub->store_id))
                                                <span class="text-muted small"><i class="mdi mdi-lock"></i> Locked</span>
                                            @else
                                                <a href="{{ route('store.subcategories.edit', $sub->id) }}" class="btn btn-sm btn-primary py-1"><i class="mdi mdi-pencil"></i></a>
                                                <form action="{{ route('store.subcategories.destroy', $sub->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger py-1 delete-btn"><i class="mdi mdi-trash-can"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No subcategories found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $subcategories->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('store.subcategories.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Subcategories</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Excel File (xlsx, csv)</label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted d-block mt-1">Required Columns: <strong>category_code</strong>, <strong>name</strong>, <strong>code</strong></small>
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
        $('.delete-btn').click(function() {
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?', text: "This will delete the subcategory permanently!",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    </script>
    @endpush
</x-app-layout>