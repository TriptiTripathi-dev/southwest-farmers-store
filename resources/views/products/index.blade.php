<x-app-layout title="Product Management">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 ">
                    <div>
                        <h4 class="h3 fw-bold m-0 text-dark">Products Management</h4>
                        <p class="text-muted mb-0 mt-2">View and manage your product catalog</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light border" id="refreshBtn" data-bs-toggle="tooltip" title="Refresh">
                            <i class="mdi mdi-refresh"></i>
                        </button>
                    </div>
                </div>

                <div class="row g-3 ">
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                            <i class="mdi mdi-cube-outline text-primary fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">Total Products</p>
                                        <h3 class="mb-0 fw-bold text-dark">{{ $products->total() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                            <i class="mdi mdi-check-circle text-success fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">Active</p>
                                        <h3 class="mb-0 fw-bold text-success">{{ $products->where('is_active', true)->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                            <i class="mdi mdi-close-circle text-warning fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">Inactive</p>
                                        <h3 class="mb-0 fw-bold text-warning">{{ $products->where('is_active', false)->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                            <i class="mdi mdi-format-list-bulleted text-info fs-3"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="text-muted mb-1 small">This Page</p>
                                        <h3 class="mb-0 fw-bold text-info">{{ $products->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-lg-5 col-md-12">
                            <label class="form-label small text-muted mb-2">Search Products</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="mdi mdi-magnify text-primary"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" 
                                       placeholder="Search by product name, SKU, or barcode..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small text-muted mb-2">Filter by Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small text-muted mb-2">Filter by Category</label>
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-1 col-md-12">
                            <label class="form-label small text-muted mb-2 d-none d-lg-block">&nbsp;</label>
                            <button class="btn btn-light border w-100" onclick="clearFilters()">
                                <i class="mdi mdi-filter-off"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>Products List
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 text-muted fw-semibold">#</th>
                                    <th class="text-muted fw-semibold">PRODUCT NAME</th>
                                    <th class="text-muted fw-semibold">CATEGORY</th>
                                    <th class="text-muted fw-semibold">SUBCATEGORY</th>
                                    <th class="text-muted fw-semibold">SKU</th>
                                    <th class="text-muted fw-semibold">PRICE</th>
                                    <th class="text-muted fw-semibold">STATUS</th>
                                    <th class="text-muted fw-semibold text-end pe-4">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $loop->iteration + $products->firstItem() - 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3 border border-success border-opacity-25" style="width: 36px; height: 36px;">
                                                   
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-dark">{{ $product->product_name }}</h6>
                                                    <small class="text-muted">Unit: {{ $product->unit }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill" style="background-color: #d4f4dd; color: #1f7b3c; padding: 6px 14px;">
                                                {{ $product->category->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($product->subcategory)
                                                <span class="badge rounded-pill" style="background-color: #cfe9fc; color: #0c5c99; padding: 6px 14px;">
                                                    {{ $product->subcategory->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: #ffe5f1; color: #c7215d; padding: 6px 12px; font-family: monospace;">
                                                {{ $product->sku ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($product->price, 2) }}</td>
                                        <td>
                                            <label class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" {{ $product->is_active ? 'checked' : '' }} disabled>
                                            </label>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button type="button" class="btn btn-sm btn-primary ms-1" 
                                                    onclick="openRequestModal({{ $product->id }}, '{{ addslashes($product->product_name) }}')"
                                                    title="Request Stock">
                                                <i class="mdi mdi-plus"></i>
                                            </button>
                                            
                                            <button type="button" class="btn btn-sm btn-outline-primary view-btn" 
                                                    data-id="{{ $product->id }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="View Details">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                    <i class="mdi mdi-cube-off text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                                <h5 class="text-muted mb-2">No Products Found</h5>
                                                <p class="text-muted small mb-0">Try adjusting your filters or search terms.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <small class="text-muted">
                            Showing <strong>{{ $products->firstItem() }}</strong> to <strong>{{ $products->lastItem() }}</strong> of <strong>{{ $products->total() }}</strong> results
                        </small>
                        <div>{{ $products->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom bg-light py-3">
                    <h5 class="modal-title fw-bold" id="modalProductName">
                        <i class="mdi mdi-cube me-2 text-primary"></i>Product Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modalContent">
                    <div class="text-center py-5" id="modalLoader">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                        <p class="mt-3 text-muted fw-medium">Fetching product details...</p>
                    </div>

                    <div id="modalDetails" class="d-none">
                        <div class="row g-4">
                            <div class="col-md-6 border-end">
                                <h6 class="text-uppercase text-primary small fw-bold mb-3 border-bottom pb-2">General Information</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">SKU:</td>
                                        <td class="fw-medium font-monospace text-dark" id="viewSku"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Barcode:</td>
                                        <td class="fw-medium font-monospace text-dark" id="viewBarcode"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Category:</td>
                                        <td class="fw-medium text-dark" id="viewCategory"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Subcategory:</td>
                                        <td class="fw-medium text-dark" id="viewSubcategory"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Unit:</td>
                                        <td class="fw-medium text-dark" id="viewUnit"></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-uppercase text-primary small fw-bold mb-3 border-bottom pb-2">Pricing & Status</h6>
                                <div class="bg-light p-3 rounded border">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Selling Price:</span>
                                        <span class="fw-bold text-success h5 mb-0" id="viewPrice"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Cost Price:</span>
                                        <span class="fw-medium text-dark" id="viewCostPrice"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tax (%):</span>
                                        <span class="fw-medium text-dark" id="viewTax"></span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Current Status:</span>
                                        <span id="viewStatus"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="requestStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('inventory.request') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" id="requestProductId">
                    
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="requestModalTitle">Request Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Quantity Needed</label>
                            <input type="number" name="quantity" class="form-control" min="1" required placeholder="Enter quantity">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- View Modal Logic ---
            var viewModalEl = document.getElementById('viewProductModal');
            var viewModal = viewModalEl ? new bootstrap.Modal(viewModalEl) : null;
            
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const modalLoader = document.getElementById('modalLoader');
                    const modalDetails = document.getElementById('modalDetails');
                    
                    if(viewModal) {
                        modalLoader.classList.remove('d-none');
                        modalDetails.classList.add('d-none');
                        viewModal.show();

                        // Fetch Data via AJAX
                        fetch(`{{ url('products') }}/${productId}`)
                            .then(response => response.json())
                            .then(res => {
                                if(res.success) {
                                    const data = res.data;
                                    
                                    document.getElementById('modalProductName').textContent = data.product_name;
                                    document.getElementById('viewSku').textContent = data.sku || '-';
                                    document.getElementById('viewBarcode').textContent = data.barcode || '-';
                                    document.getElementById('viewCategory').textContent = data.category ? data.category.name : '-';
                                    document.getElementById('viewSubcategory').textContent = data.subcategory ? data.subcategory.name : '-';
                                    document.getElementById('viewUnit').textContent = data.unit;
                                    
                                    document.getElementById('viewPrice').textContent = '$' + parseFloat(data.price).toFixed(2);
                                    document.getElementById('viewCostPrice').textContent = data.cost_price ? '$' + parseFloat(data.cost_price).toFixed(2) : '-';
                                    document.getElementById('viewTax').textContent = data.tax_percent + '%';

                                    const statusHtml = data.is_active 
                                        ? '<span class="badge bg-success bg-opacity-10 text-success border border-success px-3">Active</span>' 
                                        : '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3">Inactive</span>';
                                    document.getElementById('viewStatus').innerHTML = statusHtml;

                                    setTimeout(() => {
                                        modalLoader.classList.add('d-none');
                                        modalDetails.classList.remove('d-none');
                                    }, 300);
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Failed to load product details. Please try again.');
                                viewModal.hide();
                            });
                    }
                });
            });

            // --- Filter Logic ---
            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const category = document.getElementById('categoryFilter').value;
                const status = document.getElementById('statusFilter').value;

                const url = new URL(window.location.href);
                
                if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
                if (category) url.searchParams.set('category', category); else url.searchParams.delete('category');
                if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
                
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }

            // Event Listeners for Filters
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') applyFilters();
            });
            document.getElementById('categoryFilter').addEventListener('change', applyFilters);
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            
            // Clear Filters
            window.clearFilters = function() {
                window.location.href = '{{ route('products.index') }}';
            }

            // Refresh Button
            document.getElementById('refreshBtn').addEventListener('click', function() {
                window.location.reload();
            });

            // --- FIXED: Request Stock Modal Logic ---
            window.openRequestModal = function(productId, productName) {
                // 1. Set ID in Hidden Input
                var input = document.getElementById('requestProductId');
                if(input) input.value = productId;

                // 2. Set Title
                var title = document.getElementById('requestModalTitle');
                if(title) title.innerText = 'Request Stock: ' + productName;

                // 3. Open Modal safely
                var modalEl = document.getElementById('requestStockModal');
                if (modalEl) {
                    var modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (!modalInstance) {
                        modalInstance = new bootstrap.Modal(modalEl);
                    }
                    modalInstance.show();
                } else {
                    console.error('Modal element #requestStockModal not found');
                }
            }
        });
    </script>
    @endpush
</x-app-layout>