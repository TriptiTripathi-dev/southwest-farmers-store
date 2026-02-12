<x-app-layout title="Retail POS Pro (USA)">
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">

    <style>
        :root {
            /* USA Theme - Professional Blue/Green */
            --pos-primary: #2563eb;
            /* Royal Blue */
            --pos-primary-dark: #1e40af;
            --pos-bg: #f8fafc;
            --pos-card-bg: #ffffff;
            --pos-text: #1e293b;
            --pos-muted: #64748b;
            --pos-border: #e2e8f0;
            --pos-accent: #3b82f6;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: var(--pos-bg);
            color: var(--pos-text);
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .pos-wrapper {
            height: calc(100vh - 70px);
            overflow: hidden;
        }

        .left-panel {
            background-color: #f1f5f9;
        }

        .right-panel {
            background-color: #ffffff;
            border-left: 1px solid var(--pos-border);
        }

        .search-input {
            border: 1px solid var(--pos-border);
            background: white;
            color: var(--pos-text);
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control-custom {
            border: 1px solid var(--pos-border);
            border-radius: 0.5rem;
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
        }

        .form-control-custom:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .cat-btn {
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border: 1px solid var(--pos-border);
            background: white;
            color: var(--pos-muted);
            transition: all 0.2s;
            white-space: nowrap;
        }

        .cat-btn.active {
            background: var(--pos-primary);
            color: white;
            border-color: var(--pos-primary);
        }

        .cat-btn:hover:not(.active) {
            background-color: #e2e8f0;
            color: var(--pos-primary-dark);
        }

        .product-card {
            border: 1px solid var(--pos-border);
            border-radius: 0.75rem;
            transition: all 0.2s;
            background: white;
            cursor: pointer;
            overflow: hidden;
            position: relative;
        }

        .product-card:hover {
            border-color: var(--pos-primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            height: 120px;
            background-size: cover;
            background-position: center;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            background-color: #f8fafc;
        }

        .badge-stock {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .badge-in-stock {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-low-stock {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .cart-item {
            border-bottom: 1px dashed var(--pos-border);
            padding: 0.75rem 0;
            transition: all 0.2s;
        }

        .cart-item:hover {
            background-color: #f8fafc;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background: #e2e8f0;
            color: var(--pos-text);
            border: none;
            cursor: pointer;
        }

        .qty-btn:hover {
            background: var(--pos-primary);
            color: white;
        }

        .discount-input {
            width: 80px;
            padding: 0.25rem 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            text-align: right;
        }

        .discount-input:focus {
            outline: none;
            border-color: var(--pos-primary);
        }

        .btn-pay {
            background: var(--pos-primary);
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 0.75rem;
            border: none;
            padding: 1rem;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-pay:hover:not(:disabled) {
            background-color: var(--pos-primary-dark);
            transform: translateY(-1px);
        }

        .btn-pay:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-hold {
            background: white;
            border: 2px solid #f59e0b;
            color: #f59e0b;
            font-weight: 700;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }

        .btn-hold:hover:not(:disabled) {
            background: #f59e0b;
            color: white;
        }

        .btn-hold:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: #cbd5e1;
            color: #94a3b8;
        }

        .payment-method-btn {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background: white;
            cursor: pointer;
        }

        .payment-method-btn.active {
            border-color: var(--pos-primary);
            background: #eff6ff;
            color: var(--pos-primary);
        }

        .customer-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .customer-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }

        .customer-item:hover {
            background-color: #f8fafc;
        }

        .held-carts-container {
            border: 1px solid var(--pos-border);
            border-radius: 0.5rem;
            padding: 0.5rem;
            background: #f8fafc;
            max-height: 150px;
            overflow-y: auto;
            margin-bottom: 1rem;
        }

        .held-cart-item {
            background: white;
            border-radius: 0.5rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid var(--pos-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .invoice-logo {
            max-height: 60px;
            margin-bottom: 15px;
        }

        .success-icon-anim {
            font-size: 4rem;
            color: var(--pos-primary);
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body * {
                visibility: hidden;
            }

            #invoiceContent,
            #invoiceContent * {
                visibility: visible;
            }

            #invoiceContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                padding: 5mm;
            }

            .modal-footer,
            .btn-close,
            .d-print-none {
                display: none !important;
            }
        }
    </style>
    @endpush

    <div class="container-fluid pos-wrapper p-0">
        <div class="row h-100 g-0">
            <div class="col-lg-8 col-md-7 h-100 left-panel d-flex flex-column">
                <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                            <i class="mdi mdi-storefront text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">GreenPOS (USA)</h5>
                            <small class="text-muted">Store Panel</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="badge bg-light text-dark border d-flex align-items-center px-3">
                            <i class="mdi mdi-calendar me-2"></i> {{ now()->format('M d, Y') }}
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Exit</a>
                    </div>
                </div>

                <div class="px-4 py-3">
                    <div class="position-relative mb-3">
                        <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 fs-5 text-muted"></i>
                        <input type="text" id="productSearch" class="form-control search-input ps-5" placeholder="Scan Barcode or Search Product (F1)..." autofocus autocomplete="off">
                    </div>
                    <div class="d-flex gap-2 overflow-auto hide-scrollbar">
                        <button class="cat-btn active" onclick="filterCategory('all', this)">All Categories</button>
                        @foreach($categories as $cat)
                        <button class="cat-btn" onclick="filterCategory('{{ $cat->slug }}', this)">{{ $cat->name }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="flex-grow-1 overflow-auto px-4 pb-4">
                    <div id="productGrid" class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                        <div class="col-12 text-center mt-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted mt-2 small fw-bold">Loading Inventory...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-5 h-100 right-panel d-flex flex-column shadow-lg z-1">
                <div class="p-4 border-bottom position-relative bg-white">
                    <label class="small fw-bold text-uppercase text-muted mb-2">Customer</label>
                    <div class="d-flex gap-2">
                        <div class="input-group flex-nowrap position-relative shadow-sm">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3 ps-3"><i class="mdi mdi-account-search"></i></span>
                            <input type="text" id="customerSearch" class="form-control border-start-0" placeholder="Name or Phone..." autocomplete="off">
                            <input type="hidden" id="selectedCustomerId" value="">
                            <div id="customerDropdown" class="customer-dropdown"></div>
                        </div>
                        <button class="btn btn-primary rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal" title="New Customer">
                            <i class="mdi mdi-plus fw-bold"></i>
                        </button>
                    </div>
                </div>

                <div class="px-4 pt-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted text-uppercase">Held Orders</span>
                        <span class="badge bg-warning text-dark" id="heldCountBadge">0</span>
                    </div>
                    <div class="held-carts-container" id="heldCartsList">
                        <div class="text-center text-muted py-2 small">No held orders</div>
                    </div>
                </div>

                <div class="px-4 py-2 bg-light d-flex justify-content-between align-items-center border-bottom border-top">
                    <span class="fw-bold small text-dark">
                        <i class="mdi mdi-cart-outline me-1"></i> Current Cart
                    </span>
                    <button class="btn btn-link text-danger text-decoration-none small fw-bold p-0" onclick="clearCart()">
                        <i class="mdi mdi-delete-outline me-1"></i>CLEAR
                    </button>
                </div>

                <div class="flex-grow-1 overflow-auto px-4 py-2" id="cartItems" style="
    min-height: 90px;">
                </div>

                <div class="p-4 bg-white border-top">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-bold">Subtotal</span>
                        <span class="fw-bold text-dark" id="subTotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-bold">Sales Tax (8%)</span>
                        <span class="fw-bold text-dark" id="gstAmount">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small align-items-center">
                        <span class="text-muted fw-bold">Discount</span>
                        <input type="number" id="discountInput" class="discount-input" value="0" min="0" step="0.01" placeholder="0.00">
                        <span class="fw-bold text-danger" id="discountAmount">-$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3 mb-3 pt-3 border-top border-dashed">
                        <span class="h5 fw-bold mb-0 text-dark">Total</span>
                        <span class="h2 fw-bolder text-primary mb-0" id="grandTotal">$0.00</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="payment-method-btn active text-center" onclick="selectPayment('cash', this)">
                                <i class="mdi mdi-cash fs-5 d-block"></i> <small class="fw-bold">Cash</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="payment-method-btn text-center" onclick="selectPayment('card', this)">
                                <i class="mdi mdi-credit-card fs-5 d-block"></i> <small class="fw-bold">Card</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="payment-method-btn text-center" onclick="selectPayment('check', this)">
                                <i class="mdi mdi-checkbook fs-5 d-block"></i> <small class="fw-bold">Check</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-4">
                            <button class="btn btn-hold w-100 h-100" id="holdCartBtn" onclick="holdCart()" disabled>
                                <i class="mdi mdi-pause-circle-outline d-block h4 mb-0"></i> <small>HOLD</small>
                            </button>
                        </div>
                        <div class="col-8">
                            <button class="btn-pay w-100 d-flex flex-column align-items-center justify-content-center gap-1"
                                onclick="processCheckout()"
                                id="payBtn"
                                {{ !Auth::user()->hasPermission('create_order') ? 'disabled title="Permission denied"' : '' }}>
                                <i class="mdi mdi-check-decagram fs-3"></i> <span class="small">PAY NOW (F10)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-4 border-0 shadow-lg rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="modal-title fw-bold text-primary"><i class="mdi mdi-account-plus me-2"></i>Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <form id="createCustomerForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control form-control-custom" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Phone Number <span class="text-danger">*</span></label><input type="text" name="phone" class="form-control form-control-custom" required placeholder="(555) 555-5555"></div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6"><label class="form-label small fw-bold">Email (Optional)</label><input type="email" name="email" class="form-control form-control-custom"></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Address</label><input type="text" name="address" class="form-control form-control-custom" placeholder="Street, City, State, Zip"></div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6"><label class="form-label small fw-bold">Due Amount</label><input type="number" name="due_amount" class="form-control form-control-custom" value="0" min="0" step="0.01"></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Customer Image</label><input type="file" name="image" class="form-control form-control-custom" accept="image/*"><img id="imagePreview" src="" alt="Preview" style="max-width: 100px; max-height: 100px; margin-top: 10px; display: none;"></div>
                        </div>
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-3">Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoiceModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
                <div class="modal-body" id="invoiceContent">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Store Logo" class="invoice-logo" onerror="this.style.display='none'">
                    <div class="mb-3 d-print-none"><i class="mdi mdi-check-circle success-icon-anim"></i></div>
                    <h3 class="fw-bold text-dark d-print-none">Payment Successful!</h3>

                    <div class="bg-light p-3 rounded-3 mb-4 text-start border border-dashed">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-0">{{ Auth::user()->store->store_name ?? 'US Retail Store' }}</h5>
                            <small class="text-muted">{{ Auth::user()->store->address ?? '123 Main St, New York, NY' }}</small><br>
                            <small class="text-muted">Tel: {{ Auth::user()->store->phone ?? '(555) 123-4567' }}</small>
                        </div>
                        <hr class="border-secondary border-dashed">
                        <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Invoice No:</span><span class="fw-bold text-dark" id="modalInvoiceNo">#0000</span></div>
                        <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Date:</span><span class="fw-bold text-dark">{{ now()->format('M d, Y h:i A') }}</span></div>
                        <div class="d-flex justify-content-between mb-3"><span class="text-muted small">Payment:</span><span class="fw-bold text-uppercase text-dark" id="modalPaymentMode">CASH</span></div>
                        <table class="table table-sm table-borderless mb-2 small">
                            <thead>
                                <tr class="border-bottom border-dark">
                                    <th>Item</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody id="modalInvoiceItems"></tbody>
                        </table>
                        <hr class="border-secondary border-dashed my-2">
                        <div class="d-flex justify-content-between mb-1"><span class="small">Subtotal:</span><span class="fw-bold" id="modalSubtotal">$0.00</span></div>
                        <div class="d-flex justify-content-between mb-1"><span class="small">Sales Tax (8%):</span><span class="fw-bold" id="modalTax">$0.00</span></div>
                        <div class="d-flex justify-content-between mb-2"><span class="small">Discount:</span><span class="fw-bold text-danger" id="modalDiscount">-$0.00</span></div>
                        <div class="d-flex justify-content-between border-top border-dark pt-2"><span class="fw-bold">TOTAL:</span><span class="fw-bold fs-5" id="modalAmount">$0.00</span></div>
                        <div class="text-center mt-3 small">
                            <p>Thank you!</p>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-print-none">
                        <button class="btn btn-primary fw-bold" onclick="window.print()"><i class="mdi mdi-printer me-2"></i>Print Invoice</button>
                        <button class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal"><i class="mdi mdi-plus me-2"></i>New Sale</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="paymentMethod" value="cash">

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @php
        $cartArray = [];
        // Remove the dd($currentCart) from here
        if (isset($currentCart) && $currentCart -> items) {
            foreach($currentCart -> items as $item) {
                $cartArray[] = [
                    'item_id' => $item -> id,
                    'id' => $item -> product_id,
                    'name' => $item  -> product -> product_name,
                    'price' => (float) $item  -> price,
                    'quantity' => $item  -> quantity,
                    // Ensure the stock calculation doesn't fail if relation is empty
                    'max' => $item  -> product  -> storeStocks ?
                    $item  -> product  -> storeStocks()  -> where('store_id', Auth::user()  -> store_id)  -> sum('quantity') :
                    0,
                ];
            }
        }
        @endphp
        let cart = @json($cartArray);

        let currentCategory = 'all';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let heldCarts = JSON.parse(localStorage.getItem('heldCarts')) || [];

        // USA Sales Tax Rate (Example 8%)
        const TAX_RATE = 0.08;

        $(document).ready(function() {
            $('#cartItems').scrollTop(0);
            renderCart();
            loadProducts();
            renderHeldCarts();
            $('#productSearch').focus();

            $('#productSearch').on('keyup', function() {
                loadProducts($(this).val());
            });

            $('#customerSearch').on('keyup', function() {
                let term = $(this).val();
                if (term.length < 2) {
                    $('#customerDropdown').hide();
                    return;
                }
                $.ajax({
                    url: "{{ route('store.sales.customers.search') }}",
                    data: {
                        term: term
                    },
                    success: function(customers) {
                        let html = '';
                        if (customers.length === 0) {
                            html = '<div class="p-3 text-muted small">No customer found</div>';
                        } else {
                            customers.forEach(c => {
                                html += `<div class="customer-item" onclick="selectCustomer(${c.id}, '${c.name}', '${c.phone}')">
                                            <div class="fw-bold text-dark">${c.name}</div>
                                            <div class="small text-muted">${c.phone}</div>
                                         </div>`;
                            });
                        }
                        $('#customerDropdown').html(html).show();
                    }
                });
            });

            document.querySelector('[name="image"]')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('imagePreview');
                if (file) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            });

            $('#createCustomerForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('store.sales.customers.store') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            $('#addCustomerModal').modal('hide');
                            selectCustomer(res.customer.id, res.customer.name, res.customer.phone);
                            Swal.fire({
                                icon: 'success',
                                title: 'Customer Added',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#createCustomerForm')[0].reset();
                            $('#imagePreview').hide();
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Failed to save customer.', 'error');
                    }
                });
            });

            $('#discountInput').on('input', function() {
                renderCart();
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'F1') {
                    e.preventDefault();
                    $('#productSearch').focus();
                }
                if (e.key === 'F10') {
                    e.preventDefault();
                    processCheckout();
                }
            });
        });

        function selectCustomer(id, name, phone) {
            $('#selectedCustomerId').val(id);
            $('#customerSearch').val(name + ' (' + phone + ')');
            $('#customerDropdown').hide();
        }

        function filterCategory(slug, btn) {
            $('.cat-btn').removeClass('active');
            $(btn).addClass('active');
            currentCategory = slug;
            loadProducts($('#productSearch').val());
        }

        function loadProducts(term = '') {
            $.ajax({
                url: "{{ route('store.sales.search') }}",
                data: {
                    term: term,
                    category: currentCategory
                },
                success: function(products) {
                    let html = '';
                    if (products.length === 0) {
                        html = '<div class="col-12 text-center text-muted mt-5"><i class="mdi mdi-package-variant fs-1 opacity-25"></i><p>No products found.</p></div>';
                    } else {
                        products.forEach(p => {
                            let badge = p.quantity <= 5 ? `<span class="badge-stock badge-low-stock">Low: ${p.quantity}</span>` : `<span class="badge-stock badge-in-stock">${p.quantity} In Stock</span>`;
                            if (p.quantity == 0) badge = `<span class="badge-stock badge-low-stock">Out of Stock</span>`;
                            // Ensure image works, fallback to placeholder
                            let img = p.image ? `/storage/${p.image}` : `https://placehold.co/200x200/ecfdf5/10b981?text=${p.product_name.charAt(0)}`;
                            html += `<div class="col">
                                <div class="product-card p-3 h-100 d-flex flex-column" onclick="addToCart(${p.product_id}, '${p.product_name.replace(/'/g, "\\'")}', ${p.price}, ${p.quantity})">
                                    <div class="product-img" style="background-image: url('${img}');"></div>
                                    <div class="d-flex justify-content-between align-items-start mb-2"><h6 class="fw-bold text-dark mb-0 text-truncate" title="${p.product_name}">${p.product_name}</h6>${badge}</div>
                                    <div class="mt-auto d-flex justify-content-between align-items-center"><small class="text-muted">${p.sku}</small><span class="h5 fw-bolder text-primary mb-0">$${parseFloat(p.price).toFixed(2)}</span></div>
                                </div></div>`;
                        });
                    }
                    $('#productGrid').html(html);
                }
            });
        }

        function addToCart(id, name, price, maxStock) {
            if (maxStock <= 0) return Swal.fire('Out of Stock', 'Product is currently unavailable.', 'error');

            $.ajax({
                url: "{{ route('store.sales.cart.add') }}",
                method: "POST",
                data: {
                    _token: csrfToken,
                    product_id: id,
                    quantity: 1
                },
                success: function(res) {
                    cart = res.cart;
                    renderCart();
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 800
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Added to cart'
                    });
                }
            });
        }

        function renderCart() {
            let html = '';
            let subtotal = 0;

            if (cart.length === 0) {
                $('#cartItems').html('<div class="text-center mt-5 text-muted small"><i class="mdi mdi-cart-outline fs-1 opacity-25"></i><br>Cart is empty</div>');
                updateTotals(0);
                $('#holdCartBtn, #payBtn').prop('disabled', true);
                return;
            }
            $('#holdCartBtn, #payBtn').prop('disabled', false);

            cart.forEach((item, index) => {
                subtotal += item.price * item.quantity;
                html += `<div class="cart-item d-flex align-items-center gap-2">
                    <div class="flex-grow-1"><h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width:140px;">${item.name}</h6><small class="text-primary fw-bold">$${item.price.toFixed(2)} x ${item.quantity}</small></div>
                    <div class="d-flex align-items-center bg-light rounded-3 p-1"><button class="qty-btn" onclick="updateQty(${index}, -1)">-</button><span class="fw-bold px-2 text-dark">${item.quantity}</span><button class="qty-btn" onclick="updateQty(${index}, 1)">+</button></div>
                    <div class="text-end"><span class="d-block fw-bold text-dark">$${(item.price * item.quantity).toFixed(2)}</span><i class="mdi mdi-delete-outline text-danger cursor-pointer" onclick="removeFromCart(${index})"></i></div>
                </div>`;
            });
            $('#cartItems').html(html);
            updateTotals(subtotal);
        }

        function updateTotals(subtotal) {
            let discount = parseFloat($('#discountInput').val()) || 0;
            let taxableAmount = Math.max(0, subtotal - discount);
            let tax = taxableAmount * TAX_RATE; // Changed GST_RATE to TAX_RATE
            let grandTotal = taxableAmount + tax;

            $('#subTotal').text('$' + subtotal.toFixed(2));
            $('#discountAmount').text('-$' + discount.toFixed(2));
            $('#gstAmount').text('$' + tax.toFixed(2));
            $('#grandTotal').text('$' + grandTotal.toFixed(2));
        }

        function updateQty(index, change) {
            let item = cart[index];
            let newQty = item.quantity + change;
            if (newQty < 1) return;

            $.ajax({
                url: "{{ route('store.sales.cart.update') }}",
                method: "POST",
                data: {
                    _token: csrfToken,
                    item_id: item.item_id,
                    quantity: newQty
                },
                success: function(res) {
                    cart = res.cart;
                    renderCart();
                }
            });
        }

        function removeFromCart(index) {
            let item = cart[index];
            $.ajax({
                url: "{{ route('store.sales.cart.remove') }}",
                method: "POST",
                data: {
                    _token: csrfToken,
                    item_id: item.item_id
                },
                success: function(res) {
                    cart = res.cart;
                    renderCart();
                }
            });
        }

        function clearCart() {
            if (cart.length === 0) return;
            Swal.fire({
                title: 'Clear Cart?',
                text: "All items will be removed.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, clear it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            cart = [];
                            renderCart();
                            $('#customerSearch').val('');
                            $('#selectedCustomerId').val('');
                            $('#discountInput').val(0);
                        }
                    });
                }
            });
        }

        function selectPayment(method, element) {
            $('.payment-method-btn').removeClass('active');
            $(element).addClass('active');
            $('#paymentMethod').val(method);
        }

        function holdCart() {
            if (cart.length === 0) return Swal.fire('Empty Cart', 'Please add items before holding.', 'info');
            Swal.fire({
                title: 'Hold Order?',
                text: "Save to 'Held Orders' and clear screen.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Yes, Hold Order'
            }).then((result) => {
                if (result.isConfirmed) {
                    const holdObj = {
                        id: Date.now(),
                        customer: $('#customerSearch').val() || 'Walk-in',
                        customerId: $('#selectedCustomerId').val(),
                        items: cart,
                        discount: parseFloat($('#discountInput').val()) || 0,
                        total: parseFloat($('#grandTotal').text().replace('$', '')),
                        date: new Date().toLocaleString()
                    };
                    heldCarts.push(holdObj);
                    localStorage.setItem('heldCarts', JSON.stringify(heldCarts));
                    renderHeldCarts();

                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            cart = [];
                            renderCart();
                            $('#customerSearch').val('');
                            $('#selectedCustomerId').val('');
                            $('#discountInput').val(0);
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Order Held Successfully'
                            });
                        }
                    });
                }
            });
        }

        function renderHeldCarts() {
            const container = $('#heldCartsList');
            if (heldCarts.length === 0) {
                container.html('<div class="text-center text-muted py-2 small">No held orders</div>');
                $('#heldCountBadge').text('0');
                return;
            }
            $('#heldCountBadge').text(heldCarts.length);
            let html = '';
            heldCarts.forEach((hold, index) => {
                html += `<div class="held-cart-item">
                    <div><div class="held-cart-name">${hold.customer}</div><div class="held-cart-info">${hold.items.length} items • $${hold.total.toFixed(2)}</div><div class="held-cart-info text-xs">${hold.date}</div></div>
                    <div class="d-flex gap-2"><button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="restoreHeldCart(${index})"><i class="mdi mdi-restore"></i></button><button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="deleteHeldCart(${index})"><i class="mdi mdi-close"></i></button></div>
                </div>`;
            });
            container.html(html);
        }

        function restoreHeldCart(index) {
            Swal.fire({
                title: 'Restore Order?',
                text: "Overwrite current cart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Restore'
            }).then((result) => {
                if (result.isConfirmed) {
                    const hold = heldCarts[index];

                    // Simple restore logic (ideally sync to DB)
                    // For quick UI response, clearing DB and restoring client side
                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            // Note: To fully sync, you'd need to loop add items to DB here
                            // For now, restoring local state visuals
                            cart = hold.items;
                            $('#discountInput').val(hold.discount || 0);
                            if (hold.customerId) {
                                $('#selectedCustomerId').val(hold.customerId);
                                $('#customerSearch').val(hold.customer);
                            }

                            heldCarts.splice(index, 1);
                            localStorage.setItem('heldCarts', JSON.stringify(heldCarts));
                            renderHeldCarts();
                            renderCart();
                            // Re-sync restored items to DB cart
                            hold.items.forEach(item => {
                                $.post("{{ route('store.sales.cart.add') }}", {
                                    _token: csrfToken,
                                    product_id: item.id,
                                    quantity: item.quantity
                                });
                            });
                        }
                    });
                }
            });
        }

        function deleteHeldCart(index) {
            heldCarts.splice(index, 1);
            localStorage.setItem('heldCarts', JSON.stringify(heldCarts));
            renderHeldCarts();
        }

        function processCheckout() {
            if (cart.length === 0) return Swal.fire('Cart Empty', 'Add items to cart first.', 'error');
            let btn = $('#payBtn');
            let originalContent = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');

            let sub = parseFloat($('#subTotal').text().replace('$', ''));
            let tax = parseFloat($('#gstAmount').text().replace('$', ''));
            let total = parseFloat($('#grandTotal').text().replace('$', ''));
            let discount = parseFloat($('#discountInput').val()) || 0;
            let custId = $('#selectedCustomerId').val();

            $.ajax({
                url: "{{ route('store.sales.checkout') }}",
                method: "POST",
                data: {
                    _token: csrfToken,
                    cart: JSON.stringify(cart),
                    customer_id: custId,
                    payment_method: $('#paymentMethod').val(),
                    status: 'completed',
                    subtotal: sub,
                    tax_amount: tax,
                    gst_amount: tax, // Backend might still expect 'gst_amount' key, sending tax value
                    discount_amount: discount,
                    total_amount: total
                },
                success: function(res) {
                    $('#modalInvoiceNo').text(res.invoice);
                    $('#modalAmount').text('$' + total.toFixed(2));
                    $('#modalSubtotal').text('$' + sub.toFixed(2));
                    $('#modalTax').text('$' + tax.toFixed(2));
                    $('#modalDiscount').text('-$' + discount.toFixed(2));
                    $('#modalPaymentMode').text($('#paymentMethod').val().toUpperCase());

                    let modalItemsHtml = '';
                    cart.forEach(item => {
                        modalItemsHtml += `<tr><td>${item.name}</td><td class="text-end">${item.quantity}</td><td class="text-end">$${item.price.toFixed(2)}</td><td class="text-end fw-bold">$${(item.price * item.quantity).toFixed(2)}</td></tr>`;
                    });
                    $('#modalInvoiceItems').html(modalItemsHtml);

                    let invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
                    invoiceModal.show();

                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: {
                            _token: csrfToken
                        },
                        success: function() {
                            cart = [];
                            renderCart();
                            $('#customerSearch').val('');
                            $('#selectedCustomerId').val('');
                            $('#discountInput').val(0);
                            loadProducts();
                        }
                    });
                },
                error: function(err) {
                    let msg = err.responseJSON?.message || 'Unknown error occurred';
                    if (err.responseJSON?.errors) {
                        msg += '\n' + Object.values(err.responseJSON.errors).join('\n');
                    }
                    Swal.fire('Transaction Failed', msg, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalContent);
                }
            });
        }
    </script>
    @endpush
</x-app-layout>