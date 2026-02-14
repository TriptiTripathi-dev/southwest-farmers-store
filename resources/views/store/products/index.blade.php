<x-app-layout title="Product List">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Product Catalog</h4>
                    <p class="text-muted small mb-0 mt-1">Manage local store products and global warehouse items</p>
                </div>
                
                {{-- ACTIONS --}}
                <div class="d-flex flex-wrap gap-2">
                    @if(Auth::user()->hasPermission('import_products'))
                    <button class="btn btn-success text-white shadow-sm fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="mdi mdi-upload fs-5 me-1"></i> Import
                    </button>
                    @endif

                    @if(Auth::user()->hasPermission('export_products'))
                    <a href="{{ route('store.products.export') }}" class="btn btn-info text-white shadow-sm fw-bold d-flex align-items-center">
                        <i class="mdi mdi-download fs-5 me-1"></i> Export
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission('create_product'))
                    <a href="{{ route('store.products.create') }}" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Add New
                    </a>
                    @endif
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
                                <input type="text" name="search" class="form-control bg-light border-start-0 py-2" placeholder="Search Product Name or SKU..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-muted px-3"><i class="mdi mdi-filter-variant"></i></span>
                                <select name="type" class="form-select bg-light py-2" onchange="this.form.submit()">
                                    <option value="">All Product Types</option>
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
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Product</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Category</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Subcategory</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">SKU</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Type</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Price</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase text-center" style="letter-spacing: 0.5px;">Status</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{$item->icon ? Storage::url($item->icon) : asset('assets/images/logo.jpg') }}" 
                                                 class="rounded-circle border shadow-sm" width="40" height="40" style="object-fit: cover;">
                                            <div class="ms-3">
                                                <p class="mb-0 fw-bold text-dark">{{ $item->product_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-medium text-dark">{{$item->category->name ?? 'N/A'}}</td>
                                    <td class="text-muted">{{$item->subcategory->name ?? 'N/A'}}</td>
                                    <td>
                                        <span class="font-monospace text-muted small"><i class="mdi mdi-barcode me-1"></i>{{ $item->sku }}</span>
                                    </td>
                                    <td>
                                        @if($item->store_id == null)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill fw-bold">Warehouse</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill fw-bold">Store</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bolder text-success fs-6">${{ number_format($item->price, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->store_id == null)
                                            <span class="badge bg-light text-muted border px-2 py-1 rounded-pill"><i class="mdi mdi-lock me-1"></i>Global</span>
                                        @else
                                            <div class="form-check form-switch d-flex justify-content-center m-0 fs-5">
                                                <input class="form-check-input status-toggle" type="checkbox" role="switch" style="cursor: pointer;"
                                                    data-id="{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}
                                                    {{ !Auth::user()->hasPermission('edit_product') ? 'disabled' : '' }}>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if(Auth::user()->hasPermission('manage_recipes'))
                                            <a href="{{ route('store.products.recipe', $item->id) }}" class="btn btn-sm btn-light border shadow-sm text-warning" data-bs-toggle="tooltip" title="Manage Recipe">
                                                <i class="mdi mdi-silverware-variant fs-6"></i>
                                            </a>
                                            @endif

                                            @if(Auth::user()->hasPermission('view_stock_history'))
                                            <a href="{{ route('inventory.history', $item->id) }}" class="btn btn-sm btn-light border shadow-sm text-info" data-bs-toggle="tooltip" title="View Stock History">
                                                <i class="mdi mdi-history fs-6"></i>
                                            </a>
                                            @endif

                                            @if(Auth::user()->hasPermission('view_analytics'))
                                            <a href="{{ route('store.products.analytics', $item->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" data-bs-toggle="tooltip" title="Analytics">
                                                <i class="mdi mdi-chart-bar fs-6"></i>
                                            </a>
                                            @endif

                                            @if($item->store_id != null)
                                                @if(Auth::user()->hasPermission('edit_product'))
                                                <a href="{{ route('store.products.edit', $item->id) }}" class="btn btn-sm btn-light border shadow-sm text-secondary" data-bs-toggle="tooltip" title="Edit Product">
                                                    <i class="mdi mdi-pencil fs-6"></i>
                                                </a>
                                                @endif

                                                @if(Auth::user()->hasPermission('delete_product'))
                                                <form action="{{ route('store.products.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" data-bs-toggle="tooltip" title="Delete Product">
                                                        <i class="mdi mdi-trash-can fs-6"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            @else
                                                @if(Auth::user()->hasPermission('edit_product'))
                                                <a href="{{ route('store.products.edit', $item->id) }}" class="btn btn-sm btn-light border shadow-sm text-secondary" data-bs-toggle="tooltip" title="Edit Selling Price">
                                                    <i class="mdi mdi-pencil fs-6"></i>
                                                </a>
                                                @else
                                                <button disabled class="btn btn-sm btn-light border shadow-sm text-muted opacity-50">
                                                    <i class="mdi mdi-lock fs-6"></i>
                                                </button>
                                                @endif
                                                
                                                <button disabled class="btn btn-sm btn-light border shadow-sm text-muted opacity-50" data-bs-toggle="tooltip" title="Cannot delete Global Product">
                                                    <i class="mdi mdi-lock fs-6"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-package-variant-closed" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Products Found</h6>
                                        <p class="text-muted small mb-0">Try adjusting your search filters or add a new product.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if($products->hasPages())
                <div class="card-footer bg-white border-top p-3 rounded-bottom-4">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
            
        </div>
    </div>

    @include('store.products._import-modal')

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Status Toggle AJAX
        $('.status-toggle').change(function() {
            var status = $(this).prop('checked') ? 1 : 0;
            var id = $(this).data('id');
            var toggleInput = $(this); // Reference to revert on failure

            $.ajax({
                type: "POST", 
                url: "{{ route('store.products.status') }}",
                data: {'status': status, 'id': id, '_token': '{{ csrf_token() }}'},
                success: function(data) {
                    Swal.fire({
                        toast: true, 
                        position: 'top-end', 
                        icon: 'success', 
                        title: 'Product status updated', 
                        showConfirmButton: false, 
                        timer: 2000,
                        customClass: { popup: 'rounded-4 shadow-sm' }
                    });
                },
                error: function() {
                    toggleInput.prop('checked', !status); // Revert switch visually
                    Swal.fire({
                        toast: true, 
                        position: 'top-end', 
                        icon: 'error', 
                        title: 'Status update failed', 
                        showConfirmButton: false, 
                        timer: 2000,
                        customClass: { popup: 'rounded-4 shadow-sm' }
                    });
                }
            });
        });

        // Delete Confirmation
        $('.delete-btn').click(function() {
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Delete this product?',
                text: "You won't be able to revert this action!",
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
                if (result.isConfirmed) form.submit(); 
            });
        });
    </script>
    @endpush
</x-app-layout>