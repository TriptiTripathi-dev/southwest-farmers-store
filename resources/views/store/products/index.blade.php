<x-app-layout title="Product List">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Product List</h4>
                            <div>
                                <button class="btn btn-success btn-sm text-white me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="mdi mdi-upload"></i> Import
                                </button>
                                <a href="{{ route('store.products.export') }}" class="btn btn-info btn-sm text-white me-2">
                                    <i class="mdi mdi-download"></i> Export
                                </a>
                                <a href="{{ route('store.products.create') }}" class="btn btn-primary btn-sm text-white">
                                    <i class="mdi mdi-plus"></i> Add New
                                </a>
                            </div>
                        </div>

                        <form method="GET" class="row mb-4">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search Product / SKU" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="warehouse" {{ request('type') == 'warehouse' ? 'selected' : '' }}>Warehouse Products</option>
                                    <option value="store" {{ request('type') == 'store' ? 'selected' : '' }}>My Store Products</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-dark btn-sm h-100">Search</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Subcategory</th>
                                        <th>SKU</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/'.$item->icon) }}" class="rounded-circle" width="30" height="30" 
                                                    >
                                                <div class="ms-3">
                                                    <p class="mb-0 fw-bold">{{ $item->product_name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{$item->category->name}}</td>
                                        <td>{{$item->subcategory->name}}</td>
                                        <td>{{ $item->sku }}</td>
                                        <td>
                                            @if($item->store_id == null)
                                                <span class="badge bg-info text-white">Warehouse</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Store</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">₹{{ $item->price }}</td>
                                        <td>
                                            @if($item->store_id == null)
                                                <span class="badge bg-secondary">Managed by Admin</span>
                                            @else
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" type="checkbox" 
                                                        data-id="{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('store.products.edit', $item->id) }}" class="btn btn-sm btn-primary py-1"><i class="mdi mdi-pencil"></i></a>
                                            
                                            @if($item->product_store_id != null)
                                                <form action="{{ route('store.products.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger py-1 delete-btn"><i class="mdi mdi-trash-can"></i></button>
                                                </form>
                                            @else
                                                <button disabled class="btn btn-sm btn-secondary py-1" title="Cannot delete Warehouse Product"><i class="mdi mdi-lock"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No products found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $products->links() }}</div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('store.products._import-modal')

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Status Toggle
        $('.status-toggle').change(function() {
            var status = $(this).prop('checked') ? 1 : 0;
            var id = $(this).data('id');
            $.ajax({
                type: "POST", url: "{{ route('store.products.status') }}",
                data: {'status': status, 'id': id, '_token': '{{ csrf_token() }}'},
                success: function(data) {
                    Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Status updated', showConfirmButton: false, timer: 2000});
                }
            });
        });

        // Delete Confirm
        $('.delete-btn').click(function() {
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    </script>
    @endpush
</x-app-layout>