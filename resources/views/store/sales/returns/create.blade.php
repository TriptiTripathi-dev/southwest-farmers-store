<x-app-layout title="Process Return">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <!-- Header Section -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <a href="{{ route('store.sales.returns.index') }}" class="text-muted">
                            <i class="mdi mdi-arrow-left"></i>
                        </a>
                        <h2 class="mb-0 fw-bold">Process Customer Return</h2>
                    </div>
                    <p class="text-muted mb-0">Search for an invoice and process return items</p>
                </div>

                <!-- Invoice Search Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex gap-2 align-items-end">
                            <div class="flex-grow-1">
                                <label class="form-label fw-bold text-dark mb-2">
                                    <i class="mdi mdi-file-search-outline me-2"></i>Search Invoice
                                </label>
                                <input type="text" id="invoiceInput" class="form-control form-control-lg border-2" 
                                       placeholder="Enter Invoice Number (e.g. INV-20260131-0001)"
                                       autocomplete="off">
                                <small class="text-muted d-block mt-2">
                                    <i class="mdi mdi-information-outline me-1"></i>Enter the original invoice number to retrieve the sale details
                                </small>
                            </div>
                            <button class="btn btn-primary btn-lg px-5" type="button" onclick="searchInvoice()">
                                <i class="mdi mdi-magnify me-2"></i>Search
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Return Form (Hidden Initially) -->
                <form id="returnForm" method="POST" action="{{ route('store.sales.returns.store') }}" style="display:none;">
                    @csrf
                    <input type="hidden" name="sale_id" id="saleId">
                    <input type="hidden" name="customer_id" id="customerId">
                    <input type="hidden" name="total_refund" id="totalRefundInput">

                    <!-- Sale Information Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-bottom py-3">
                            <h6 class="mb-0 fw-bold">
                                <i class="mdi mdi-receipt-text-outline me-2 text-primary"></i>Sale Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-20 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="mdi mdi-account-outline text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Customer</small>
                                            <h6 class="mb-0 fw-bold" id="custName">-</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-success bg-opacity-20 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="mdi mdi-calendar-outline text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Sale Date</small>
                                            <h6 class="mb-0 fw-bold" id="saleDate">-</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Return Items Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-bottom py-3">
                            <h6 class="mb-0 fw-bold">
                                <i class="mdi mdi-package-variant-outline me-2 text-info"></i>Items to Return
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 fw-bold text-uppercase small">Product</th>
                                        <th class="fw-bold text-uppercase small">Original Qty</th>
                                        <th class="fw-bold text-uppercase small">Unit Price</th>
                                        <th class="fw-bold text-uppercase small">Return Qty</th>
                                        <th class="text-end fw-bold text-uppercase small pe-4">Refund Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable">
                                    <!-- Populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Return Reason Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light border-bottom py-3">
                            <h6 class="mb-0 fw-bold">
                                <i class="mdi mdi-comment-text-outline me-2 text-warning"></i>Return Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <label class="form-label fw-bold text-dark mb-3">Return Reason / Note</label>
                            <textarea name="reason" class="form-control border-2" rows="3" 
                                    placeholder="Specify the reason for return (e.g., Defective product, Wrong size, Changed mind, Damaged packaging)"></textarea>
                            <small class="text-muted d-block mt-2">
                                <i class="mdi mdi-information-outline me-1"></i>Provide details to help track return patterns
                            </small>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="card border-0 shadow-lg mb-4 bg-gradient">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">Items Selected:</span>
                                        <span class="badge bg-info" id="itemsCount">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">Total Units:</span>
                                        <span class="badge bg-secondary" id="totalUnits">0</span>
                                    </div>
                                </div>
                                <div class="col-md-6 border-start">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">Subtotal:</span>
                                        <span class="fw-bold" id="subtotalDisplay">$0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Tax Included:</span>
                                        <span class="fw-bold" id="taxDisplay">$0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-bold text-dark">Total Refund:</span>
                                    <span class="fs-3 fw-bold text-danger" id="totalRefundDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 mb-4">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold flex-grow-1">
                            <i class="mdi mdi-check-circle-outline me-2"></i>Confirm Return
                        </button>
                        <a href="{{ route('store.sales.returns.create') }}" class="btn btn-outline-secondary btn-lg fw-bold">
                            <i class="mdi mdi-refresh me-2"></i>Clear
                        </a>
                    </div>
                </form>

                <!-- Empty State (Hidden Initially) -->
                <div id="emptyState" class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="mdi mdi-magnify-off display-1 text-muted"></i>
                        </div>
                        <h5 class="text-muted fw-bold">No Invoice Selected</h5>
                        <p class="text-muted mb-0">Search for an invoice number above to get started</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        let currentTaxRate = 0;
        let saleItems = []; // Store items for calculation

        function searchInvoice() {
            let inv = document.getElementById('invoiceInput').value.trim();
            if(!inv) return Swal.fire('Error', 'Please enter invoice number', 'error');

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
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">${item.product.product_name}</div>
                                <small class="text-muted d-block">#${item.product_id}</small>
                                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border border-2">
                                    ${item.quantity} ${item.quantity > 1 ? 'units' : 'unit'}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">$${unitPrice.toFixed(2)}</div>
                                <small class="text-success d-block">+ $${taxPerUnit.toFixed(2)} tax</small>
                            </td>
                            <td>
                                <input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty-input text-center border-2 fw-bold" 
                                    min="0" max="${item.quantity}" value="0" 
                                    data-unit-price="${unitPrice}"
                                    data-product-name="${item.product.product_name}"
                                    oninput="calcTotal()">
                            </td>
                            <td class="text-end pe-4">
                                <span class="fw-bold text-danger item-total">$0.00</span>
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
                    Swal.fire('Error', 'Something went wrong', 'error');
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
                    Swal.fire('Limit Reached', `Cannot return more than ${max} unit(s) of "${input.dataset.productName}"`, 'warning'); 
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
                searchInvoice();
            }
        });
    </script>
@endpush
</x-app-layout>