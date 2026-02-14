<x-app-layout title="Process Return">
    @push('styles')
    <style>
        body { font-family: 'Manrope', sans-serif; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Subtle transition for quantity input */
        .qty-input { transition: border-color 0.2s, box-shadow 0.2s; }
        .qty-input:focus { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    
                    {{-- HEADER SECTION --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('store.sales.returns.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Back to Returns">
                                <i class="mdi mdi-arrow-left fs-5 text-dark"></i>
                            </a>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="mdi mdi-keyboard-return text-danger me-2"></i>Process Customer Return
                                </h4>
                                <p class="text-muted small mb-0 mt-1">Search for an invoice to select and refund items</p>
                            </div>
                        </div>
                    </div>

                    {{-- SEARCH CARD --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-9">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1 mb-2">
                                        <i class="mdi mdi-file-search-outline text-primary me-1"></i>Search Invoice
                                    </label>
                                    <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-magnify"></i></span>
                                        <input type="text" id="invoiceInput" class="form-control bg-light border-start-0 py-3 fs-6" 
                                            placeholder="Enter Invoice Number (e.g. INV-20260131-0001)" autocomplete="off">
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="mdi mdi-information-outline me-1"></i>Enter the original invoice number to retrieve sale details.
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <button class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3 py-3" type="button" onclick="searchInvoice()">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RETURN FORM (Hidden Initially) --}}
                    <form id="returnForm" method="POST" action="{{ route('store.sales.returns.store') }}" style="display:none;" class="animate__animated animate__fadeIn">
                        @csrf
                        <input type="hidden" name="sale_id" id="saleId">
                        <input type="hidden" name="customer_id" id="customerId">
                        <input type="hidden" name="total_refund" id="totalRefundInput">

                        {{-- SALE INFORMATION CARD --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                            <div class="card-header bg-light border-bottom p-4">
                                <h6 class="mb-0 fw-bold text-dark">
                                    <i class="mdi mdi-receipt-text-outline me-2 text-primary"></i>Sale Information
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-12 col-md-6 border-end-md">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px;">
                                                <i class="mdi mdi-account-outline text-primary fs-3"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block fw-bold text-uppercase letter-spacing-1 mb-1">Customer</small>
                                                <h6 class="mb-0 fw-bold text-dark fs-5" id="custName">-</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-success bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 54px; height: 54px;">
                                                <i class="mdi mdi-calendar-outline text-success fs-3"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block fw-bold text-uppercase letter-spacing-1 mb-1">Sale Date</small>
                                                <h6 class="mb-0 fw-bold text-dark fs-5" id="saleDate">-</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RETURN ITEMS CARD --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                            <div class="card-header bg-light border-bottom p-4 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-dark">
                                    <i class="mdi mdi-package-variant-outline me-2 text-info"></i>Items to Return
                                </h6>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">Set quantity to 0 to skip</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive custom-scrollbar">
                                    <table class="table table-hover align-middle mb-0 text-nowrap">
                                        <thead class="bg-white">
                                            <tr>
                                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase border-bottom">Product Details</th>
                                                <th class="py-3 text-muted small fw-bold text-uppercase border-bottom text-center">Orig. Qty</th>
                                                <th class="py-3 text-muted small fw-bold text-uppercase border-bottom">Unit Price</th>
                                                <th class="py-3 text-muted small fw-bold text-uppercase border-bottom" style="width: 150px;">Return Qty</th>
                                                <th class="text-end pe-4 py-3 text-muted small fw-bold text-uppercase border-bottom">Refund Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTable">
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            {{-- RETURN REASON CARD --}}
                            <div class="col-12 col-lg-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header bg-light border-bottom p-4">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            <i class="mdi mdi-comment-text-outline me-2 text-warning"></i>Return Details
                                        </h6>
                                    </div>
                                    <div class="card-body p-4 d-flex flex-column">
                                        <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1 mb-2">Return Reason / Note</label>
                                        <textarea name="reason" class="form-control bg-light border-0 shadow-sm flex-grow-1 p-3" style="resize: none;"
                                            placeholder="Specify the reason for return (e.g., Defective product, Wrong size, Changed mind)"></textarea>
                                        <div class="form-text mt-2">
                                            <i class="mdi mdi-information-outline me-1"></i>Providing details helps track store return patterns.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SUMMARY CARD --}}
                            <div class="col-12 col-lg-6">
                                <div class="card border-0 shadow-lg rounded-4 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                                    <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-between">
                                        
                                        <div class="row g-3 mb-4">
                                            <div class="col-6">
                                                <div class="p-3 bg-white border rounded-3 shadow-sm text-center">
                                                    <span class="d-block text-muted small fw-bold text-uppercase mb-1">Items Selected</span>
                                                    <span class="fs-4 fw-black text-info" id="itemsCount">0</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-3 bg-white border rounded-3 shadow-sm text-center">
                                                    <span class="d-block text-muted small fw-bold text-uppercase mb-1">Total Units</span>
                                                    <span class="fs-4 fw-black text-secondary" id="totalUnits">0</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted fw-semibold">Subtotal Refund</span>
                                            <span class="fw-bold text-dark" id="subtotalDisplay">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <span class="text-muted fw-semibold">Tax Refund Included</span>
                                            <span class="fw-bold text-dark" id="taxDisplay">$0.00</span>
                                        </div>

                                        <div class="border-top border-2 border-danger border-opacity-25 pt-4">
                                            <div class="d-flex justify-content-between align-items-end">
                                                <span class="fs-5 fw-bold text-muted text-uppercase letter-spacing-1">Total Refund</span>
                                                <span class="display-6 fw-black text-danger" id="totalRefundDisplay">$0.00</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="d-flex flex-column flex-sm-row gap-3 mt-5 border-top pt-4">
                            <a href="{{ route('store.sales.returns.create') }}" class="btn btn-light border btn-lg rounded-pill fw-bold shadow-sm px-5 order-2 order-sm-1 text-muted">
                                <i class="mdi mdi-refresh me-2"></i>Clear Search
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold shadow-sm flex-grow-1 order-1 order-sm-2 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-check-circle-outline me-2 fs-5"></i>Confirm Return & Refund
                            </button>
                        </div>
                    </form>

                    {{-- EMPTY STATE (Visible Initially) --}}
                    <div id="emptyState" class="card border-0 shadow-sm rounded-4">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="mdi mdi-file-search-outline display-4 text-muted opacity-50"></i>
                                </div>
                            </div>
                            <h5 class="text-dark fw-bold mb-2">No Invoice Selected</h5>
                            <p class="text-muted mb-0">Search for an invoice number above to fetch order details and process a return.</p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentTaxRate = 0;
        let saleItems = []; // Store items for calculation

        function searchInvoice() {
            let inv = document.getElementById('invoiceInput').value.trim();
            if(!inv) return Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Please enter invoice number', showConfirmButton: false, timer: 2000});

            let btn = document.querySelector('button[onclick="searchInvoice()"]');
            let originalText = btn.innerHTML;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-2"></i>Searching...';
            btn.disabled = true;

            fetch(`{{ route('store.sales.returns.search') }}?invoice=${encodeURIComponent(inv)}`)
                .then(res => res.json())
                .then(data => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;

                    if(!data.status) {
                        document.getElementById('returnForm').style.display = 'none';
                        document.getElementById('emptyState').style.display = 'block';
                        return Swal.fire('Not Found', data.message, 'warning');
                    }
                    
                    let sale = data.sale;
                    currentTaxRate = parseFloat(sale.tax_rate) || 0;
                    saleItems = sale.items;

                    document.getElementById('returnForm').style.display = 'block';
                    document.getElementById('emptyState').style.display = 'none';
                    document.getElementById('saleId').value = sale.id;
                    document.getElementById('customerId').value = sale.customer_id;
                    document.getElementById('custName').innerText = sale.customer ? sale.customer.name : 'Walk-in Customer';
                    document.getElementById('saleDate').innerText = new Date(sale.created_at).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });

                    let html = '';
                    sale.items.forEach((item, index) => {
                        let unitPrice = parseFloat(item.price);
                        let taxPerUnit = unitPrice * currentTaxRate;

                        html += `
                        <tr class="bg-white border-bottom">
                            <td class="ps-4">
                                <div class="fw-bold text-dark">${item.product.product_name}</div>
                                <div class="text-muted font-monospace small mt-1"><i class="mdi mdi-barcode me-1"></i>${item.product.sku}</div>
                                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    ${item.quantity} ${item.quantity > 1 ? 'Units' : 'Unit'}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">$${unitPrice.toFixed(2)}</div>
                                <small class="text-success fw-medium">+ $${taxPerUnit.toFixed(2)} tax</small>
                            </td>
                            <td>
                                <input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty-input text-center bg-light border-0 shadow-sm rounded-3 py-2 fw-bold text-danger fs-6" 
                                    min="0" max="${item.quantity}" value="0" 
                                    data-unit-price="${unitPrice}"
                                    data-product-name="${item.product.product_name}"
                                    oninput="calcTotal()">
                            </td>
                            <td class="text-end pe-4">
                                <span class="fw-black text-danger fs-5 item-total">$0.00</span>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('itemsTable').innerHTML = html;
                    calcTotal();
                })
                .catch(err => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    console.error(err);
                    Swal.fire('Error', 'Something went wrong while fetching the invoice', 'error');
                });
        }

        function calcTotal() {
            let grandTotal = 0;
            let itemsCount = 0;
            let totalUnits = 0;
            let totalSubtotal = 0;
            let totalTax = 0;

            document.querySelectorAll('.qty-input').forEach((input) => {
                let qty = parseInt(input.value) || 0;
                let max = parseInt(input.getAttribute('max'));
                
                if(qty > max) { 
                    input.value = max; 
                    qty = max; 
                    Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: `Max ${max} units allowed for "${input.dataset.productName}"`, showConfirmButton: false, timer: 3000}); 
                }
                
                let unitPrice = parseFloat(input.dataset.unitPrice);
                let taxPerUnit = unitPrice * currentTaxRate;
                let totalPerUnit = unitPrice + taxPerUnit;
                let rowTotal = qty * totalPerUnit;
                
                input.closest('tr').querySelector('.item-total').innerText = '$' + rowTotal.toFixed(2);

                if(qty > 0) {
                    itemsCount++;
                    totalUnits += qty;
                    totalSubtotal += qty * unitPrice;
                    totalTax += qty * taxPerUnit;
                }
                
                grandTotal += rowTotal;
            });
            
            document.getElementById('totalRefundDisplay').innerText = '$' + grandTotal.toFixed(2);
            document.getElementById('subtotalDisplay').innerText = '$' + totalSubtotal.toFixed(2);
            document.getElementById('taxDisplay').innerText = '$' + totalTax.toFixed(2);
            document.getElementById('itemsCount').innerText = itemsCount;
            document.getElementById('totalUnits').innerText = totalUnits;
            document.getElementById('totalRefundInput').value = grandTotal;

            // Add visual feedback for empty selection
            let submitBtn = document.querySelector('button[type="submit"]');
            if(grandTotal === 0) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50');
            } else {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50');
            }
        }

        // Allow Enter key to search
        document.getElementById('invoiceInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                searchInvoice();
            }
        });
    </script>
    @endpush
</x-app-layout>