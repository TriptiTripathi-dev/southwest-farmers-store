<x-app-layout title="Create Purchase Order">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-file-document-plus text-primary me-2"></i> Create Purchase Order
            </h4>
            <small class="text-muted">Add products to create a new purchase order</small>
        </div>
        <a href="{{ route('store.stock-control.requests') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form action="{{ route('store.stock-control.requests.store') }}" method="POST" id="poForm">
        @csrf
        
        <div class="row">
            <!-- Left: Product Search -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="mdi mdi-magnify me-2"></i>Search Products</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Type to search by UPC or Name</label>
                            <input type="text" id="productSearch" class="form-control form-control-lg" placeholder="Start typing..." autocomplete="off">
                        </div>
                        
                        <!-- Search Results -->
                        <div id="searchResults" class="border rounded" style="max-height: 500px; overflow-y: auto; display: none;">
                            <!-- Results will be populated here -->
                        </div>
                        
                        <div id="searchEmpty" class="text-center text-muted py-4" style="display: none;">
                            <i class="mdi mdi-magnify fs-1"></i>
                            <p>No products found</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Selected Products -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="mdi mdi-cart me-2"></i>Selected Products</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="selectedProductsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">UPC</th>
                                        <th>Product</th>
                                        <th class="text-center">C/W</th>
                                        <th style="width: 100px;">Qty</th>
                                        <th style="width: 120px;">Unit Cost</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center" style="width: 60px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="selectedProductsBody">
                                    <tr id="emptyRow">
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-cart-outline fs-1"></i>
                                            <p>No products added yet. Search and add products from the left.</p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total Items:</td>
                                        <td class="text-end fw-bold" id="totalItems">0</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold fs-5">Total Amount:</td>
                                        <td class="text-end fw-bold text-success fs-5" id="totalAmount">₹0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Add any notes or special instructions..."></textarea>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between gap-2 align-items-center mb-3">
                    <div id="palletEstimateBadge" class="badge bg-secondary fs-6 py-2 px-3 shadow-sm d-none">
                        <i class="mdi mdi-package-variant-closed me-1"></i> Estimated Pallets: <span id="palletCount">0</span>
                        <small class="d-block mt-1 opacity-75 fw-normal" id="palletWeightInfo"></small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('store.stock-control.requests') }}" class="btn btn-secondary">
                            <i class="mdi mdi-close me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                            <i class="mdi mdi-check me-1"></i> Generate PO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for line items -->
        <div id="hiddenInputs"></div>
    </form>

</div>

@push('scripts')
<script>
let selectedProducts = [];
let searchTimeout = null;
let palletEstimateTimeout = null;

// Debounce search
$('#productSearch').on('input', function() {
    clearTimeout(searchTimeout);
    const term = $(this).val().trim();
    
    if (term.length < 2) {
        $('#searchResults').hide();
        $('#searchEmpty').hide();
        return;
    }
    
    searchTimeout = setTimeout(() => searchProducts(term), 300);
});

function searchProducts(term) {
    $.ajax({
        url: '{{ route("store.stock-control.search-products") }}',
        data: { term: term },
        success: function(products) {
            if (products.length > 0) {
                renderSearchResults(products);
                $('#searchResults').show();
                $('#searchEmpty').hide();
            } else {
                $('#searchResults').hide();
                $('#searchEmpty').show();
            }
        }
    });
}

function renderSearchResults(products) {
    let html = '';
    products.forEach(p => {
        const isAdded = selectedProducts.some(sp => sp.id === p.id);
        const inTransitWarning = p.in_transit > 0 ? `<small class="text-warning"><i class="mdi mdi-alert"></i> ${p.in_transit} in transit</small>` : '';
        
        html += `
            <div class="product-result-item p-3 border-bottom ${isAdded ? 'bg-light' : ''}" style="cursor: pointer;" onclick="addProduct(${p.id}, ${isAdded})">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="font-monospace text-primary small mb-1">
                            <i class="mdi mdi-barcode"></i> ${p.upc || 'N/A'}
                        </div>
                        <div class="fw-bold">${p.product_name}</div>
                        <div class="small text-muted">
                            Stock: <span class="badge bg-info bg-opacity-10 text-info">${p.current_stock}</span>
                            ${inTransitWarning}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="badge ${p.unit_type === 'weight' ? 'bg-warning' : 'bg-primary'} bg-opacity-10 border">
                            ${p.unit_type === 'weight' ? 'W' : 'C'}
                        </div>
                        ${isAdded ? '<div class="text-success small mt-1"><i class="mdi mdi-check-circle"></i> Added</div>' : ''}
                    </div>
                </div>
            </div>
        `;
    });
    $('#searchResults').html(html);
}

function addProduct(productId, isAdded) {
    if (isAdded) {
        Swal.fire('Already Added', 'This product is already in your PO', 'info');
        return;
    }
    
    // Fetch product details
    $.ajax({
        url: '{{ route("store.stock-control.search-products") }}',
        data: { term: '', product_id: productId },
        success: function(products) {
            if (products.length > 0) {
                const product = products[0];
                
                // Requirement 12.1: Block if Stock + Transit >= Requirement (Max Stock)
                const totalAvailable = (product.current_stock || 0) + (product.in_transit || 0);
                const requirement = product.max_stock || 0;

                if (requirement > 0 && totalAvailable >= requirement) {
                    Swal.fire({
                        title: 'Order Blocked',
                        html: `You have sufficient stock.<br>Current: ${product.current_stock}<br>In-Transit: ${product.in_transit}<br>Max Level: ${requirement}`,
                        icon: 'error'
                    });
                    return;
                }

                // Check in-transit warning
                if (product.in_transit > 0) {
                    Swal.fire({
                        title: 'In-Transit Warning',
                        html: `This product has <strong>${product.in_transit} units</strong> already in transit.<br>Do you still want to order?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Add It'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            addToSelectedProducts(product);
                        }
                    });
                } else {
                    addToSelectedProducts(product);
                }
            }
        }
    });
}

function addToSelectedProducts(product) {
    selectedProducts.push({
        id: product.id,
        upc: product.upc || 'N/A',
        product_name: product.product_name,
        unit_type: product.unit_type,
        cost_price: product.cost_price || 0,
        quantity: 1
    });
    
    renderSelectedProducts();
    $('#productSearch').val('').focus();
    $('#searchResults').hide();
}

function renderSelectedProducts() {
    if (selectedProducts.length === 0) {
        $('#emptyRow').show();
        $('#submitBtn').prop('disabled', true);
        updateTotals();
        return;
    }
    
    $('#emptyRow').hide();
    $('#submitBtn').prop('disabled', false);
    
    let html = '';
    selectedProducts.forEach((p, index) => {
        const total = p.quantity * p.cost_price;
        html += `
            <tr>
                <td class="ps-3">
                    <small class="font-monospace text-dark">${p.upc}</small>
                </td>
                <td>${p.product_name}</td>
                <td class="text-center">
                    <span class="badge ${p.unit_type === 'weight' ? 'bg-warning' : 'bg-primary'} bg-opacity-10">
                        ${p.unit_type === 'weight' ? 'W' : 'C'}
                    </span>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" min="1" value="${p.quantity}" 
                           onchange="updateQuantity(${index}, this.value)">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" step="0.01" min="0" value="${p.cost_price}" 
                           onchange="updateCost(${index}, this.value)">
                </td>
                <td class="text-end fw-bold">₹${total.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeProduct(${index})" title="Remove">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    $('#selectedProductsBody').html(html);
    updateTotals();
    updateHiddenInputs();
}

function updateQuantity(index, value) {
    selectedProducts[index].quantity = parseInt(value) || 1;
    renderSelectedProducts();
}

function updateCost(index, value) {
    selectedProducts[index].cost_price = parseFloat(value) || 0;
    renderSelectedProducts();
}

function removeProduct(index) {
    selectedProducts.splice(index, 1);
    renderSelectedProducts();
}

function updateTotals() {
    const totalItems = selectedProducts.length;
    const totalAmount = selectedProducts.reduce((sum, p) => sum + (p.quantity * p.cost_price), 0);
    
    $('#totalItems').text(totalItems);
    $('#totalAmount').text('₹' + totalAmount.toFixed(2));
    
    estimatePallets();
}

function estimatePallets() {
    clearTimeout(palletEstimateTimeout);
    
    if (selectedProducts.length === 0) {
        $('#palletEstimateBadge').addClass('d-none');
        return;
    }
    
    $('#palletEstimateBadge').removeClass('d-none bg-success bg-warning bg-danger').addClass('bg-secondary');
    $('#palletCount').html('<i class="mdi mdi-spin mdi-loading"></i>');
    $('#palletWeightInfo').text('Calculating...');

    palletEstimateTimeout = setTimeout(() => {
        const payload = selectedProducts.map(p => ({
            product_id: p.id,
            quantity: p.quantity
        }));

        $.ajax({
            url: '{{ route("store.stock-control.estimate-pallets") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: payload
            },
            success: function(response) {
                if (response.success) {
                    $('#palletCount').text(response.total_pallets);
                    $('#palletWeightInfo').text(response.total_weight.toFixed(2) + ' lbs total');
                    
                    $('#palletEstimateBadge').removeClass('bg-secondary');
                    if (response.total_pallets === 0) {
                         $('#palletEstimateBadge').addClass('bg-secondary');
                    } else if (response.total_pallets < 5) {
                        $('#palletEstimateBadge').addClass('bg-success');
                    } else if (response.total_pallets < 10) {
                        $('#palletEstimateBadge').addClass('bg-warning text-dark');
                    } else {
                        $('#palletEstimateBadge').addClass('bg-danger');
                    }
                }
            },
            error: function() {
                $('#palletEstimateBadge').removeClass('bg-success bg-warning bg-danger').addClass('bg-secondary');
                $('#palletCount').text('Error');
                $('#palletWeightInfo').text('Could not estimate');
            }
        });
    }, 500); // 500ms debounce
}

function updateHiddenInputs() {
    let html = '';
    selectedProducts.forEach((p, index) => {
        html += `
            <input type="hidden" name="products[${index}][product_id]" value="${p.id}">
            <input type="hidden" name="products[${index}][quantity]" value="${p.quantity}">
            <input type="hidden" name="products[${index}][unit_cost]" value="${p.cost_price}">
        `;
    });
    $('#hiddenInputs').html(html);
}

// Form validation
$('#poForm').on('submit', function(e) {
    if (selectedProducts.length === 0) {
        e.preventDefault();
        Swal.fire('No Products', 'Please add at least one product to the PO', 'error');
        return false;
    }
});
</script>
@endpush

</x-app-layout>
