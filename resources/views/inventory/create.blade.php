<x-app-layout title="New Order Inventory">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-file-plus text-primary me-2"></i> New Order Inventory
            </h4>
            <small class="text-muted">Create a new replenishment order for the warehouse</small>
        </div>
        <a href="{{ route('inventory.requests') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form action="{{ route('inventory.order.store') }}" method="POST" id="orderForm">
        @csrf
        
        <div class="row">
            <!-- Left: Order Details & Contacts -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="mdi mdi-information-outline me-2"></i>Order Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Vendor</label>
                            <input type="text" class="form-control bg-light" value="HOMEFOODS DISTRIBUTORS" readonly disabled>
                            <small class="text-muted">Orders are pre-assigned to the central warehouse distributor.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="departmentSelect" class="form-select select2" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Products will be filtered based on the selected department.</small>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="mdi mdi-account-group me-2"></i>Approval Contacts</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase text-muted mb-1">General Manager</label>
                                <input type="email" name="gm_email" class="form-control mb-2" placeholder="GM Email">
                                <input type="text" name="gm_phone" class="form-control" placeholder="GM Phone">
                            </div>
                            <div class="col-12 mt-3">
                                <hr class="my-2 opacity-10">
                                <label class="form-label fw-bold small text-uppercase text-muted mb-1">VP (Vice President)</label>
                                <input type="email" name="vp_email" class="form-control mb-2" placeholder="VP Email">
                                <input type="text" name="vp_phone" class="form-control" placeholder="VP Phone">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <label class="form-label fw-bold">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Add any notes or special instructions..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Product Selection -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="mdi mdi-cart me-2"></i>Select Products</h6>
                            <span class="badge bg-white text-success rounded-pill" id="itemCount">0 Items</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-4 shadow-sm border rounded">
                            <span class="input-group-text bg-white border-0"><i class="mdi mdi-magnify fs-4 text-muted"></i></span>
                            <input type="text" id="productSearch" class="form-control border-0 py-3" placeholder="Search by UPC, Name, or SKU..." autocomplete="off" disabled>
                        </div>
                        
                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="position-relative">
                            <div class="list-group shadow border rounded position-absolute w-100 z-index-100 bg-white" style="display: none; max-height: 400px; overflow-y: auto;">
                                <!-- Results populated here -->
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="selectedItemsTable">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th style="width: 15%">UPC</th>
                                        <th style="width: 40%">Product Name</th>
                                        <th style="width: 20%" class="text-center">Quantity</th>
                                        <th style="width: 20%" class="text-end">Unit Cost</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="selectedItemsBody">
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="mb-2"><i class="mdi mdi-basket-plus-outline fs-1"></i></div>
                                            <p class="mb-0">Please select a department and search for products to add items.</p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold fs-5 py-3">Total Estimated Amount:</td>
                                        <td class="text-end fw-bold fs-5 py-3 text-success" id="totalAmount">₹0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 p-4">
                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-bold" onclick="window.history.back()">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm" id="submitBtn" disabled>
                                <i class="mdi mdi-check-circle me-1"></i> Review Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for form submission -->
        <div id="hiddenInputs"></div>
    </form>
</div>

@push('styles')
<style>
    .z-index-100 { z-index: 1000 !important; }
    .product-item:hover { background-color: #f8f9fa; cursor: pointer; }
    .select2-container .select2-selection--single { height: 45px !important; line-height: 45px !important; border: 1px solid #dee2e6; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 45px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 45px; }
</style>
@endpush

@push('scripts')
<script>
    let selectedProducts = [];
    let searchTimeout = null;

    $(document).ready(function() {
        // Initialize Select2 if available
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: 'Select Department'
            });
        }

        // Enable search only when department is selected
        $('#departmentSelect').on('change', function() {
            const hasDept = $(this).val() !== "";
            $('#productSearch').prop('disabled', !hasDept);
            if (!hasDept) {
                $('#productSearch').val('');
                $('#searchResults div').hide();
                // Clear products if dept changes? (Optional decision)
            }
        });

        // Search Input Handler
        $('#productSearch').on('input', function() {
            clearTimeout(searchTimeout);
            const term = $(this).val().trim();
            const deptId = $('#departmentSelect').val();

            if (term.length < 2) {
                $('#searchResults div').hide();
                return;
            }

            searchTimeout = setTimeout(() => {
                $.ajax({
                    url: '{{ route("inventory.search-products") }}',
                    data: { term: term, department_id: deptId },
                    success: function(products) {
                        renderSearchResults(products);
                    }
                });
            }, 300);
        });

        // Close search results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchResults, #productSearch').length) {
                $('#searchResults div').hide();
            }
        });
    });

    function renderSearchResults(products) {
        const $container = $('#searchResults div');
        $container.empty();

        if (products.length === 0) {
            $container.append('<div class="list-group-item text-muted text-center py-3">No products found in this department.</div>');
        } else {
            products.forEach(p => {
                const isAdded = selectedProducts.some(sp => sp.id === p.id);
                const html = `
                    <div class="list-group-item product-item p-3 border-bottom d-flex justify-content-between align-items-center ${isAdded ? 'opacity-50 pointer-events-none' : ''}" 
                         onclick="addProduct(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                        <div>
                            <div class="fw-bold text-dark">${p.product_name}</div>
                            <div class="small text-muted font-monospace">${p.upc || p.sku || 'N/A'}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-primary">₹${(p.cost_price || 0).toFixed(2)}</div>
                            <small class="text-muted">${p.unit || 'units'}</small>
                        </div>
                    </div>
                `;
                $container.append(html);
            });
        }
        $container.show();
    }

    function addProduct(product) {
        if (selectedProducts.some(p => p.id === product.id)) return;

        selectedProducts.push({
            id: product.id,
            name: product.product_name,
            upc: product.upc || product.sku || 'N/A',
            cost: product.cost_price || 0,
            quantity: 1
        });

        $('#productSearch').val('');
        $('#searchResults div').hide();
        renderSelectedItems();
    }

    function renderSelectedItems() {
        const $body = $('#selectedItemsBody');
        const $hidden = $('#hiddenInputs');
        $body.empty();
        $hidden.empty();

        if (selectedProducts.length === 0) {
            $('#emptyRow').show().appendTo($body);
            $('#submitBtn').prop('disabled', true);
            $('#itemCount').text('0 Items');
            $('#totalAmount').text('₹0.00');
            return;
        }

        $('#emptyRow').hide();
        $('#submitBtn').prop('disabled', false);

        let total = 0;
        selectedProducts.forEach((p, index) => {
            const itemTotal = p.quantity * p.cost;
            total += itemTotal;

            const row = `
                <tr>
                    <td class="font-monospace text-muted small">${p.upc}</td>
                    <td class="fw-bold text-dark">${p.name}</td>
                    <td>
                        <div class="input-group input-group-sm mx-auto" style="width: 120px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty(${index}, -1)">-</button>
                            <input type="number" class="form-control text-center" value="${p.quantity}" onchange="setQty(${index}, this.value)" min="1">
                            <button type="button" class="btn btn-outline-secondary" onclick="updateQty(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td class="text-end fw-bold">₹${p.cost.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeItem(${index})">
                            <i class="mdi mdi-delete-outline fs-4"></i>
                        </button>
                    </td>
                </tr>
            `;
            $body.append(row);

            // Add hidden inputs for form submission
            $hidden.append(`<input type="hidden" name="products[${index}][product_id]" value="${p.id}">`);
            $hidden.append(`<input type="hidden" name="products[${index}][quantity]" value="${p.quantity}">`);
        });

        $('#itemCount').text(`${selectedProducts.length} Items`);
        $('#totalAmount').text(`₹${total.toFixed(2)}`);
    }

    function updateQty(index, delta) {
        selectedProducts[index].quantity = Math.max(1, selectedProducts[index].quantity + delta);
        renderSelectedItems();
    }

    function setQty(index, value) {
        selectedProducts[index].quantity = Math.max(1, parseInt(value) || 1);
        renderSelectedItems();
    }

    function removeItem(index) {
        selectedProducts.splice(index, 1);
        renderSelectedItems();
    }

    $('#orderForm').on('submit', function(e) {
        if (selectedProducts.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'No Items Added',
                text: 'Please add at least one product to your order.'
            });
            return false;
        }
    });
</script>
@endpush
</x-app-layout>