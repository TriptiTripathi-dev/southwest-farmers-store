<x-app-layout title="Retail POS Pro (USA)">
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">

    <style>
        :root {
            --pos-primary: #2563eb;
            --pos-primary-dark: #1e40af;
            --pos-bg: #f8fafc;
            --pos-card-bg: #ffffff;
            --pos-text: #1e293b;
            --pos-muted: #64748b;
            --pos-border: #e2e8f0;
            --pos-accent: #3b82f6;
            --pos-success: #10b981;
            --pos-warning: #f59e0b;
            --pos-danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .pos-wrapper {
            height: calc(100vh - 70px);
            overflow: hidden;
        }

        /* DESKTOP LAYOUT */
        @media (min-width: 992px) {
            .pos-wrapper {
                display: flex;
            }

            .left-panel {
                flex: 1;
                border-right: 1px solid var(--pos-border);
            }

            .right-panel {
                width: 380px;
                box-shadow: -5px 0 15px rgba(0, 0, 0, 0.08);
            }
        }

        /* MOBILE LAYOUT */
        @media (max-width: 991px) {
            .left-panel {
                width: 100%;
            }

            .right-panel {
                display: none;
            }

            .floating-cart-btn {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 1040;
                width: calc(100% - 32px);
                max-width: 500px;
            }
        }

        .left-panel,
        .right-panel {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* HEADER */
        .pos-header {
            background: white;
            border-bottom: 1.5px solid var(--pos-border);
            padding: 12px 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        @media (min-width: 768px) {
            .pos-header {
                padding: 16px 24px;
            }
        }

        .store-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--pos-primary), var(--pos-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        /* SEARCH & FILTERS */
        .search-input {
            border: 1.5px solid var(--pos-border);
            background: white;
            color: var(--pos-text);
            font-weight: 500;
            border-radius: 10px;
            padding: 11px 12px 11px 40px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .search-input:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        /* CATEGORY BUTTONS */
        .cat-btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            padding: 7px 14px;
            border: 1.5px solid var(--pos-border);
            background: white;
            color: var(--pos-muted);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            cursor: pointer;
        }

        .cat-btn.active {
            background: var(--pos-primary);
            color: white;
            border-color: var(--pos-primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .cat-btn:hover:not(.active) {
            border-color: #cbd5e1;
            background-color: #f1f5f9;
        }

        /* PRODUCTS GRID */
        .product-card {
            border: 1.5px solid var(--pos-border);
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            cursor: pointer;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            border-color: var(--pos-primary);
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
        }

        .product-img {
            height: 130px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            margin: 12px 12px 8px 12px;
            background-color: #f1f5f9;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .product-content {
            padding: 0 12px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-weight: 700;
            font-size: 13px;
            color: var(--pos-text);
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 8px;
            border-top: 1px solid var(--pos-border);
        }

        .product-price {
            font-size: 16px;
            font-weight: 800;
            color: var(--pos-primary);
        }

        .badge-stock {
            font-size: 10px;
            font-weight: 800;
            padding: 3px 6px;
            border-radius: 4px;
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

        /* CART ITEMS */
        .cart-item {
            border-bottom: 1px dashed var(--pos-border);
            padding: 10px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            transition: all 0.2s;
        }

        .cart-item:hover {
            background-color: #f8fafc;
            padding: 10px 8px;
            border-radius: 6px;
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-weight: 700;
            font-size: 12px;
            color: var(--pos-text);
            line-height: 1.3;
        }

        .cart-item-price {
            font-weight: 700;
            color: var(--pos-primary);
            font-size: 11px;
            margin-top: 2px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            border-radius: 6px;
            gap: 6px;
            padding: 2px;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background: transparent;
            color: var(--pos-text);
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: white;
        }

        /* PAY NOW BUTTON */
        .btn-pay {
            background: linear-gradient(135deg, var(--pos-primary), var(--pos-primary-dark));
            color: white;
            font-weight: 800;
            font-size: 15px;
            border-radius: 10px;
            border: none;
            padding: 13px;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
            }

            50% {
                box-shadow: 0 8px 30px rgba(37, 99, 235, 0.35);
            }
        }

        .btn-pay:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(37, 99, 235, 0.4);
        }

        .btn-pay:active:not(:disabled) {
            transform: translateY(-1px);
        }

        .btn-pay:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            animation: none;
        }

        /* HOLD BUTTON */
        .btn-hold {
            background: white;
            border: 2px solid var(--pos-warning);
            color: var(--pos-warning);
            font-weight: 700;
            border-radius: 10px;
            transition: all 0.3s;
            cursor: pointer;
            padding: 11px;
        }

        .btn-hold:hover:not(:disabled) {
            background: var(--pos-warning);
            color: white;
            transform: translateY(-2px);
        }

        .btn-hold:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* PAYMENT BUTTONS */
        .payment-method-btn {
            border: 1.5px solid var(--pos-border);
            border-radius: 8px;
            padding: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 11px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .payment-method-btn.active {
            border-color: var(--pos-primary);
            background: #eff6ff;
            color: var(--pos-primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        /* OFFCANVAS MOBILE */
        .offcanvas-bottom {
            border-radius: 20px 20px 0 0 !important;
            max-height: 85vh !important;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.1) !important;
        }

        .offcanvas-header {
            border-bottom: 1.5px solid var(--pos-border);
            padding: 16px;
        }

        .offcanvas-body {
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .offcanvas-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .offcanvas-footer {
            background: #f8fafc;
            padding: 16px;
            border-top: 1.5px solid var(--pos-border);
        }

        /* FLOATING CART BUTTON */
        .floating-cart-btn {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(100px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* MODALS */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1.5px solid var(--pos-border);
            padding: 20px;
            background: white;
        }

        .modal-body {
            padding: 20px;
        }

        .form-control-custom {
            border: 1.5px solid var(--pos-border);
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13px;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        /* INVOICE */
        .invoice-box {
            background: #f8fafc;
            border: 1.5px dashed var(--pos-border);
            border-radius: 10px;
            padding: 16px;
        }

        .invoice-logo {
            max-height: 50px;
        }

        .success-icon-anim {
            font-size: 4rem;
            color: var(--pos-primary);
        }

        /* DISCOUNT INPUT */
        .discount-input {
            width: 70px;
            padding: 6px 8px;
            border: 1.5px solid var(--pos-border);
            border-radius: 6px;
            font-size: 12px;
            text-align: right;
            font-weight: 600;
            transition: all 0.3s;
        }

        .discount-input:focus {
            border-color: var(--pos-primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* TOTALS */
        .totals-label {
            color: var(--pos-muted);
            font-weight: 600;
            font-size: 12px;
        }

        .totals-value {
            color: var(--pos-text);
            font-weight: 700;
            font-size: 12px;
        }

        .grand-total-value {
            font-size: 28px;
            font-weight: 900;
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

    <div class="pos-wrapper">
        <div class="left-panel">
            <div class="pos-header">
                <div class="row align-items-center g-2 g-md-3">
                    <div class="col-auto">
                        <div class="store-icon">
                            <i class="mdi mdi-storefront"></i>
                        </div>
                    </div>
                    <div class="col flex-grow-1 min-w-0">
                        <h5 class="mb-0 fw-bold text-dark" style="font-size: 15px;">{{ Auth::user()->store->store_name ?? 'GreenPOS (USA)' }}</h5>
                        <small class="text-muted d-block">{{ Auth::user()->store->address ?? 'Store Panel' }}</small>
                    </div>
                    <div class="col-auto d-none d-sm-flex gap-2">
                        <span class="badge bg-light text-dark border fw-bold" style="font-size: 11px;">
                            <i class="mdi mdi-calendar me-1"></i> {{ now()->format('M d, Y') }}
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Exit</a>
                    </div>
                    <div class="col-auto d-sm-none">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-danger btn-sm rounded-pill px-2">Exit</a>
                    </div>
                </div>
            </div>

            <div class="p-3 p-md-4">
                <div class="position-relative mb-3">
                    <i class="mdi mdi-magnify position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 18px;"></i>
                    <input type="text" id="productSearch" class="form-control search-input ps-5" placeholder="Search (F1)..." autofocus autocomplete="off">
                </div>

                <div class="d-flex gap-2 overflow-auto hide-scrollbar pb-1">
                    <button class="cat-btn active" onclick="filterCategory('all', this)">All</button>
                    @foreach($categories as $cat)
                    <button class="cat-btn" onclick="filterCategory('{{ $cat->slug }}', this)">{{ $cat->name }}</button>
                    @endforeach
                </div>
            </div>

            <div class="flex-grow-1 overflow-y-auto px-3 px-md-4 pb-4">
                <div class="row g-2 g-md-3 pt-2" id="productGrid">
                    <div class="col-12 text-center py-8">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-3 fw-bold">Loading Products...</p>
                    </div>
                </div>
            </div>

            <div class="floating-cart-btn d-lg-none">
                <button class="btn btn-primary w-100 fw-bold py-3 rounded-3 d-flex align-items-center justify-content-between" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <span class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-cart"></i>
                        <span id="mobileItemCount">0</span> items
                    </span>
                    <span id="mobileTotal" class="fw-bold">$0.00</span>
                </button>
            </div>
        </div>

        <div class="right-panel d-none d-lg-flex">
            <div class="p-4 border-bottom position-relative bg-white flex-shrink-0">
                <label class="small fw-bold text-uppercase text-muted mb-2">Customer</label>
                <div class="d-flex gap-2">
                    <div class="input-group flex-nowrap position-relative">
                        <span class="input-group-text bg-white border-end-0 rounded-start-3 ps-3"><i class="mdi mdi-account-search"></i></span>
                        <input type="text" id="customerSearch" class="form-control border-start-0 form-control-custom" placeholder="Name..." autocomplete="off">
                        <input type="hidden" id="selectedCustomerId" value="">
                        <div id="customerDropdown" class="position-absolute w-100 bg-white border rounded-3 shadow-sm mt-1 hidden overflow-y-auto" style="top: 100%; max-height: 150px; z-index: 100;"></div>
                    </div>
                    <button class="btn btn-primary rounded-3 px-3 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                        <i class="mdi mdi-plus fw-bold"></i>
                    </button>
                </div>
            </div>

            <div class="px-4 pt-3 pb-2 flex-shrink-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-muted text-uppercase">Held Orders</span>
                    <span class="badge bg-warning text-dark fw-bold" id="heldCountBadge">0</span>
                </div>
                <div class="held-carts-container" id="heldCartsList" style="max-height: 100px;">
                    <div class="text-center text-muted py-2 small">No held orders</div>
                </div>
            </div>

            <div class="px-4 py-2 bg-light d-flex justify-content-between align-items-center border-top border-bottom flex-shrink-0">
                <span class="fw-bold small text-dark">
                    <i class="mdi mdi-cart-outline me-1"></i>Cart
                </span>
                <button class="btn btn-link text-danger text-decoration-none small fw-bold p-0" onclick="clearCart()">
                    <i class="mdi mdi-delete-outline me-1"></i>Clear
                </button>
            </div>

            <div class="flex-grow-1 overflow-y-auto px-4 py-3" id="cartItems"></div>

            <div class="p-4 bg-white border-top flex-shrink-0">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="totals-label">Subtotal</span>
                        <span class="totals-value" id="subTotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="totals-label">Tax (8%)</span>
                        <span class="totals-value" id="gstAmount">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <span class="totals-label">Discount</span>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="discountInput" class="discount-input" value="0" min="0" step="0.01">
                            <span class="text-danger fw-bold small" id="discountAmount">-$0.00</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end pt-3 border-top border-dashed">
                        <span class="h6 fw-bold mb-0 text-muted">Total</span>
                        <span class="grand-total-value mb-0" id="grandTotal">$0.00</span>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="payment-method-btn active text-center" onclick="selectPayment('cash', this)">
                            <i class="mdi mdi-cash" style="font-size: 18px;"></i> Cash
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn text-center" onclick="selectPayment('card', this)">
                            <i class="mdi mdi-credit-card" style="font-size: 18px;"></i> Card
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn text-center" onclick="selectPayment('check', this)">
                            <i class="mdi mdi-checkbook" style="font-size: 18px;"></i> Check
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-auto">
                        <button class="btn btn-hold" id="holdCartBtn" onclick="holdCart()" disabled>
                            <i class="mdi mdi-pause-circle-outline" style="font-size: 18px;"></i>
                        </button>
                    </div>
                    <div class="col flex-grow-1">
                        <button class="btn-pay" onclick="processCheckout()" id="payBtn" {{ !Auth::user()->hasPermission('create_order') ? 'disabled' : '' }}>
                            <i class="mdi mdi-check-decagram" style="font-size: 18px;"></i>
                            <span>PAY NOW</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-bottom d-lg-none" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold">Current Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 d-flex flex-column">
            <div class="offcanvas-content">
                <div id="mobileCartItems"></div>
            </div>
            <div class="offcanvas-footer">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="totals-label">Subtotal</span>
                        <span class="totals-value" id="mobileSubtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="totals-label">Tax (8%)</span>
                        <span class="totals-value" id="mobileTax">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <span class="totals-label">Discount</span>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="discountInputMobile" class="discount-input" value="0" min="0" step="0.01">
                            <span class="text-danger fw-bold small" id="mobileDiscount">-$0.00</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end pt-3 border-top border-dashed">
                        <span class="h6 fw-bold mb-0">Total</span>
                        <span class="grand-total-value mb-0" id="mobileGrandTotal">$0.00</span>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="payment-method-btn active text-center" onclick="selectPayment('cash', this)">
                            <i class="mdi mdi-cash" style="font-size: 16px;"></i> Cash
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn text-center" onclick="selectPayment('card', this)">
                            <i class="mdi mdi-credit-card" style="font-size: 16px;"></i> Card
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn text-center" onclick="selectPayment('check', this)">
                            <i class="mdi mdi-checkbook" style="font-size: 16px;"></i> Check
                        </div>
                    </div>
                </div>

                <button class="btn-pay mb-2" onclick="processCheckout()" id="mobilePayBtn">
                    <i class="mdi mdi-check-decagram"></i> <span>PAY NOW</span>
                </button>
                <button class="btn btn-hold w-100 fw-bold mb-2" id="mobileHoldBtn" onclick="holdCart()">
                    <i class="mdi mdi-pause-circle-outline me-2"></i>HOLD
                </button>

                <div class="pt-3 border-top mt-3">
                    <label class="small fw-bold text-uppercase text-muted mb-2">Customer</label>
                    <div class="d-flex gap-2 mb-3">
                        <div class="input-group flex-nowrap position-relative flex-grow-1">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3 ps-3"><i class="mdi mdi-account-search"></i></span>
                            <input type="text" id="customerSearchMobile" class="form-control border-start-0 form-control-custom" placeholder="Name..." autocomplete="off">
                            <input type="hidden" id="selectedCustomerIdMobile" value="">
                            <div id="customerDropdownMobile" class="position-absolute w-100 bg-white border rounded-3 shadow-sm mt-1 hidden overflow-y-auto" style="top: 100%; max-height: 120px; z-index: 100;"></div>
                        </div>
                        <button class="btn btn-primary rounded-3 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <i class="mdi mdi-plus fw-bold"></i>
                        </button>
                    </div>

                    <label class="small fw-bold text-uppercase text-muted mb-2">Held Orders (<span id="mobileHeldCountBadge">0</span>)</label>
                    <div id="mobileHeldCarts" style="max-height: 80px; overflow-y: auto; margin-bottom: 10px;"></div>

                    <button class="btn btn-outline-danger w-100 fw-bold" onclick="clearCart()">
                        <i class="mdi mdi-delete-outline me-1"></i> Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary"><i class="mdi mdi-account-plus me-2"></i>Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createCustomerForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-custom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control form-control-custom" required placeholder="(555) 555-5555">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control form-control-custom">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Address</label>
                                <input type="text" name="address" class="form-control form-control-custom">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Due Amount</label>
                                <input type="number" name="due_amount" class="form-control form-control-custom" value="0" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Customer Image</label>
                                <input type="file" name="image" class="form-control form-control-custom" accept="image/*">
                                <img id="imagePreview" src="" alt="Preview" style="max-width: 100px; max-height: 100px; margin-top: 10px; display: none; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold">Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoiceModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4" id="invoiceContent">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo" class="invoice-logo mb-3" onerror="this.style.display='none'">
                    <div class="mb-3 d-print-none" style="color: #10b981;"><i class="mdi mdi-check-circle" style="font-size: 4rem;"></i></div>
                    <h3 class="fw-bold text-dark d-print-none mb-4">Payment Successful!</h3>

                    <div class="invoice-box text-start">
                        <div class="text-center mb-3">
                            <h6 class="fw-bold text-dark mb-1">{{ Auth::user()->store->store_name ?? 'US Retail Store' }}</h6>
                            <small class="text-muted">{{ Auth::user()->store->address ?? '123 Main St, New York, NY' }}</small>
                        </div>
                        <hr>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Invoice No:</span>
                            <span class="fw-bold" id="modalInvoiceNo">#0000</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted">Payment:</span>
                            <span class="badge bg-primary" id="modalPaymentMode">CASH</span>
                        </div>

                        <table class="table table-sm table-borderless mb-2 small">
                            <thead class="border-bottom border-dark">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody id="modalInvoiceItems"></tbody>
                        </table>

                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold" id="modalSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Tax:</span>
                            <span class="fw-bold" id="modalTax">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted">Discount:</span>
                            <span class="fw-bold text-danger" id="modalDiscount">-$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between border-top border-dark pt-3">
                            <span class="fw-bold">TOTAL:</span>
                            <span class="fw-bold fs-5" id="modalAmount">$0.00</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4 d-print-none">
                        <button class="btn btn-primary fw-bold" onclick="window.print()">
                            <i class="mdi mdi-printer me-2"></i>Print Invoice
                        </button>
                        <button class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">
                            <i class="mdi mdi-plus me-2"></i>New Sale
                        </button>
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
        if (isset($currentCart) && $currentCart->items) {
            foreach($currentCart->items as $item) {
                $cartArray[] = [
                    'item_id' => $item->id,
                    'id' => $item->product_id,
                    'product_id' => $item->product_id, // Added for reliable mapping
                    'name' => $item->product->product_name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'max' => $item->product->storeStocks ?
                    $item->product->storeStocks()->where('store_id', Auth::user()->store_id)->sum('quantity') : 0,
                ];
            }
        }
        @endphp
        let cart = @json($cartArray);

        let currentCategory = 'all';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let heldCarts = JSON.parse(localStorage.getItem('heldCarts')) || [];

        const TAX_RATE = 0.08;

        // HELPER TO FIX JSON OBJECT vs ARRAY ISSUE
        function preserveMaxStock(newCart) {
            if (!newCart) return [];
            let parsedCart = Array.isArray(newCart) ? newCart : Object.values(newCart);
            
            let maxMap = {};
            cart.forEach(i => { 
                let pId = i.product_id || i.id;
                if(i.max !== undefined) maxMap[pId] = i.max; 
            });
            
            parsedCart.forEach(i => {
                let pId = i.product_id || i.id;
                if (maxMap[pId] !== undefined && i.max === undefined) {
                    i.max = maxMap[pId];
                }
            });
            return parsedCart;
        }

        $(document).ready(function() {
            renderCart();
            loadProducts();
            renderHeldCarts();
            $('#productSearch').focus();

            $('#productSearch').on('keyup', function() {
                loadProducts($(this).val());
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

            // DESKTOP CUSTOMER SEARCH
            $('#customerSearch').on('keyup', function() {
                let term = $(this).val();
                if (term.length < 2) {
                    $('#customerDropdown').addClass('hidden');
                    return;
                }
                $.ajax({
                    url: "{{ route('store.sales.customers.search') }}",
                    data: { term: term },
                    success: function(customers) {
                        let html = '';
                        if (customers.length === 0) {
                            html = '<div class="p-2 text-muted small">No results</div>';
                        } else {
                            customers.forEach(c => {
                                html += `<div class="p-2 border-bottom cursor-pointer" onclick="selectCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}', '${c.phone}')">
                                    <div class="fw-bold small">${c.name}</div>
                                    <small class="text-muted">${c.phone}</small>
                                </div>`;
                            });
                        }
                        $('#customerDropdown').html(html).removeClass('hidden');
                    }
                });
            });

            // MOBILE CUSTOMER SEARCH
            $('#customerSearchMobile').on('keyup', function() {
                let term = $(this).val();
                if (term.length < 2) {
                    $('#customerDropdownMobile').addClass('hidden');
                    return;
                }
                $.ajax({
                    url: "{{ route('store.sales.customers.search') }}",
                    data: { term: term },
                    success: function(customers) {
                        let html = '';
                        if (customers.length === 0) {
                            html = '<div class="p-2 text-muted small">No results</div>';
                        } else {
                            customers.forEach(c => {
                                html += `<div class="p-2 border-bottom cursor-pointer" onclick="selectCustomerMobile(${c.id}, '${c.name.replace(/'/g, "\\'")}', '${c.phone}')">
                                    <div class="fw-bold small">${c.name}</div>
                                    <small class="text-muted">${c.phone}</small>
                                </div>`;
                            });
                        }
                        $('#customerDropdownMobile').html(html).removeClass('hidden');
                    }
                });
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#customerSearch, #customerDropdown').length) {
                    $('#customerDropdown').addClass('hidden');
                }
                if (!$(e.target).closest('#customerSearchMobile, #customerDropdownMobile').length) {
                    $('#customerDropdownMobile').addClass('hidden');
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
                            Swal.fire({ icon: 'success', title: 'Added!', timer: 1500, showConfirmButton: false });
                            $('#createCustomerForm')[0].reset();
                            $('#imagePreview').hide();
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Failed to save', 'error');
                    }
                });
            });

            $('#discountInput, #discountInputMobile').on('input', function() {
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
            $('#customerDropdown').addClass('hidden');
        }

        function selectCustomerMobile(id, name, phone) {
            $('#selectedCustomerIdMobile').val(id);
            $('#customerSearchMobile').val(name + ' (' + phone + ')');
            $('#customerDropdownMobile').addClass('hidden');
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
                data: { term: term, category: currentCategory },
                success: function(products) {
                    let html = '';
                    if (products.length === 0) {
                        html = '<div class="col-12 text-center text-muted mt-5 pt-5"><i class="mdi mdi-package-variant fs-1 opacity-25"></i><p class="mt-3">No products found.</p></div>';
                    } else {
                        products.forEach(p => {
                            let badgeClass = p.quantity <= 5 ? 'badge-low-stock' : 'badge-in-stock';
                            let badgeText = p.quantity == 0 ? 'Out of Stock' : (p.quantity <= 5 ? 'Low: ' + p.quantity : p.quantity + ' In Stock');
                            let img = p.image ? `/storage/${p.image}` : `https://placehold.co/200x200/ecfdf5/10b981?text=${p.product_name.charAt(0)}`;
                            let safeName = p.product_name.replace(/'/g, "\\'");
                            
                            html += `<div class="col-6 col-sm-4 col-lg-3">
                                <div class="product-card" onclick="addToCart(${p.product_id}, '${safeName}', ${p.price}, ${p.quantity})">
                                    <div class="product-img" style="background-image: url('${img}');"></div>
                                    <div class="product-content">
                                        <div class="product-name" title="${p.product_name}">${p.product_name}</div>
                                        <span class="badge-stock ${badgeClass}">${badgeText}</span>
                                        <div class="product-footer">
                                            <small class="text-muted">${p.sku}</small>
                                            <span class="product-price">$${parseFloat(p.price).toFixed(2)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        });
                    }
                    $('#productGrid').html(html);
                }
            });
        }

        function addToCart(id, name, price, maxStock) {
            if (maxStock <= 0) return Swal.fire('Out of Stock', 'Unavailable.', 'error');

            // Find item safely
            let existingItem = cart.find(i => i.product_id == id || i.id == id);
            
            // Check limits immediately
            if (existingItem && (existingItem.quantity + 1) > maxStock) {
                return Swal.fire({
                    toast: true, position: 'top-end', icon: 'error', 
                    title: 'Stock limit exceeded', showConfirmButton: false, timer: 1500
                });
            }

            // OPTIMISTIC UPDATE: Fixes the fast-click race condition 
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ 
                    item_id: 'temp_' + id, 
                    id: id, 
                    product_id: id, 
                    name: name, 
                    price: price, 
                    quantity: 1, 
                    max: maxStock 
                });
            }
            renderCart();

            // Background Request
            $.ajax({
                url: "{{ route('store.sales.cart.add') }}",
                method: "POST",
                data: { _token: csrfToken, product_id: id, quantity: 1 },
                success: function(res) {
                    let maxMap = {};
                    cart.forEach(i => { 
                        let pId = i.product_id || i.id;
                        if(i.max !== undefined) maxMap[pId] = i.max; 
                    });
                    maxMap[id] = maxStock;

                    cart = preserveMaxStock(res.cart);
                    cart.forEach(i => { 
                        let pId = i.product_id || i.id;
                        if(maxMap[pId] !== undefined) i.max = maxMap[pId]; 
                    });
                    
                    renderCart();
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 800 });
                    Toast.fire({ icon: 'success', title: 'Added' });
                }
            });
        }

        function renderCart() {
            let subtotal = 0;
            let totalItems = 0;

            if (cart.length === 0) {
                $('#cartItems').html('<div class="text-center py-8 text-muted small"><i class="mdi mdi-cart-outline fs-1 opacity-25"></i><p class="mt-3">Cart is empty</p></div>');
                $('#mobileCartItems').html('<div class="text-center py-8 text-muted small"><i class="mdi mdi-cart-outline fs-1 opacity-25"></i><p class="mt-3">Cart is empty</p></div>');
                updateTotals(0);
                $('#holdCartBtn, #payBtn, #mobileHoldBtn, #mobilePayBtn').prop('disabled', true);
                return;
            }
            $('#holdCartBtn, #payBtn, #mobileHoldBtn, #mobilePayBtn').prop('disabled', false);

            let html = '';
            cart.forEach((item, index) => {
                subtotal += item.price * item.quantity;
                totalItems += item.quantity;
                
                html += `<div class="cart-item">
                    <div class="cart-item-info flex-grow-1">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">$${item.price.toFixed(2)} x ${item.quantity}</div>
                    </div>
                    <div class="qty-control">
                        <button class="qty-btn" onclick="updateQty(${index}, -1)">−</button>
                        <span class="fw-bold text-dark" style="min-width: 20px; text-align: center;">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                    <div class="text-end ms-2" style="min-width: 60px;">
                        <div class="fw-bold small">$${(item.price * item.quantity).toFixed(2)}</div>
                        <i class="mdi mdi-delete-outline text-danger cursor-pointer" style="font-size: 14px;" onclick="removeFromCart(${index})"></i>
                    </div>
                </div>`;
            });

            $('#cartItems').html(html);
            $('#mobileCartItems').html(html);
            updateTotals(subtotal);
            $('#mobileItemCount').text(totalItems);
            let total = subtotal + (subtotal * TAX_RATE) - Math.max(0, parseFloat($('#discountInput').val() || $('#discountInputMobile').val() || 0));
            $('#mobileTotal').text('$' + Math.max(0, total).toFixed(2));
        }

        function updateTotals(subtotal) {
            let discount = Math.max(0, parseFloat($('#discountInput').val() || $('#discountInputMobile').val() || 0));
            let taxableAmount = Math.max(0, subtotal - discount);
            let tax = taxableAmount * TAX_RATE;
            let grandTotal = taxableAmount + tax;

            // Desktop
            $('#subTotal').text('$' + subtotal.toFixed(2));
            $('#gstAmount').text('$' + tax.toFixed(2));
            $('#discountAmount').text('-$' + discount.toFixed(2));
            $('#grandTotal').text('$' + grandTotal.toFixed(2));

            // Mobile
            $('#mobileSubtotal').text('$' + subtotal.toFixed(2));
            $('#mobileTax').text('$' + tax.toFixed(2));
            $('#mobileDiscount').text('-$' + discount.toFixed(2));
            $('#mobileGrandTotal').text('$' + grandTotal.toFixed(2));
        }

        function updateQty(index, change) {
            let item = cart[index];
            let newQty = item.quantity + change;
            
            if (newQty < 1) {
                removeFromCart(index);
                return;
            }

            // Limit check
            if (change > 0 && item.max !== undefined && newQty > item.max) {
                return Swal.fire({
                    toast: true, position: 'top-end', icon: 'error', 
                    title: 'Stock limit exceeded', showConfirmButton: false, timer: 1500
                });
            }

            // OPTIMISTIC UPDATE
            item.quantity = newQty;
            renderCart();

            // Ignore temporary items that haven't registered to DB yet
            if(String(item.item_id).startsWith('temp_')) return;

            $.ajax({
                url: "{{ route('store.sales.cart.update') }}",
                method: "POST",
                data: { _token: csrfToken, item_id: item.item_id, quantity: newQty },
                success: function(res) {
                    cart = preserveMaxStock(res.cart);
                    renderCart();
                }
            });
        }

        function removeFromCart(index) {
            let item = cart[index];
            if(!item) return;

            let itemId = item.item_id;
            
            // OPTIMISTIC UPDATE
            cart.splice(index, 1);
            renderCart();

            if(String(itemId).startsWith('temp_')) return;

            $.ajax({
                url: "{{ route('store.sales.cart.remove') }}",
                method: "POST",
                data: { _token: csrfToken, item_id: itemId },
                success: function(res) {
                    cart = preserveMaxStock(res.cart);
                    renderCart();
                }
            });
        }

        function clearCart() {
            if (cart.length === 0) return;
            Swal.fire({
                title: 'Clear Cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: { _token: csrfToken },
                        success: function() {
                            cart = [];
                            renderCart();
                            $('#customerSearch, #customerSearchMobile').val('');
                            $('#selectedCustomerId, #selectedCustomerIdMobile').val('');
                            $('#discountInput, #discountInputMobile').val(0);
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
            if (cart.length === 0) return Swal.fire('Empty', 'Add items', 'info');
            Swal.fire({
                title: 'Hold Order?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    const holdObj = {
                        id: Date.now(),
                        customer: $('#customerSearch').val() || $('#customerSearchMobile').val() || 'Walk-in',
                        customerId: $('#selectedCustomerId').val() || $('#selectedCustomerIdMobile').val(),
                        items: cart,
                        discount: parseFloat($('#discountInput').val() || $('#discountInputMobile').val() || 0),
                        total: parseFloat($('#grandTotal').text().replace('$', '') || $('#mobileGrandTotal').text().replace('$', '')),
                        date: new Date().toLocaleString()
                    };
                    heldCarts.push(holdObj);
                    localStorage.setItem('heldCarts', JSON.stringify(heldCarts));
                    renderHeldCarts();

                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: { _token: csrfToken },
                        success: function() {
                            cart = [];
                            renderCart();
                            $('#customerSearch, #customerSearchMobile').val('');
                            $('#selectedCustomerId, #selectedCustomerIdMobile').val('');
                            $('#discountInput, #discountInputMobile').val(0);
                            Swal.fire({ icon: 'success', title: 'Held', timer: 1500, showConfirmButton: false });
                        }
                    });
                }
            });
        }

        function renderHeldCarts() {
            const desktopContainer = $('#heldCartsList');
            const mobileContainer = $('#mobileHeldCarts');
            
            if (heldCarts.length === 0) {
                desktopContainer.html('<div class="text-center text-muted py-2 small">No held orders</div>');
                mobileContainer.html('');
                $('#heldCountBadge, #mobileHeldCountBadge').text('0');
                return;
            }
            
            $('#heldCountBadge, #mobileHeldCountBadge').text(heldCarts.length);
            let html = '';
            
            heldCarts.forEach((hold, index) => {
                html += `<div class="bg-white border rounded p-2 mb-2" style="font-size: 12px;">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="fw-bold text-truncate">${hold.customer}</div>
                        <div class="text-primary fw-bold">$${hold.total.toFixed(2)}</div>
                    </div>
                    <div class="text-muted small mb-2">${hold.items.length} items</div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1 fw-bold" onclick="restoreHeldCart(${index})">Restore</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteHeldCart(${index})"><i class="mdi mdi-close"></i></button>
                    </div>
                </div>`;
            });
            
            desktopContainer.html(html);
            mobileContainer.html(html);
        }

        function restoreHeldCart(index) {
            Swal.fire({
                title: 'Restore?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    const hold = heldCarts[index];

                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: { _token: csrfToken },
                        success: function() {
                            cart = hold.items;
                            $('#discountInput, #discountInputMobile').val(hold.discount || 0);
                            if (hold.customerId) {
                                $('#selectedCustomerId, #selectedCustomerIdMobile').val(hold.customerId);
                                $('#customerSearch, #customerSearchMobile').val(hold.customer);
                            }

                            heldCarts.splice(index, 1);
                            localStorage.setItem('heldCarts', JSON.stringify(heldCarts));
                            renderHeldCarts();
                            renderCart();
                            
                            // Re-add to backend session
                            hold.items.forEach(item => {
                                $.post("{{ route('store.sales.cart.add') }}", {
                                    _token: csrfToken,
                                    product_id: item.product_id || item.id,
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
            if (cart.length === 0) return Swal.fire('Empty', 'Add items', 'error');
            let btn = $('#payBtn, #mobilePayBtn');
            btn.prop('disabled', true);

            let sub = parseFloat($('#subTotal').text().replace('$', ''));
            let tax = parseFloat($('#gstAmount').text().replace('$', ''));
            let total = parseFloat($('#grandTotal').text().replace('$', ''));
            let discount = parseFloat($('#discountInput').val() || $('#discountInputMobile').val() || 0);
            let custId = $('#selectedCustomerId').val() || $('#selectedCustomerIdMobile').val();

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
                    gst_amount: tax,
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
                        modalItemsHtml += `<tr><td>${item.name}</td><td class="text-end">${item.quantity}</td><td class="text-end fw-bold">$${(item.price * item.quantity).toFixed(2)}</td></tr>`;
                    });
                    $('#modalInvoiceItems').html(modalItemsHtml);

                    let offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'));
                    if (offcanvas) offcanvas.hide();

                    let invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
                    invoiceModal.show();

                    $.ajax({
                        url: "{{ route('store.sales.cart.clear') }}",
                        method: "POST",
                        data: { _token: csrfToken },
                        success: function() {
                            cart = [];
                            renderCart();
                            $('#customerSearch, #customerSearchMobile').val('');
                            $('#selectedCustomerId, #selectedCustomerIdMobile').val('');
                            $('#discountInput, #discountInputMobile').val(0);
                            loadProducts();
                        }
                    });
                },
                error: function(err) {
                    Swal.fire('Failed', err.responseJSON?.message || 'Error', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        }
    </script>
    @endpush
</x-app-layout>