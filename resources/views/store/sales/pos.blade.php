<x-app-layout title="Retail POS Pro (USA)">
    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">

        <style>
            :root {
                --pos-primary: #3b82f6;
                --pos-primary-dark: #1d4ed8;
                --pos-bg: #f1f5f9;
                --pos-card-bg: #ffffff;
                --pos-text: #0f172a;
                --pos-muted: #64748b;
                --pos-border: #e2e8f0;
                --pos-accent: #6366f1;
                --pos-success: #10b981;
                --pos-warning: #f59e0b;
                --pos-danger: #ef4444;
                --pos-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }

            *,
            *::before,
            *::after {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Manrope', sans-serif;
                background-color: var(--pos-bg);
                color: var(--pos-text);
            }

            .hidden {
                display: none !important;
            }

            /* Scrollbar styling */
            ::-webkit-scrollbar {
                width: 5px;
                height: 5px;
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

            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }

            /* ─── LAYOUT ─────────────────────────────────────────── */
            .pos-wrapper {
                height: calc(100vh - 70px);
                overflow: hidden;
                display: flex;
            }

            .left-panel {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                border-right: 1.5px solid var(--pos-border);
                background: var(--pos-bg);
            }

            .right-panel {
                width: 400px;
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                background: #fff;
                box-shadow: -8px 0 30px rgba(0, 0, 0, 0.06);
                z-index: 10;
            }

            /* Mobile: hide right panel, show offcanvas */
            @media (max-width: 991px) {
                .right-panel {
                    display: none;
                }

                .floating-cart-btn {
                    position: fixed;
                    bottom: 16px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: calc(100% - 32px);
                    max-width: 480px;
                    z-index: 1040; /* Below offcanvas backdrop */
                    animation: slideUp 0.3s ease-out;
                    padding-bottom: constant(safe-area-inset-bottom);
                    padding-bottom: env(safe-area-inset-bottom);
                }
            }

            @media (min-width: 992px) {
                .floating-cart-btn {
                    display: none !important;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(40px);
                }

                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }

            /* ─── POS HEADER ──────────────────────────────────────── */
            .pos-header {
                background: #fff;
                border-bottom: 1.5px solid var(--pos-border);
                padding: 12px 16px;
                flex-shrink: 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            }

            @media (min-width: 768px) {
                .pos-header {
                    padding: 14px 24px;
                }
            }

            /* ─── SEARCH & FILTERS ────────────────────────────────── */
            .search-wrap {
                padding: 14px 16px 8px;
                flex-shrink: 0;
            }

            @media (min-width: 768px) {
                .search-wrap {
                    padding: 16px 24px 10px;
                }
            }

            .search-input {
                border: 1.5px solid var(--pos-border);
                background: #fff;
                color: var(--pos-text);
                font-weight: 600;
                border-radius: 10px;
                padding: 10px 12px 10px 40px;
                font-size: 14px;
                transition: border-color .2s, box-shadow .2s;
                width: 100%;
            }

            .search-input:focus {
                border-color: var(--pos-primary);
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
                outline: none;
            }

            .cat-scroll {
                padding: 0 16px 10px;
                flex-shrink: 0;
            }

            @media (min-width: 768px) {
                .cat-scroll {
                    padding: 0 24px 12px;
                }
            }

            .cat-btn {
                border-radius: 8px;
                font-weight: 700;
                font-size: 12px;
                padding: 7px 14px;
                border: 1.5px solid var(--pos-border);
                background: #fff;
                color: var(--pos-muted);
                transition: all .2s;
                white-space: nowrap;
                cursor: pointer;
            }

            .cat-btn.active {
                background: var(--pos-primary);
                color: #fff;
                border-color: var(--pos-primary);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
            }

            .cat-btn:hover:not(.active) {
                border-color: #94a3b8;
                background: #f8fafc;
            }

            /* ─── PRODUCT GRID ────────────────────────────────────── */
            .products-scroll {
                flex: 1;
                overflow-y: auto;
                padding: 4px 16px 80px;
            }

            @media (min-width: 768px) {
                .products-scroll {
                    padding: 4px 24px 24px;
                }
            }

            .product-card {
                border: 1.5px solid var(--pos-border);
                border-radius: 12px;
                background: #fff;
                cursor: pointer;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                height: 100%;
                transition: border-color .2s, transform .2s, box-shadow .2s;
            }

            .product-card:hover {
                border-color: var(--pos-primary);
                transform: translateY(-4px);
                box-shadow: 0 12px 24px rgba(59, 130, 246, 0.16);
            }

            .product-img {
                height: 120px;
                background-size: cover;
                background-position: center;
                border-radius: 8px;
                margin: 10px 10px 6px;
                background-color: #f1f5f9;
                transition: transform .3s;
            }

            .product-card:hover .product-img {
                transform: scale(1.04);
            }

            .product-content {
                padding: 0 10px 10px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .product-name {
                font-weight: 700;
                font-size: 12.5px;
                color: var(--pos-text);
                margin-bottom: 5px;
                line-height: 1.3;
            }

            .product-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: auto;
                padding-top: 7px;
                border-top: 1px solid var(--pos-border);
            }

            .product-price {
                font-size: 15px;
                font-weight: 800;
                color: var(--pos-primary);
            }

            .badge-stock {
                font-size: 9.5px;
                font-weight: 800;
                padding: 3px 6px;
                border-radius: 4px;
                text-transform: uppercase;
            }

            .badge-in-stock {
                background: #dcfce7;
                color: #166534;
            }

            .badge-low-stock {
                background: #fee2e2;
                color: #991b1b;
            }

            /* ─── RIGHT PANEL SECTIONS ────────────────────────────── */
            .rp-customer {
                padding: 14px 16px;
                border-bottom: 1.5px solid var(--pos-border);
                flex-shrink: 0;
                background: #fff;
            }

            .rp-held {
                padding: 10px 16px;
                flex-shrink: 0;
                border-bottom: 1px solid var(--pos-border);
                background: #fafafa;
            }

            .rp-cart-header {
                padding: 10px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8fafc;
                border-bottom: 1.5px solid var(--pos-border);
                flex-shrink: 0;
            }

            /* ─── CART AREA (the key fix: flex-grow so it fills space) */
            .rp-cart-body {
                flex: 1;
                overflow-y: auto;
                padding: 12px 14px;
                min-height: 0;
                /* CRITICAL for flex scroll */
            }

            .rp-footer {
                padding: 14px 16px;
                background: #fff;
                border-top: 1.5px solid var(--pos-border);
                flex-shrink: 0;
            }

            /* ─── CART ITEMS ──────────────────────────────────────── */
            .cart-item {
                background: #fff;
                border: 1.5px solid var(--pos-border);
                border-radius: 10px;
                padding: 11px 13px;
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 10px;
                transition: border-color .2s, box-shadow .2s;
            }

            .cart-item:last-child {
                margin-bottom: 0;
            }

            .cart-item:hover {
                border-color: var(--pos-primary);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
            }

            .cart-item-name {
                font-weight: 700;
                font-size: 13px;
                color: var(--pos-text);
                line-height: 1.2;
                margin-bottom: 3px;
            }

            .cart-item-price {
                font-weight: 600;
                color: var(--pos-primary);
                font-size: 12.5px;
            }

            .qty-control {
                display: flex;
                align-items: center;
                background: #f1f5f9;
                border-radius: 7px;
                gap: 4px;
                padding: 2px;
                flex-shrink: 0;
            }

            .qty-btn {
                width: 26px;
                height: 26px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 5px;
                background: transparent;
                color: var(--pos-text);
                border: none;
                cursor: pointer;
                font-weight: 800;
                font-size: 15px;
                transition: background .15s;
            }

            .qty-btn:hover {
                background: #fff;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            }

            .cart-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100%;
                min-height: 140px;
                color: var(--pos-muted);
            }

            /* ─── TOTALS ──────────────────────────────────────────── */
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
                font-size: 26px;
                font-weight: 900;
                color: var(--pos-primary);
            }

            .discount-input {
                width: 72px;
                padding: 5px 8px;
                border: 1.5px solid var(--pos-border);
                border-radius: 6px;
                font-size: 12px;
                text-align: right;
                font-weight: 700;
                transition: border-color .2s;
            }

            .discount-input:focus {
                border-color: var(--pos-primary);
                outline: none;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            /* ─── PAYMENT METHOD BUTTONS ──────────────────────────── */
            .payment-method-btn {
                border: 1.5px solid var(--pos-border);
                border-radius: 8px;
                padding: 8px 4px;
                background: #fff;
                cursor: pointer;
                transition: all .2s;
                font-weight: 700;
                font-size: 11px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 3px;
                color: var(--pos-muted);
            }

            .payment-method-btn.active {
                border-color: var(--pos-primary);
                background: #eff6ff;
                color: var(--pos-primary);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            }

            .payment-method-btn:hover:not(.active) {
                background: #f8fafc;
                border-color: #94a3b8;
            }

            /* ─── ACTION BUTTONS ──────────────────────────────────── */
            .btn-pay {
                background: linear-gradient(135deg, var(--pos-primary), var(--pos-primary-dark));
                color: #fff;
                font-weight: 800;
                font-size: 14px;
                border-radius: 10px;
                border: none;
                padding: 13px;
                width: 100%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: 0 6px 20px rgba(59, 130, 246, 0.28);
                transition: transform .2s, box-shadow .2s;
                animation: pulse-glow 2.5s ease-in-out infinite;
            }

            @keyframes pulse-glow {

                0%,
                100% {
                    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.28);
                }

                50% {
                    box-shadow: 0 8px 30px rgba(59, 130, 246, 0.42);
                }
            }

            .btn-pay:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
                animation: none;
            }

            .btn-pay:active:not(:disabled) {
                transform: translateY(0);
            }

            .btn-pay:disabled {
                opacity: 0.55;
                cursor: not-allowed;
                animation: none;
            }

            .btn-hold {
                background: #fff;
                border: 2px solid var(--pos-warning);
                color: var(--pos-warning);
                font-weight: 700;
                border-radius: 10px;
                transition: all .2s;
                cursor: pointer;
                padding: 11px 14px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-hold:hover:not(:disabled) {
                background: var(--pos-warning);
                color: #fff;
                transform: translateY(-2px);
            }

            .btn-hold:disabled {
                opacity: 0.4;
                cursor: not-allowed;
            }

            /* ─── FORM CONTROLS ───────────────────────────────────── */
            .form-control-custom {
                border: 1.5px solid var(--pos-border);
                border-radius: 8px;
                padding: 9px 12px;
                font-size: 13px;
                transition: border-color .2s, box-shadow .2s;
                width: 100%;
            }

            .form-control-custom:focus {
                border-color: var(--pos-primary);
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                outline: none;
            }

            /* ─── CUSTOMER DROPDOWN ───────────────────────────────── */
            .customer-dropdown-item {
                padding: 10px 14px;
                cursor: pointer;
                border-bottom: 1px solid var(--pos-border);
                transition: background .15s;
            }

            .customer-dropdown-item:last-child {
                border-bottom: none;
            }

            .customer-dropdown-item:hover {
                background: #f0f9ff;
            }

            /* ─── MODALS ──────────────────────────────────────────── */
            .modal-content {
                border: none;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            }

            .modal-header {
                border-bottom: 1.5px solid var(--pos-border);
                padding: 18px 20px;
            }

            .modal-body {
                padding: 20px;
            }

            .modal-footer {
                border-top: 1.5px solid var(--pos-border);
                padding: 14px 20px;
            }

            /* ─── INVOICE ─────────────────────────────────────────── */
            .invoice-box {
                background: #f8fafc;
                border: 1.5px dashed var(--pos-border);
                border-radius: 10px;
                padding: 14px;
            }

            /* ─── OFFCANVAS (MOBILE CART) ─────────────────────────── */
            .offcanvas-top {
                border-radius: 0 !important;
                height: 100% !important;
                box-shadow: none !important;
            }

            .offcanvas-header {
                border-bottom: 1.5px solid var(--pos-border);
                padding: 14px 16px;
            }

            .offcanvas-body {
                padding: 0;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .offcanvas-content {
                flex: 1;
                overflow-y: auto;
                padding: 16px;
                min-height: 200px;
                background: #fff;
            }

            .offcanvas-footer {
                background: #f8fafc;
                padding: 16px;
                border-top: 1.5px solid var(--pos-border);
                flex-shrink: 0;
            }

            /* ─── LOADING OVERLAY ─────────────────────────────────── */
            #paymentLoadingOverlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.82);
                backdrop-filter: blur(4px);
                z-index: 9999;
                display: none;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: #fff;
            }

            .loader-ripple {
                display: inline-block;
                position: relative;
                width: 80px;
                height: 80px;
            }

            .loader-ripple div {
                position: absolute;
                border: 4px solid var(--pos-primary);
                opacity: 1;
                border-radius: 50%;
                animation: loader-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
            }

            .loader-ripple div:nth-child(2) {
                animation-delay: -0.5s;
            }

            @keyframes loader-ripple {
                0% {
                    top: 36px;
                    left: 36px;
                    width: 0;
                    height: 0;
                    opacity: 0;
                }

                4.9% {
                    top: 36px;
                    left: 36px;
                    width: 0;
                    height: 0;
                    opacity: 0;
                }

                5% {
                    top: 36px;
                    left: 36px;
                    width: 0;
                    height: 0;
                    opacity: 1;
                }

                100% {
                    top: 0;
                    left: 0;
                    width: 72px;
                    height: 72px;
                    opacity: 0;
                }
            }

            /* ─── HELD CARTS ──────────────────────────────────────── */
            .held-cart-card {
                background: #fff;
                border: 1.5px solid var(--pos-border);
                border-radius: 8px;
                padding: 8px 10px;
                margin-bottom: 6px;
                font-size: 12px;
            }

            .held-cart-card:last-child {
                margin-bottom: 0;
            }

            /* ─── PRINT STYLES ────────────────────────────────────── */
            @media print {
                @page {
                    size: 80mm auto;
                    margin: 0;
                }

                /* Hide EVERYTHING EXCEPT the modal we want to print */
                body > :not(#invoiceModal) {
                    display: none !important;
                }

                #invoiceModal {
                    display: block !important;
                    position: absolute !important;
                    left: 0 !important;
                    top: 0 !important;
                    width: 76mm !important; /* Slightly less than 80 to avoid clipping */
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #fff !important;
                }

                #invoiceModal .modal-dialog {
                    margin: 0 !important;
                    max-width: 100% !important;
                    width: 100% !important;
                }

                #invoiceModal .modal-content {
                    box-shadow: none !important;
                    border: none !important;
                    border-radius: 0 !important;
                }

                #invoiceContent {
                    display: block !important;
                    padding: 0 !important;
                }

                #invoiceContent * {
                    visibility: visible !important;
                    color: #000 !important;
                }

                /* Hide UI elements inside the modal */
                .modal-header .btn-close,
                .modal-footer .btn-secondary,
                .d-print-none {
                    display: none !important;
                }

                /* Ensure table fits */
                table {
                    width: 100% !important;
                }
            }

                /* Ensure invoice box prints cleanly */
                .invoice-box {
                    background: #fff !important;
                    border: 1px dashed #999 !important;
                    padding: 8px !important;
                }
            }
        </style>
    @endpush

    @php
        $brandLogo = asset('assets/images/logo.jpg');
        $brandName = 'Home Food Distributors';
        $settings = \App\Models\StoreSetting::first();
        if ($settings) {
            if (!empty($settings->logo)) {
                $brandLogo = asset('storage/' . $settings->logo);
            }
            if (!empty($settings->app_name)) {
                $brandName = $settings->app_name;
            }
        }
    @endphp

    {{-- Loading overlay --}}
    <div id="paymentLoadingOverlay">
        <div class="loader-ripple">
            <div></div>
            <div></div>
        </div>
        <h4 class="mt-4 fw-bold">Processing Payment...</h4>
        <p class="text-white-50 mt-1">Please do not refresh or close this page.</p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MAIN POS WRAPPER
    ════════════════════════════════════════════════════════════ --}}
    <div class="pos-wrapper">

        {{-- ── LEFT PANEL: Products ── --}}
        <div class="left-panel">

            {{-- Header --}}
            <div class="pos-header">
                <div class="row align-items-center g-2">
                    <div class="col-auto">
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="rounded border bg-white p-1"
                            style="width:42px;height:42px;object-fit:contain;">
                    </div>
                    <div class="col min-w-0">
                        <small class="text-primary fw-bold d-block lh-1">{{ $brandName }}</small>
                        <span class="fw-bold text-dark d-block" style="font-size:14px;line-height:1.3">
                            {{ Auth::user()->store->store_name ?? 'GreenPOS (USA)' }}
                        </span>
                        <small class="text-muted d-block">{{ Auth::user()->store->address ?? 'Store Panel' }}</small>
                    </div>
                    <div class="col-auto d-none d-sm-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border fw-bold" style="font-size:11px;" id="hardwareStatusBadge">
                            <i class="mdi mdi-robot-confused-outline me-1"></i>Agent: Checking...
                        </span>
                        <span class="badge bg-light text-dark border fw-bold" style="font-size:11px;">
                            <i class="mdi mdi-calendar me-1"></i>{{ now()->format('M d, Y') }}
                        </span>
                        <a href="{{ route('dashboard') }}"
                            class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                            <i class="mdi mdi-exit-to-app me-1"></i>Exit
                        </a>
                    </div>
                    <div class="col-auto d-sm-none">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-danger btn-sm rounded-pill px-2">
                            <i class="mdi mdi-exit-to-app"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <div class="search-wrap">
                <div class="position-relative">
                    <i class="mdi mdi-magnify position-absolute"
                        style="left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:18px;"></i>
                    <input type="text" id="productSearch" class="search-input" placeholder="Search products (F1)..."
                        autofocus autocomplete="off">
                </div>
            </div>

            {{-- Category filters --}}
            <div class="cat-scroll">
                <div class="d-flex gap-2 overflow-auto hide-scrollbar pb-1">
                    <button class="cat-btn active" onclick="filterCategory('all', this)">All</button>
                    @foreach ($categories as $cat)
                        <button class="cat-btn"
                            onclick="filterCategory('{{ $cat->id }}', this)">{{ $cat->name }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Products grid --}}
            <div class="products-scroll" id="productsScrollArea">
                <div class="row g-2 g-md-3 pt-1" id="productGrid">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-3 fw-bold">Loading Products...</p>
                    </div>
                </div>
            </div>

            {{-- Floating cart button (mobile) --}}
            <div class="floating-cart-btn">
                <button
                    class="btn btn-primary w-100 fw-bold py-3 rounded-3 d-flex align-items-center justify-content-between shadow-lg"
                    data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <span class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-cart fs-5"></i>
                        <span id="mobileItemCount">0</span> items
                    </span>
                    <span id="mobileTotal" class="fw-bold">$0.00</span>
                </button>
            </div>
        </div>

        {{-- ── RIGHT PANEL: Cart ── --}}
        <div class="right-panel">

            {{-- Customer search --}}
            <div class="rp-customer">
                <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Customer</label>
                <div class="d-flex gap-2">
                    <div class="position-relative flex-grow-1">
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3">
                                <i class="mdi mdi-account-search"></i>
                            </span>
                            <input type="text" id="customerSearch"
                                class="form-control border-start-0 form-control-custom" placeholder="Search customer..."
                                autocomplete="off">
                            <input type="hidden" id="selectedCustomerId" value="">
                        </div>
                        <div id="customerDropdown"
                            class="position-absolute w-100 bg-white border rounded-3 shadow-lg hidden overflow-auto"
                            style="top:calc(100% + 4px);max-height:240px;z-index:9999;"></div>
                    </div>
                    <button class="btn btn-primary rounded-3 px-3 flex-shrink-0" data-bs-toggle="modal"
                        data-bs-target="#addCustomerModal" title="Add new customer">
                        <i class="mdi mdi-account-plus"></i>
                    </button>
                </div>
            </div>

            {{-- Held orders --}}
            <div class="rp-held">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-muted text-uppercase">Held Orders</span>
                    <span class="badge bg-warning text-dark fw-bold" id="heldCountBadge">0</span>
                </div>
                <div id="heldCartsList" style="max-height:90px;overflow-y:auto;">
                    <div class="text-center text-muted small py-1">No held orders</div>
                </div>
            </div>

            {{-- Cart header --}}
            <div class="rp-cart-header">
                <span class="fw-bold small text-dark">
                    <i class="mdi mdi-cart-outline me-1 text-primary"></i>Order Items
                </span>
                <button class="btn btn-link text-danger text-decoration-none small fw-bold p-0" onclick="clearCart()">
                    <i class="mdi mdi-delete-outline me-1"></i>Clear
                </button>
            </div>

            {{-- Cart items (scrollable, fills available space) --}}
            <div class="rp-cart-body" id="cartItems"></div>

            {{-- Footer: totals + payment + actions --}}
            <div class="rp-footer">
                {{-- Totals --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="totals-label">Subtotal</span>
                        <span class="totals-value" id="subTotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="totals-label">Tax (8%)</span>
                        <span class="totals-value" id="gstAmount">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <span class="totals-label">Discount</span>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="discountInput" class="discount-input" value="0"
                                min="0" step="0.01">
                            <span class="text-danger fw-bold small" id="discountAmount">-$0.00</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                        <span class="fw-bold text-muted small">TOTAL</span>
                        <span class="grand-total-value" id="grandTotal">$0.00</span>
                    </div>
                </div>

                {{-- Payment methods --}}
                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <div class="payment-method-btn active" onclick="selectPayment('cash', this)">
                            <i class="mdi mdi-cash" style="font-size:18px;"></i>Cash
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn" onclick="selectPayment('card', this)">
                            <i class="mdi mdi-credit-card" style="font-size:18px;"></i>Card
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn" onclick="selectPayment('check', this)">
                            <i class="mdi mdi-checkbook" style="font-size:18px;"></i>Check
                        </div>
                    </div>
                </div>

                {{-- Card auth panel --}}
                <div id="cardAuthPanelDesktop" class="d-none mb-2">
                    <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-3 py-2">
                        <small class="fw-bold text-muted" id="cardAuthStatusDesktop">Card not authorized</small>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold"
                            onclick="openCardAuthModal()">Authorize</button>
                    </div>
                </div>

                {{-- Hold + Pay --}}
                <div class="row g-2">
                    <div class="col-auto">
                        <button class="btn-hold" id="holdCartBtn" onclick="holdCart()" disabled title="Hold order">
                            <i class="mdi mdi-pause-circle-outline" style="font-size:18px;"></i>
                        </button>
                    </div>
                    <div class="col">
                        <button class="btn-pay" onclick="processCheckout()" id="payBtn"
                            {{ !Auth::user()->hasPermission('create_order') ? 'disabled' : '' }}>
                            <i class="mdi mdi-check-decagram" style="font-size:18px;"></i>
                            <span>PAY NOW</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MOBILE OFFCANVAS CART
    ════════════════════════════════════════════════════════════ --}}
    <div class="offcanvas offcanvas-top d-lg-none" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold">
                <i class="mdi mdi-cart text-primary me-2"></i>Current Order
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="offcanvas-content">
                <div id="mobileCartItems"></div>
            </div>
            <div class="offcanvas-footer">
                {{-- Totals --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="totals-label">Subtotal</span>
                        <span class="totals-value" id="mobileSubtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="totals-label">Tax (8%)</span>
                        <span class="totals-value" id="mobileTax">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <span class="totals-label">Discount</span>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="discountInputMobile" class="discount-input" value="0"
                                min="0" step="0.01">
                            <span class="text-danger fw-bold small" id="mobileDiscount">-$0.00</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                        <span class="fw-bold small text-muted">TOTAL</span>
                        <span class="grand-total-value" id="mobileGrandTotal">$0.00</span>
                    </div>
                </div>

                {{-- Payment methods --}}
                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <div class="payment-method-btn active" onclick="selectPayment('cash', this)">
                            <i class="mdi mdi-cash" style="font-size:16px;"></i>Cash
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn" onclick="selectPayment('card', this)">
                            <i class="mdi mdi-credit-card" style="font-size:16px;"></i>Card
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="payment-method-btn" onclick="selectPayment('check', this)">
                            <i class="mdi mdi-checkbook" style="font-size:16px;"></i>Check
                        </div>
                    </div>
                </div>

                <div id="cardAuthPanelMobile" class="d-none mb-2">
                    <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-2 py-2">
                        <small class="fw-bold text-muted" id="cardAuthStatusMobile">Card not authorized</small>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold"
                            onclick="openCardAuthModal()">Authorize</button>
                    </div>
                </div>

                <button class="btn-pay mb-2" onclick="processCheckout()" id="mobilePayBtn">
                    <i class="mdi mdi-check-decagram"></i><span>PAY NOW</span>
                </button>
                <button class="btn btn-hold w-100 mb-3" id="mobileHoldBtn" onclick="holdCart()">
                    <i class="mdi mdi-pause-circle-outline me-2"></i>HOLD ORDER
                </button>

                {{-- Customer (mobile) --}}
                <div class="border-top pt-3 mb-3">
                    <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Customer</label>
                    <div class="d-flex gap-2">
                        <div class="position-relative flex-grow-1">
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text bg-white border-end-0 rounded-start-3">
                                    <i class="mdi mdi-account-search"></i>
                                </span>
                                <input type="text" id="customerSearchMobile"
                                    class="form-control border-start-0 form-control-custom"
                                    placeholder="Search customer..." autocomplete="off">
                                <input type="hidden" id="selectedCustomerIdMobile" value="">
                            </div>
                            <div id="customerDropdownMobile"
                                class="position-absolute w-100 bg-white border rounded-3 shadow hidden overflow-auto"
                                style="top:calc(100% + 4px);max-height:120px;z-index:9999;"></div>
                        </div>
                        <button class="btn btn-primary rounded-3 flex-shrink-0" data-bs-toggle="modal"
                            data-bs-target="#addCustomerModal">
                            <i class="mdi mdi-account-plus"></i>
                        </button>
                    </div>
                </div>

                {{-- Held orders (mobile) --}}
                <div class="mb-3">
                    <label class="small fw-bold text-uppercase text-muted mb-2 d-block">
                        Held Orders (<span id="mobileHeldCountBadge">0</span>)
                    </label>
                    <div id="mobileHeldCarts" style="max-height:80px;overflow-y:auto;"></div>
                </div>

                <button class="btn btn-outline-danger w-100 fw-bold" onclick="clearCart()">
                    <i class="mdi mdi-delete-outline me-1"></i>Clear Cart
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MODALS
    ════════════════════════════════════════════════════════════ --}}

    {{-- Add Customer --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">
                        <i class="mdi mdi-account-plus me-2"></i>Add Customer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createCustomerForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-custom"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Phone <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control form-control-custom"
                                    required placeholder="(555) 555-5555">
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
                                <input type="number" name="due_amount" class="form-control form-control-custom"
                                    value="0" min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Customer Image</label>
                                <input type="file" name="image" class="form-control form-control-custom"
                                    accept="image/*">
                                <img id="imagePreview" src="" alt="Preview"
                                    style="max-width:80px;max-height:80px;margin-top:8px;display:none;border-radius:8px;object-fit:cover;">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light fw-bold"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary fw-bold">Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoice / Receipt --}}
    <div class="modal fade" id="invoiceModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
            <div class="modal-content">
                <div class="modal-body p-4" id="invoiceContent">
                    {{-- Brand header (prints) --}}
                    <div class="text-center mb-3">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width:28px;height:28px;">
                                <i class="mdi mdi-home-variant text-white" style="font-size:14px;"></i>
                            </div>
                            <span class="fw-bold text-dark text-uppercase"
                                style="font-size:17px;letter-spacing:.04em;">Home Food</span>
                        </div>
                        <span class="d-block text-muted fw-bold text-uppercase"
                            style="font-size:10px;letter-spacing:.08em;">Distributors</span>
                    </div>

                    {{-- Success icon (screen only) --}}
                    <div class="text-center mb-3 d-print-none" style="color:#10b981;">
                        <i class="mdi mdi-check-circle" style="font-size:3.5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark text-center d-print-none mb-3">Payment Successful!</h4>

                    {{-- Receipt box --}}
                    <div class="invoice-box text-start">
                        <div class="text-center mb-2">
                            <h6 class="fw-bold text-dark mb-0">SWF –
                                {{ Auth::user()->store->store_name ?? 'Location' }}</h6>
                            <small class="text-muted">{{ Auth::user()->store->address ?? 'USA Retail Store' }}</small>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Invoice No:</span>
                            <span class="fw-bold" id="modalInvoiceNo">#0000</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Payment:</span>
                            <span class="badge bg-primary" id="modalPaymentMode">CASH</span>
                        </div>

                        <table class="table table-sm table-borderless mb-1 small">
                            <thead class="border-bottom border-dark">
                                <tr>
                                    <th class="px-0">Item</th>
                                    <th class="text-end px-0">Qty</th>
                                    <th class="text-end px-0">Total</th>
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
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Discount:</span>
                            <span class="fw-bold text-danger" id="modalDiscount">-$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between border-top border-dark pt-2">
                            <span class="fw-bold">TOTAL:</span>
                            <span class="fw-bold" style="font-size:1.1rem;" id="modalAmount">$0.00</span>
                        </div>

                        <div class="text-center mt-3 small text-muted">
                            Thank you for your purchase!
                        </div>
                    </div>

                    {{-- Action buttons (screen only) --}}
                    <div class="d-grid gap-2 mt-3 d-print-none">
                        <button class="btn btn-primary fw-bold" onclick="window.print()">
                            <i class="mdi mdi-printer me-2"></i>Print Receipt
                        </button>
                        <button class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">
                            <i class="mdi mdi-plus me-2"></i>New Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Authorization --}}
    <div class="modal fade" id="cardAuthModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">Card Authorization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Authorization Result</label>
                        <select class="form-select form-control-custom" id="cardAuthResult">
                            <option value="approved">Approved</option>
                            <option value="declined">Declined</option>
                            <option value="partial">Partial Approval</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bold small">Approved Amount</label>
                        <input type="number" min="0" step="0.01" class="form-control form-control-custom"
                            id="cardApprovedAmount" placeholder="0.00">
                    </div>
                    <small class="text-muted">Partial approval is rejected. Approved amount must match order
                        total.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary fw-bold" onclick="submitCardAuthorization()">Save
                        Authorization</button>
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
                    foreach ($currentCart->items as $item) {
                        $cartArray[] = [
                            'item_id' => $item->id,
                            'id' => $item->product_id,
                            'product_id' => $item->product_id,
                            'name' => $item->product->product_name,
                            'price' => (float) $item->price,
                            'quantity' => $item->quantity,
                            'unit_type' => $item->product->unit_type,
                            'max' => $item->product->storeStocks
                                ? $item->product
                                    ->storeStocks()
                                    ->where('store_id', Auth::user()->store_id)
                                    ->sum('quantity')
                                : 0,
                        ];
                    }
                }
            @endphp
            let cart = @json($cartArray);

            let currentCategory = 'all';
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            let heldCarts = JSON.parse(localStorage.getItem('heldCarts')) || [];
            const TAX_RATE = 0.08;
            let cardAuthState = {
                attempted: false,
                status: null,
                approvedAmount: 0
            };

            let hardwareAgent = {
                online: false,
                approved: false,
                terminal_id: '{{ Auth::user()->store->pos_terminal_id ?? "" }}'
            };

            const hardwareStatusMap = {
                online: { text: "Agent: Online", class: "bg-success text-white border-0", icon: "mdi-robot-outline" },
                offline: { text: "Agent: Offline", class: "bg-danger text-white border-0", icon: "mdi-robot-dead-outline" },
                unauthorized: { text: "Agent: Unapproved", class: "bg-warning text-dark border-0", icon: "mdi-robot-confused-outline" }
            };

            function updateHardwareUI(status) {
                const badge = $('#hardwareStatusBadge');
                const config = hardwareStatusMap[status] || hardwareStatusMap.offline;
                badge.attr('class', `badge fw-bold ${config.class}`).css('font-size', '11px');
                badge.html(`<i class="mdi ${config.icon} me-1"></i>${config.text}`);
            }

            function checkTerminalStatus() {
                if (!hardwareAgent.terminal_id) {
                    updateHardwareUI('offline');
                    return;
                }
                $.get("{{ route('store.sales.terminal-status') }}")
                    .done(function(data) {
                        if (data && data.status === 'Approved') {
                            hardwareAgent.online = true;
                            hardwareAgent.approved = true;
                            updateHardwareUI('online');
                        } else if (data && (data.status === 'PendingApproval' || data.status === 'Registered')) {
                            hardwareAgent.online = true;
                            hardwareAgent.approved = false;
                            updateHardwareUI('unauthorized');
                        } else {
                            hardwareAgent.online = false;
                            updateHardwareUI('offline');
                        }
                    })
                    .fail(function() {
                        hardwareAgent.online = false;
                        updateHardwareUI('offline');
                    });
            }

            let lastBarcode = null;
            function pollScanner() {
                if (!hardwareAgent.online || !hardwareAgent.approved) return;
                
                $.get("{{ route('store.sales.scanner-scan') }}")
                    .done(function(data) {
                        if (data && data.success && data.scan && data.scan.barcode) {
                            const newBarcode = data.scan.barcode;
                            if (newBarcode !== lastBarcode) {
                                lastBarcode = newBarcode;
                                processScannedBarcode(newBarcode);
                            }
                        }
                    });
            }

            function processScannedBarcode(barcode) {
                // Find product by barcode
                $.get("{{ route('sales.search') }}", { search: barcode })
                    .done(function(data) {
                        if (data && data.length > 0) {
                            const product = data[0];
                            addToCart(product.id);
                            toastr.success(`Scanned: ${product.product_name}`);
                        } else {
                            toastr.warning(`Barcode ${barcode} not found.`);
                        }
                    });
            }

            // Start scanner polling every 2 seconds
            setInterval(pollScanner, 2000);

            // Check hardware status every 30 seconds
            checkTerminalStatus();
            setInterval(checkTerminalStatus, 30000);

            /* ─── HELPERS ──────────────────────────────────────── */
            function preserveMaxStock(newCart) {
                if (!newCart) return [];
                let parsed = Array.isArray(newCart) ? newCart : Object.values(newCart);
                let maxMap = {};
                cart.forEach(i => {
                    let p = i.product_id || i.id;
                    if (i.max !== undefined) maxMap[p] = i.max;
                });
                parsed.forEach(i => {
                    let p = i.product_id || i.id;
                    if (maxMap[p] !== undefined && i.max === undefined) i.max = maxMap[p];
                });
                return parsed;
            }

            function roundToNine(price) {
                let val = parseFloat(price);
                return Math.floor(val) + 0.9;
            }

            /* ─── INIT ─────────────────────────────────────────── */
            $(document).ready(function() {
                renderCart();
                loadProducts();
                renderHeldCarts();
                updateCardAuthUI();
                $('#productSearch').focus();

                $('#productSearch').on('keyup', function() {
                    loadProducts($(this).val());
                });

                // Image preview
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

                /* ── DESKTOP customer search ── */
                $('#customerSearch').on('keyup', function() {
                    let term = $(this).val();
                    if (term.length < 2) {
                        $('#customerDropdown').addClass('hidden');
                        return;
                    }
                    $.ajax({
                        url: "{{ route('store.sales.customers.search') }}",
                        data: {
                            term
                        },
                        success: function(customers) {
                            let html = customers.length === 0 ?
                                '<div class="p-3 text-muted small text-center">No results</div>' :
                                customers.map(c =>
                                    `<div class="customer-dropdown-item" onclick="selectCustomer(${c.id},'${c.name.replace(/'/g,"\\'")}','${c.phone}')">
                                    <div class="fw-bold text-dark small">${c.name}</div>
                                    <small class="text-muted">${c.phone}</small>
                                </div>`).join('');
                            $('#customerDropdown').html(html).removeClass('hidden');
                        }
                    });
                });

                /* ── MOBILE customer search ── */
                $('#customerSearchMobile').on('keyup', function() {
                    let term = $(this).val();
                    if (term.length < 2) {
                        $('#customerDropdownMobile').addClass('hidden');
                        return;
                    }
                    $.ajax({
                        url: "{{ route('store.sales.customers.search') }}",
                        data: {
                            term
                        },
                        success: function(customers) {
                            let html = customers.length === 0 ?
                                '<div class="p-2 text-muted small text-center">No results</div>' :
                                customers.map(c =>
                                    `<div class="customer-dropdown-item" onclick="selectCustomerMobile(${c.id},'${c.name.replace(/'/g,"\\'")}','${c.phone}')">
                                    <div class="fw-bold small">${c.name}</div>
                                    <small class="text-muted">${c.phone}</small>
                                </div>`).join('');
                            $('#customerDropdownMobile').html(html).removeClass('hidden');
                        }
                    });
                });

                /* ── Close dropdowns on outside click ── */
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#customerSearch, #customerDropdown').length) {
                        $('#customerDropdown').addClass('hidden');
                    }
                    if (!$(e.target).closest('#customerSearchMobile, #customerDropdownMobile').length) {
                        $('#customerDropdownMobile').addClass('hidden');
                    }
                });

                /* ── Add customer form ── */
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
                                selectCustomer(res.customer.id, res.customer.name, res.customer
                                    .phone);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Customer Added!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('#createCustomerForm')[0].reset();
                                $('#imagePreview').hide();
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to save customer.', 'error');
                        }
                    });
                });

                /* ── Discount sync ── */
                $('#discountInput').on('input', function() {
                    $('#discountInputMobile').val($(this).val());
                    renderCart();
                });
                $('#discountInputMobile').on('input', function() {
                    $('#discountInput').val($(this).val());
                    renderCart();
                });

                /* ── Keyboard shortcuts ── */
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

            /* ─── CUSTOMER SELECT ──────────────────────────────── */
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

            /* ─── PRODUCTS ─────────────────────────────────────── */
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
                        term,
                        category: currentCategory
                    },
                    success: function(products) {
                        let html = '';
                        if (products.length === 0) {
                            html =
                                '<div class="col-12 text-center text-muted mt-5 pt-5"><i class="mdi mdi-package-variant fs-1 opacity-25 d-block mb-3"></i>No products found.</div>';
                        } else {
                            products.forEach(p => {
                                let pName = p.product_name || 'Unknown Product';
                                let badgeClass = p.quantity <= 5 ? 'badge-low-stock' : 'badge-in-stock';
                                let badgeText = p.quantity == 0 ? 'Out of Stock' : (p.quantity <= 5 ?
                                    'Low: ' + p.quantity : p.quantity + ' In Stock');
                                let img = p.icon ? `/storage/${p.icon}` :
                                    `https://placehold.co/200x200/ecfdf5/10b981?text=${encodeURIComponent(pName.charAt(0))}`;
                                let safeName = pName.replace(/'/g, "\\'");
                                let displayPrice = roundToNine(p.selling_price || p.price || 0);

                                html += `<div class="col-6 col-sm-4 col-lg-3">
                                <div class="product-card" onclick="addToCart(${p.product_id},'${safeName}',${displayPrice},${p.quantity})">
                                    <div class="product-img" style="background-image:url('${img}');"></div>
                                    <div class="product-content">
                                        <div class="text-muted mb-1" style="font-size:0.65rem;font-family:monospace;">
                                            <i class="mdi mdi-barcode"></i> ${p.barcode || 'N/A'}
                                        </div>
                                        <div class="product-name" title="${pName}">${pName}</div>
                                        <span class="badge-stock ${badgeClass}">${badgeText}</span>
                                        <div class="product-footer">
                                            <small class="text-muted" style="font-size:10px;">UPC: ${p.upc || 'N/A'}</small>
                                            <span class="product-price">$${displayPrice.toFixed(2)}</span>
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

            /* ─── CART OPERATIONS ──────────────────────────────── */
            function addToCart(id, name, price, maxStock) {
                if (maxStock <= 0) return Swal.fire('Out of Stock', 'This item is unavailable.', 'error');

                let existing = cart.find(i => (i.product_id == id || i.id == id));

                if (existing && (existing.quantity + 1) > maxStock) {
                    return Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .fire({
                            icon: 'error',
                            title: 'Stock limit exceeded'
                        });
                }

                // Optimistic update
                if (existing) {
                    existing.quantity += 1;
                } else {
                    cart.push({
                        item_id: 'temp_' + id,
                        id,
                        product_id: id,
                        name,
                        price,
                        quantity: 1,
                        max: maxStock
                    });
                }
                renderCart();

                $.ajax({
                    url: "{{ route('store.sales.cart.add') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        product_id: id,
                        quantity: 1
                    },
                    success: function(res) {
                        let maxMap = {};
                        cart.forEach(i => {
                            let p = i.product_id || i.id;
                            if (i.max !== undefined) maxMap[p] = i.max;
                        });
                        maxMap[id] = maxStock;
                        cart = preserveMaxStock(res.cart);
                        cart.forEach(i => {
                            let p = i.product_id || i.id;
                            if (maxMap[p] !== undefined) i.max = maxMap[p];
                            i.price = roundToNine(i.price);
                        });
                        renderCart();
                        Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 700
                        }).fire({
                            icon: 'success',
                            title: 'Added to cart'
                        });
                    }
                });
            }

            function renderCart() {
                let subtotal = 0,
                    totalItems = 0;

                if (cart.length === 0) {
                    let empty = `<div class="cart-empty">
                    <i class="mdi mdi-cart-outline" style="font-size:2.5rem;opacity:.25;margin-bottom:8px;"></i>
                    <span class="small fw-bold">Cart is empty</span>
                    <span class="small text-muted mt-1">Click a product to add it</span>
                </div>`;
                    $('#cartItems').html(empty);
                    $('#mobileCartItems').html(empty);
                    updateTotals(0);
                    $('#holdCartBtn,#payBtn,#mobileHoldBtn,#mobilePayBtn').prop('disabled', true);
                    $('#mobileItemCount').text('0');
                    $('#mobileTotal').text('$0.00');
                    return;
                }

                $('#holdCartBtn,#payBtn,#mobileHoldBtn,#mobilePayBtn').prop('disabled', false);

                let html = '';
                cart.forEach((item, index) => {
                    let rp = roundToNine(item.price);
                    subtotal += rp * item.quantity;
                    totalItems += item.quantity;

                    html += `<div class="cart-item">
                    <div class="cart-item-info flex-grow-1 min-w-0">
                        <div class="cart-item-name text-truncate">
                            ${item.name}
                            ${(item.unit_type && ['kg', 'lb', 'pound', 'oz'].includes(item.unit_type.toLowerCase())) ? 
                                `<span class="badge bg-soft-info text-info ms-1" style="font-size:10px; cursor:pointer;" onclick="getWeightForLine(${index})">
                                    <i class="mdi mdi-scale"></i> Scale
                                </span>` : ''}
                        </div>
                        <div class="cart-item-price">$${rp.toFixed(2)} × ${item.quantity} = $${(rp*item.quantity).toFixed(2)}</div>
                    </div>
                    <div class="qty-control flex-shrink-0">
                        <button class="qty-btn" onclick="updateQty(${index},-1)">−</button>
                        <span class="fw-bold text-dark" style="min-width:22px;text-align:center;font-size:13px;">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQty(${index},1)">+</button>
                    </div>
                    <button onclick="removeFromCart(${index})"
                            class="btn btn-sm btn-light ms-1 flex-shrink-0 rounded-circle p-0 d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;color:#ef4444;border:1.5px solid #fee2e2;">
                        <i class="mdi mdi-close" style="font-size:13px;"></i>
                    </button>
                </div>`;
                });

                $('#cartItems').html(html);
                $('#mobileCartItems').html(html);
                updateTotals(subtotal);
                $('#mobileItemCount').text(totalItems);
                let discount = Math.max(0, parseFloat($('#discountInput').val() || 0));
                let total = Math.max(0, (subtotal - discount) * (1 + TAX_RATE));
                $('#mobileTotal').text('$' + total.toFixed(2));
            }

            function updateTotals(subtotal) {
                let discount = Math.max(0, parseFloat($('#discountInput').val() || 0));
                let taxableAmount = Math.max(0, subtotal - discount);
                let tax = taxableAmount * TAX_RATE;
                let grandTotal = taxableAmount + tax;

                $('#subTotal').text('$' + subtotal.toFixed(2));
                $('#gstAmount').text('$' + tax.toFixed(2));
                $('#discountAmount').text('-$' + discount.toFixed(2));
                $('#grandTotal').text('$' + grandTotal.toFixed(2));

                $('#mobileSubtotal').text('$' + subtotal.toFixed(2));
                $('#mobileTax').text('$' + tax.toFixed(2));
                $('#mobileDiscount').text('-$' + discount.toFixed(2));
                $('#mobileGrandTotal').text('$' + grandTotal.toFixed(2));
            }

            function getWeightForLine(index) {
                if (!hardwareAgent.online || !hardwareAgent.approved) {
                    toastr.error("Hardware Agent not connected/approved.");
                    return;
                }
                
                toastr.info("Reading scale...");
                $.get("{{ route('store.sales.scale-weight') }}")
                    .done(function(data) {
                        if (data && data.success && data.weight !== null) {
                            const weight = parseFloat(data.weight);
                            if (weight > 0) {
                                cart[index].quantity = weight;
                                renderCart();
                                // Sync with server cart
                                syncCartItem(index);
                                toastr.success(`Weight captured: ${weight}`);
                            } else {
                                toastr.warning("No weight detected on scale.");
                            }
                        } else {
                            toastr.error("Failed to read from scale.");
                        }
                    })
                    .fail(function() {
                        toastr.error("Scale error.");
                    });
            }

            function syncCartItem(index) {
                const item = cart[index];
                $.ajax({
                    url: "{{ route('store.sales.cart.update') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        item_id: item.item_id,
                        quantity: item.quantity
                    }
                });
            }

            function updateQty(index, change) {
                let item = cart[index];
                let newQty = item.quantity + change;

                if (newQty < 1) {
                    removeFromCart(index);
                    return;
                }
                if (change > 0 && item.max !== undefined && newQty > item.max) {
                    return Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .fire({
                            icon: 'error',
                            title: 'Stock limit exceeded'
                        });
                }

                item.quantity = newQty;
                renderCart();

                if (String(item.item_id).startsWith('temp_')) return;

                $.ajax({
                    url: "{{ route('store.sales.cart.update') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        item_id: item.item_id,
                        quantity: newQty
                    },
                    success: function(res) {
                        cart = preserveMaxStock(res.cart);
                        renderCart();
                    }
                });
            }

            function removeFromCart(index) {
                let item = cart[index];
                if (!item) return;
                let itemId = item.item_id;
                cart.splice(index, 1);
                renderCart();
                if (String(itemId).startsWith('temp_')) return;
                $.ajax({
                    url: "{{ route('store.sales.cart.remove') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        item_id: itemId
                    },
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
                        confirmButtonText: 'Yes, clear'
                    })
                    .then(r => {
                        if (r.isConfirmed) {
                            $.ajax({
                                url: "{{ route('store.sales.cart.clear') }}",
                                method: 'POST',
                                data: {
                                    _token: csrfToken
                                },
                                success: function() {
                                    cart = [];
                                    renderCart();
                                    resetCustomerFields();
                                    resetCardAuthorization();
                                }
                            });
                        }
                    });
            }

            function resetCustomerFields() {
                $('#customerSearch,#customerSearchMobile').val('');
                $('#selectedCustomerId,#selectedCustomerIdMobile').val('');
                $('#discountInput,#discountInputMobile').val(0);
            }

            /* ─── PAYMENT ──────────────────────────────────────── */
            function selectPayment(method, element) {
                $('.payment-method-btn').removeClass('active');
                $(element).addClass('active');
                // Sync all matching buttons (desktop + mobile have same text)
                $('.payment-method-btn').each(function() {
                    let txt = $(this).text().toLowerCase().trim();
                    if (txt.includes(method)) $(this).addClass('active');
                });
                $('#paymentMethod').val(method);
                updateCardAuthUI();
            }

            function updateCardAuthUI() {
                const isCard = $('#paymentMethod').val() === 'card';
                $('#cardAuthPanelDesktop,#cardAuthPanelMobile').toggleClass('d-none', !isCard);
                let statusText = 'Card not authorized';
                if (cardAuthState.attempted && cardAuthState.status) {
                    if (cardAuthState.status === 'approved')
                        statusText = `Authorized: $${Number(cardAuthState.approvedAmount||0).toFixed(2)}`;
                    else if (cardAuthState.status === 'partial')
                        statusText = `Partial: $${Number(cardAuthState.approvedAmount||0).toFixed(2)} (Rejected)`;
                    else
                        statusText = 'Card declined';
                }
                $('#cardAuthStatusDesktop,#cardAuthStatusMobile').text(statusText);
            }

            function resetCardAuthorization() {
                cardAuthState = {
                    attempted: false,
                    status: null,
                    approvedAmount: 0
                };
                updateCardAuthUI();
            }

            function openCardAuthModal() {
                if ($('#paymentMethod').val() !== 'card') return;
                const currentTotal = parseFloat($('#grandTotal').text().replace('$', '')) || 0;
                $('#cardAuthResult').val('approved');
                $('#cardApprovedAmount').val(currentTotal.toFixed(2));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('cardAuthModal')).show();
            }

            function submitCardAuthorization() {
                cardAuthState = {
                    attempted: true,
                    status: $('#cardAuthResult').val(),
                    approvedAmount: parseFloat($('#cardApprovedAmount').val() || 0)
                };
                updateCardAuthUI();
                bootstrap.Modal.getInstance(document.getElementById('cardAuthModal'))?.hide();
                if (cardAuthState.status !== 'approved') {
                    showCardFailureOptions(cardAuthState.status === 'partial' ?
                        'Insufficient funds. Full amount required.' :
                        'Card declined. Choose another payment method.');
                } else {
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    }).fire({
                        icon: 'success',
                        title: 'Card authorized'
                    });
                }
            }

            function showCardFailureOptions(message) {
                Swal.fire({
                    title: 'Card Authorization Failed',
                    text: message || 'Card declined.',
                    icon: 'error',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Try Again',
                    denyButtonText: 'Switch to Cash',
                    cancelButtonText: 'Cancel Sale',
                    confirmButtonColor: '#2563eb',
                    denyButtonColor: '#10b981'
                }).then(r => {
                    if (r.isConfirmed) openCardAuthModal();
                    else if (r.isDenied) switchToCashAfterCardFailure();
                    else if (r.dismiss === Swal.DismissReason.cancel) cancelSaleAfterCardFailure();
                });
            }

            function switchToCashAfterCardFailure() {
                const cashBtn = $('.payment-method-btn').filter(function() {
                    return $(this).text().toLowerCase().includes('cash');
                }).first();
                if (cashBtn.length) {
                    selectPayment('cash', cashBtn[0]);
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    }).fire({
                        icon: 'info',
                        title: 'Switched to Cash'
                    });
                }
            }

            function cancelSaleAfterCardFailure() {
                $.ajax({
                    url: "{{ route('store.sales.cart.clear') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function() {
                        cart = [];
                        renderCart();
                        resetCustomerFields();
                        resetCardAuthorization();
                        switchToCashAfterCardFailure();
                    }
                });
            }

            /* ─── HOLD ─────────────────────────────────────────── */
            function holdCart() {
                if (cart.length === 0) return Swal.fire('Empty', 'Add items first.', 'info');
                Swal.fire({
                        title: 'Hold Order?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: 'Hold'
                    })
                    .then(r => {
                        if (r.isConfirmed) {
                            const holdObj = {
                                id: Date.now(),
                                customer: $('#customerSearch').val() || $('#customerSearchMobile').val() || 'Walk-in',
                                customerId: $('#selectedCustomerId').val() || $('#selectedCustomerIdMobile').val(),
                                items: JSON.parse(JSON.stringify(cart)),
                                discount: parseFloat($('#discountInput').val() || 0),
                                total: parseFloat($('#grandTotal').text().replace('$', '') || 0),
                                date: new Date().toLocaleString()
                            };
                            heldCarts.push(holdObj);
                            localStorage.setItem('heldCarts', JSON.stringify(heldCarts));
                            renderHeldCarts();
                            $.ajax({
                                url: "{{ route('store.sales.cart.clear') }}",
                                method: 'POST',
                                data: {
                                    _token: csrfToken
                                },
                                success: function() {
                                    cart = [];
                                    renderCart();
                                    resetCustomerFields();
                                    resetCardAuthorization();
                                    Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).fire({
                                        icon: 'success',
                                        title: 'Order Held'
                                    });
                                }
                            });
                        }
                    });
            }

            function renderHeldCarts() {
                if (heldCarts.length === 0) {
                    $('#heldCartsList').html('<div class="text-center text-muted py-1 small">No held orders</div>');
                    $('#mobileHeldCarts').html('');
                    $('#heldCountBadge,#mobileHeldCountBadge').text('0');
                    return;
                }
                $('#heldCountBadge,#mobileHeldCountBadge').text(heldCarts.length);
                let html = '';
                heldCarts.forEach((h, i) => {
                    html += `<div class="held-cart-card">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="fw-bold text-truncate">${h.customer}</span>
                        <span class="text-primary fw-bold ms-2">$${h.total.toFixed(2)}</span>
                    </div>
                    <div class="text-muted mb-2">${h.items.length} item(s) · ${h.date}</div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1 fw-bold" onclick="restoreHeldCart(${i})">
                            <i class="mdi mdi-restore me-1"></i>Restore
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteHeldCart(${i})">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                </div>`;
                });
                $('#heldCartsList,#mobileHeldCarts').html(html);
            }

            function restoreHeldCart(index) {
                Swal.fire({
                        title: 'Restore this order?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Restore'
                    })
                    .then(r => {
                        if (r.isConfirmed) {
                            const hold = heldCarts[index];
                            $.ajax({
                                url: "{{ route('store.sales.cart.clear') }}",
                                method: 'POST',
                                data: {
                                    _token: csrfToken
                                },
                                success: function() {
                                    cart = JSON.parse(JSON.stringify(hold.items));
                                    $('#discountInput,#discountInputMobile').val(hold.discount || 0);
                                    if (hold.customerId) {
                                        $('#selectedCustomerId,#selectedCustomerIdMobile').val(hold.customerId);
                                        $('#customerSearch,#customerSearchMobile').val(hold.customer);
                                    }
                                    heldCarts.splice(index, 1);
                                    localStorage.setItem('heldCarts', JSON.stringify(heldCarts));
                                    renderHeldCarts();
                                    renderCart();
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

            /* ─── CHECKOUT ─────────────────────────────────────── */
            function processCheckout() {
                if (cart.length === 0) return Swal.fire('Empty Cart', 'Add items before checking out.', 'error');
                if ($('#paymentMethod').val() === 'card') {
                    simulateCardPayment();
                } else {
                    finalizeCheckout();
                }
            }

            function simulateCardPayment() {
                Swal.fire({
                    title: 'Processing Card...',
                    text: 'Waiting for authorization',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        setTimeout(() => {
                            const ok = Math.random() > 0.3;
                            if (ok) {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Authorized!',
                                        timer: 1500,
                                        showConfirmButton: false
                                    })
                                    .then(() => finalizeCheckout());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Card Declined',
                                    text: 'Authorization failed. Choose another payment method.',
                                    showCancelButton: true,
                                    showDenyButton: true,
                                    confirmButtonText: 'Try Again',
                                    denyButtonText: 'Switch to Cash',
                                    cancelButtonText: 'Cancel Sale',
                                    confirmButtonColor: '#2563eb',
                                    denyButtonColor: '#10b981'
                                }).then(r => {
                                    if (r.isConfirmed) simulateCardPayment();
                                    else if (r.isDenied) finalizeCheckout('cash');
                                });
                            }
                        }, 2000);
                    }
                });
            }

            function finalizeCheckout(overrideMethod = null) {
                let btn = $('#payBtn,#mobilePayBtn');
                btn.prop('disabled', true);

                let sub = parseFloat($('#subTotal').text().replace('$', ''));
                let tax = parseFloat($('#gstAmount').text().replace('$', ''));
                let total = parseFloat($('#grandTotal').text().replace('$', ''));
                let discount = parseFloat($('#discountInput').val() || $('#discountInputMobile').val() || 0);
                let custId = $('#selectedCustomerId').val() || $('#selectedCustomerIdMobile').val();
                let method = overrideMethod || $('#paymentMethod').val();

                if (method === 'card') {
                    if (!cardAuthState.attempted) {
                        btn.prop('disabled', false);
                        openCardAuthModal();
                        return;
                    }
                    if (cardAuthState.status !== 'approved') {
                        btn.prop('disabled', false);
                        showCardFailureOptions(cardAuthState.status === 'partial' ?
                            'Insufficient funds.' : 'Card declined.');
                        return;
                    }
                }

                const payload = {
                    _token: csrfToken,
                    cart: JSON.stringify(cart),
                    customer_id: custId,
                    payment_method: method,
                    status: 'completed',
                    subtotal: sub,
                    tax_amount: tax,
                    gst_amount: tax,
                    discount_amount: discount,
                    total_amount: total
                };
                if (method === 'card') {
                    payload.card_auth_status = cardAuthState.status;
                    payload.card_approved_amount = cardAuthState.approvedAmount;
                }

                $('#paymentLoadingOverlay').css('display', 'flex');

                $.ajax({
                    url: "{{ route('store.sales.checkout') }}",
                    method: 'POST',
                    data: payload,
                    success: function(res) {
                        // Populate invoice modal
                        $('#modalInvoiceNo').text(res.invoice);
                        $('#modalAmount').text('$' + total.toFixed(2));
                        $('#modalSubtotal').text('$' + sub.toFixed(2));
                        $('#modalTax').text('$' + tax.toFixed(2));
                        $('#modalDiscount').text('-$' + discount.toFixed(2));
                        $('#modalPaymentMode').text(method.toUpperCase());

                        let itemsHtml = '';
                        cart.forEach(item => {
                            itemsHtml += `<tr>
                            <td class="px-0">${item.name}</td>
                            <td class="text-end px-0">${item.quantity}</td>
                            <td class="text-end fw-bold px-0">$${(roundToNine(item.price)*item.quantity).toFixed(2)}</td>
                        </tr>`;
                        });
                        $('#modalInvoiceItems').html(itemsHtml);

                        // Close offcanvas if open
                        bootstrap.Offcanvas.getInstance(document.getElementById('cartOffcanvas'))?.hide();

                        // Show invoice modal
                        new bootstrap.Modal(document.getElementById('invoiceModal')).show();

                        // Clear cart in background
                        $.ajax({
                            url: "{{ route('store.sales.cart.clear') }}",
                            method: 'POST',
                            data: {
                                _token: csrfToken
                            },
                            success: function() {
                                cart = [];
                                renderCart();
                                resetCustomerFields();
                                resetCardAuthorization();
                                // Reset to cash payment
                                const cashBtn = $('.payment-method-btn').filter(function() {
                                    return $(this).text().toLowerCase().includes('cash');
                                }).first();
                                if (cashBtn.length) selectPayment('cash', cashBtn[0]);
                                loadProducts();
                            }
                        });
                    },
                    error: function(err) {
                        const response = err.responseJSON || {};
                        if (response.error_code === 'CARD_DECLINED' || response.error_code ===
                            'PARTIAL_AUTH_DECLINED') {
                            cardAuthState.attempted = true;
                            cardAuthState.status = response.error_code === 'CARD_DECLINED' ? 'declined' : 'partial';
                            updateCardAuthUI();
                            showCardFailureOptions(response.message);
                            return;
                        }
                        Swal.fire('Error', response.message || 'Failed to process payment.', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        $('#paymentLoadingOverlay').hide();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
