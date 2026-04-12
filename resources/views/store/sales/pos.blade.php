<x-app-layout title="Retail POS Pro (USA)">
    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">

        <style>
            :root {
                --pos-primary: #019934;
                --pos-primary-dark: #004d1a;
                --pos-bg: #f8fafc;
                --pos-card-bg: #ffffff;
                --pos-text: #0f172a;
                --pos-muted: #64748b;
                --pos-border: #e2e8f0;
                --pos-accent: #019934;
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
                flex-direction: column;
            }

            .main-content {
                flex: 1;
                display: flex;
                overflow: hidden;
                width: 100%;
            }

            .left-panel {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                background: var(--pos-bg);
            }

            /* No more right-panel needed on this page */
            .right-panel {
                display: none !important;
            }

            /* Floating Cart Bar */
            .floating-cart-bar {
                background: #fff;
                border-top: 2px solid var(--pos-primary);
                padding: 12px 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.08);
                z-index: 1000;
                position: relative;
                transition: transform 0.3s ease;
            }

            .floating-cart-bar.hidden {
                transform: translateY(100%);
            }

            /* Cart Review Modal Styles */
            .review-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px;
                background: #f8fafc;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
            }

            .review-item-info {
                flex: 1;
                min-width: 0;
            }

            .review-item-name {
                font-weight: 700;
                color: #1e293b;
                font-size: 14px;
            }

            .review-item-price {
                font-size: 12px;
                color: #64748b;
            }

            .review-qty-controls {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #fff;
                padding: 4px;
                border-radius: 8px;
                border: 1px solid #cbd5e1;
            }

            .review-qty-btn {
                width: 24px;
                height: 24px;
                border-radius: 6px;
                border: none;
                background: #f1f5f9;
                color: #1e293b;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
            }

            .review-qty-btn:hover {
                background: #e2e8f0;
            }

            .review-qty-val {
                font-weight: 800;
                font-size: 13px;
                min-width: 20px;
                text-align: center;
            }

            .review-scale-btn {
                background: #f0fdf4;
                color: #15803d;
                border: 1px solid #bcf0da;
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 700;
                cursor: pointer;
            }

            /* Mobile: hide right panel, show offcanvas */
            /* Mobile specific tweaks */
            @media (max-width: 991px) {
                .pos-header {
                    padding: 10px 12px;
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
            .active-pulse {
                transform: scale(0.96) !important;
                border-color: #10b981 !important;
                box-shadow: 0 0 15px rgba(16, 185, 129, 0.4) !important;
                transition: all 0.1s ease-in-out;
            }

            .text-muted-green {
                color: #10b981;
                opacity: 0.9;
                font-size: 10px;
                font-weight: 700;
            }

            .add-to-cart-btn {
                background: var(--pos-primary);
                color: white;
                border: none;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex !important;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                transition: all 0.2s;
                cursor: pointer;
            }

            .remove-from-cart-btn {
                background: #f1f5f9;
                color: #64748b;
                border: 1px solid #e2e8f0;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex !important;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                transition: all 0.2s;
                cursor: pointer;
            }

            .remove-from-cart-btn:hover {
                background: #fee2e2;
                color: #ef4444;
                border-color: #fecaca;
                transform: scale(1.1);
            }

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
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 12px 14px;
                min-height: 150px;
                background: #fff;
                /* Ensure it's not collapsed */
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
                body> :not(#invoiceModal) {
                    display: none !important;
                }

                #invoiceModal {
                    display: block !important;
                    position: absolute !important;
                    left: 0 !important;
                    top: 0 !important;
                    width: 76mm !important;
                    /* Slightly less than 80 to avoid clipping */
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
                $brandLogo = Storage::disk('r2')->url($settings->logo);
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
        <div class="main-content">
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
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <small class="text-muted">{{ Auth::user()->store->address ?? 'Store Panel' }}</small>
                                <span
                                    class="badge bg-warning-subtle text-warning border border-warning-subtle fw-bold cursor-pointer"
                                    style="font-size:10px;" onclick="openHeldOrders()" id="headerHeldBadge">
                                    <i class="mdi mdi-pause-circle-outline"></i> <span id="heldCountText">0</span> Held
                                </span>
                            </div>
                        </div>
                        <div class="col-auto d-none d-lg-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-light border fw-bold rounded-pill px-3"
                                onclick="syncHardwareManual()" title="Sync Hardware Status">
                                <i class="mdi mdi-refresh me-1"></i>Sync
                            </button>
                            <span class="badge bg-light text-dark border fw-bold" style="font-size:11px;"
                                id="hardwareStatusBadge" title="Agent Connection">
                                <i class="mdi mdi-robot-confused-outline me-1"></i>Agent: -
                            </span>
                            <span class="badge bg-light text-dark border fw-bold" style="font-size:11px;"
                                id="scannerStatusBadge" title="Scanner Connection">
                                <i class="mdi mdi-barcode-scan me-1"></i>Scanner: -
                            </span>
                            <span class="badge bg-light text-dark border fw-bold" style="font-size:11px;"
                                id="scaleStatusBadge" title="Scale Connection">
                                <i class="mdi mdi-scale me-1"></i>Scale: -
                            </span>
                            <a href="{{ route('dashboard') }}"
                                class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                <i class="mdi mdi-exit-to-app me-1"></i>Exit
                            </a>
                        </div>
                        <div class="col-auto d-lg-none">
                            <button class="btn btn-light btn-sm rounded-pill px-2 me-2" onclick="syncHardwareManual()">
                                <i class="mdi mdi-refresh"></i>
                            </button>
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
                        <input type="text" id="productSearch" class="search-input"
                            placeholder="Search products (F1)..." autofocus autocomplete="off">
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
                        @if (isset($initialProducts) && count($initialProducts) > 0)
                            @foreach ($initialProducts as $p)
                                @php
                                    $stock = $p->storeStocks->first();
                                    $qty = $stock ? $stock->quantity : 0;
                                    $price = $stock && $stock->selling_price > 0 ? $stock->selling_price : $p->price;
                                    $displayPrice = floor($price) + 0.9;
                                    $img = $p->icon
                                        ? Storage::disk('r2')->url($p->icon)
                                        : 'https://placehold.co/200x200/ecfdf5/10b981?text=' .
                                            urlencode(substr($p->product_name, 0, 1));
                                @endphp
                                <div class="col-6 col-sm-4 col-lg-3">
                                    <div class="product-card clickable-product" data-id="{{ $p->id }}"
                                        data-name="{{ str_replace('"', '&quot;', $p->product_name) }}"
                                        data-price="{{ $displayPrice }}" data-stock="{{ $qty }}">
                                        <div class="product-img position-relative"
                                            style="background-image:url('{{ $img }}');">
                                            <span
                                                class="position-absolute top-0 end-0 m-2 badge bg-primary rounded-circle d-flex align-items-center justify-content-center cart-qty-badge d-none"
                                                style="width: 24px; height: 24px; font-size: 11px; z-index: 10; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">0</span>
                                        </div>
                                        <div class="product-content">
                                            <div class="text-muted mb-1" style="font-size:10px;font-family:monospace;">
                                                <i class="mdi mdi-barcode"></i> {{ $p->barcode ?? 'N/A' }}
                                            </div>
                                            <div class="product-name fw-bold" title="{{ $p->product_name }}">
                                                {{ $p->product_name }}</div>
                                            <div
                                                class="product-footer mt-auto pt-2 d-flex align-items-center justify-content-between">
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-bold text-dark">${{ number_format($displayPrice, 2) }}</span>
                                                    <small class="text-muted-green">{{ $qty }} in
                                                        stock</small>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <button class="remove-from-cart-btn btn-cart-control"
                                                        onclick="event.stopPropagation(); decreaseCartQty({{ $p->id }})">
                                                        <i class="mdi mdi-minus"></i>
                                                    </button>
                                                    <button class="add-to-cart-btn btn-cart-control">
                                                        <i class="mdi mdi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-3 fw-bold">Loading Products...</p>
                            </div>
                        @endif
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

            </div> {{-- End of main-content --}}
        </div> {{-- End of pos-wrapper --}}

        {{-- Floating Cart Summary Bar (Page 1) --}}
        <div id="floatingCartBar" class="floating-cart-bar">
            <div class="container-fluid d-flex align-items-center justify-content-between h-100">
                <div class="d-flex align-items-center gap-3 cursor-pointer" onclick="openCartReview()">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px;">
                        <i class="mdi mdi-cart-outline fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 16px;">
                            <span id="barItemCount">0</span> Items <small
                                class="text-muted fw-normal">(Review)</small>
                        </div>
                        <div class="text-primary fw-bold" id="barGrandTotal">$0.00</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-warning fw-bold px-3 py-3 rounded-3 d-flex align-items-center gap-2"
                        onclick="holdCart()">
                        <i class="mdi mdi-pause-circle-outline fs-5"></i>
                        <span class="d-none d-sm-inline">HOLD</span>
                    </button>
                    <a href="{{ route('store.sales.checkout-page') }}" id="proceedToCheckoutBtn"
                        class="btn btn-primary fw-bold px-4 py-3 rounded-3 shadow-sm d-flex align-items-center gap-2 disabled">
                        <span class="d-none d-sm-inline">PROCEED TO CHECKOUT</span>
                        <span class="d-inline d-sm-none">CHECKOUT</span>
                        <i class="mdi mdi-arrow-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         MODALS
    ════════════════════════════════════════════════════════════ --}}

    <!-- Held Orders Modal -->
    <div class="modal fade" id="heldOrdersModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold fs-5"><i
                            class="mdi mdi-pause-circle-outline me-2 text-warning"></i>Held Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div id="modalHeldCartsList" class="d-flex flex-column gap-2">
                        {{-- Injected here --}}
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-light fw-bold w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold fs-5 text-primary"><i class="mdi mdi-account-plus me-2"></i>Add
                        Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <form id="addCustomerForm" action="{{ route('store.sales.customers.store') }}" method="POST"
                        enctype="multipart/form-data">
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
                        <button class="btn btn-primary fw-bold" id="hwPrintBtn" onclick="printReceiptViaHardware()">
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

    {{-- Printer Selection Modal --}}
    <div class="modal fade" id="printerSelectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-printer me-2"></i>Select Printer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Available hardware printers detected by the POS Agent:</p>
                    <div id="printerListContainer" class="list-group list-group-flush border rounded overflow-hidden"
                        style="max-height: 300px; overflow-y: auto;">
                        <div class="p-4 text-center text-muted">Fetching printer list...</div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light fw-bold w-100"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary fw-bold w-100 d-none" id="confirmPrintBtn"
                        onclick="confirmHardwarePrint()">
                        Print Now
                    </button>
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

    {{-- ═══════════════════════════════════════════════════════════
         CART REVIEW MODAL
    ════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="cartReviewModal" tabindex="-1" aria-labelledby="cartReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:540px;">
            <div class="modal-content border-0 shadow-lg rounded-4">

                {{-- Header --}}
                <div class="modal-header border-0 pb-2 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold fs-5" id="cartReviewModalLabel">
                            <i class="mdi mdi-cart-check me-2 text-primary"></i>Review Cart
                        </h5>
                        <small class="text-muted">
                            <span id="reviewItemCount">0</span> item(s) ·
                            <span class="text-success fw-bold" id="reviewHardwareBadge"></span>
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Items list --}}
                <div class="modal-body px-4 py-2" style="max-height:55vh; overflow-y:auto;">
                    <div id="cartReviewBody" class="d-flex flex-column gap-2">
                        {{-- Injected by openCartReview() --}}
                    </div>
                </div>

                {{-- Totals + actions --}}
                <div class="modal-footer flex-column border-0 pt-0 pb-4 px-4 gap-2">
                    <div class="w-100 bg-light rounded-3 p-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted fw-semibold">Subtotal</span>
                            <span class="fw-bold" id="reviewSubtotal">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted fw-semibold">Tax (8%)</span>
                            <span class="fw-bold" id="reviewTax">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-1">
                            <span class="fw-bold fs-6">Total</span>
                            <span class="fw-bold fs-5 text-primary" id="reviewGrand">$0.00</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 w-100">
                        <button class="btn btn-light fw-bold flex-grow-1 rounded-3" data-bs-dismiss="modal">
                            <i class="mdi mdi-arrow-left me-1"></i>Shop More
                        </button>
                        <a href="{{ route('store.sales.checkout-page') }}"
                           class="btn btn-primary fw-bold flex-grow-1 rounded-3"
                           id="reviewCheckoutBtn">
                            Checkout <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
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
            console.log("POS SCRIPT START");
            let cart = @json($cartArray);
            let currentCategory = 'all';
            let isCartUpdating = false;
            let csrfToken = $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}";
            let heldCarts = JSON.parse(localStorage.getItem('heldCarts')) || [];
            const TAX_RATE = 0.08;

            window.updateProductCartIndicators = function() {
                $('.product-card').each(function() {
                    let card = $(this);
                    let id = card.data('id');
                    let badge = card.find('.cart-qty-badge');
                    let item = cart.find(i => (i.product_id == id || i.id == id));

                    if (item && item.quantity > 0) {
                        badge.text(item.quantity).removeClass('d-none');
                        card.addClass('border-primary shadow-sm');
                    } else {
                        badge.addClass('d-none');
                        card.removeClass('border-primary shadow-sm');
                    }
                });
            };

            window.filterCategory = function(slug, btn) {
                $('.cat-btn').removeClass('active');
                $(btn).addClass('active');
                currentCategory = slug;
                loadProducts($('#productSearch').val());
            };

            window.loadProducts = function(term = '') {
                $('#productGrid').html(
                    '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2 small">Searching...</p></div>'
                );
                $.ajax({
                    url: "/pos/search-products",
                    method: 'GET',
                    data: {
                        term,
                        category: currentCategory
                    },
                    success: function(products) {
                        let html = '';
                        if (!products || products.length === 0) {
                            html =
                                '<div class="col-12 text-center text-muted py-5 mt-5"><h5>No products found</h5></div>';
                        } else {
                            products.forEach(p => {
                                let id = p.id;
                                let name = (p.product_name || 'Item').replace(/"/g, '&quot;');
                                let price = parseFloat(p.selling_price || 0);
                                let displayPrice = Math.floor(price) + 0.9;
                                let qty = parseInt(p.quantity || 0);
                                let bc = p.barcode || 'N/A';
                                let img = p.icon ? `/storage/${p.icon}` :
                                    `https://placehold.co/200x200/ecfdf5/10b981?text=${encodeURIComponent(name.charAt(0))}`;

                                html += `<div class="col-6 col-md-3">
                                    <div class="product-card clickable-product" data-id="${id}" data-name="${name}" data-price="${displayPrice}" data-stock="${qty}">
                                        <div class="product-img position-relative" style="background-image:url('${img}');">
                                            <span class="position-absolute top-0 end-0 m-2 badge bg-primary rounded-circle d-flex align-items-center justify-content-center cart-qty-badge d-none" style="width: 24px; height: 24px; font-size: 11px; z-index: 10; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">0</span>
                                        </div>
                                        <div class="product-content">
                                            <div class="text-muted mb-1 small" style="font-size:9px;"><i class="mdi mdi-barcode"></i> ${bc}</div>
                                            <div class="product-name fw-bold small text-truncate" title="${name}">${name}</div>
                                            <div class="product-footer mt-auto pt-2 d-flex align-items-center justify-content-between">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold text-dark small">$${displayPrice.toFixed(2)}</span>
                                                    <small class="text-muted-green" style="font-size:9px;">${qty} in stock</small>
                                                </div>
                                                <div class="d-flex gap-1">
                                                    <button class="remove-from-cart-btn btn-cart-control" onclick="event.stopPropagation(); decreaseCartQty(${id})">
                                                        <i class="mdi mdi-minus"></i>
                                                    </button>
                                                    <button class="add-to-cart-btn btn-cart-control">
                                                        <i class="mdi mdi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                            });
                        }
                        $('#productGrid').html(html);
                        updateProductCartIndicators();
                    },
                    error: function() {
                        toastr.error('Failed to load products.');
                    }
                });
            };

            window.addToCart = function(id, name, price, maxStock) {
                if (isCartUpdating) return;
                if (maxStock <= 0) return Swal.fire('Out of Stock', 'Unavailable', 'error');
                let existing = cart.find(i => (i.product_id == id || i.id == id));
                if (existing && (existing.quantity + 1) > maxStock) {
                    toastr.warning('Stock limit reached');
                    return;
                }

                if (existing) {
                    existing.quantity += 1;
                } else {
                    cart.push({
                        id,
                        product_id: id,
                        name,
                        price,
                        quantity: 1,
                        max: maxStock
                    });
                }

                isCartUpdating = true;
                $('.btn-cart-control').prop('disabled', true);
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
                        if (res && res.cart) {
                            cart = preserveMaxStock(res.cart);
                            renderCart();
                        }
                        toastr.success('Item added to cart successfully');
                    },
                    error: function() {
                        toastr.error('Sync error.');
                    },
                    complete: function() {
                        isCartUpdating = false;
                        $('.btn-cart-control').prop('disabled', false);
                    }
                });
            };

            window.updateQty = function(index, change, manualQty = null) {
                if (isCartUpdating) return;
                let item = cart[index];
                if (!item) return;
                let newQty = manualQty !== null ? parseFloat(manualQty) : (item.quantity + change);
                if (isNaN(newQty) || newQty < 1) return removeFromCart(index);
                if (item.max && newQty > item.max) return toastr.warning('Stock limit reached');

                isCartUpdating = true;
                $('.btn-cart-control').prop('disabled', true);

                item.quantity = newQty;
                renderCart();

                $.ajax({
                    url: "{{ route('store.sales.cart.update') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        item_id: item.item_id || item.id,
                        quantity: newQty
                    },
                    success: function(res) {
                        if (res && res.cart) {
                            cart = preserveMaxStock(res.cart);
                            renderCart();
                        }
                        toastr.success('Cart quantity updated successfully');
                    },
                    error: function() {
                        toastr.error('Update failed.');
                    },
                    complete: function() {
                        isCartUpdating = false;
                        $('.btn-cart-control').prop('disabled', false);
                    }
                });
            };

            window.removeFromCart = function(index) {
                if (isCartUpdating) return;
                let item = cart[index];
                if (!item) return;

                isCartUpdating = true;
                $('.btn-cart-control').prop('disabled', true);

                let id = item.item_id || item.id;
                cart.splice(index, 1);
                renderCart();

                $.ajax({
                    url: "{{ route('store.sales.cart.remove') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        item_id: id
                    },
                    success: function(res) {
                        if (res && res.cart) {
                            cart = preserveMaxStock(res.cart);
                            renderCart();
                        }
                        toastr.success('Item removed from cart successfully');
                    },
                    error: function() {
                        toastr.error('Remove failed.');
                    },
                    complete: function() {
                        isCartUpdating = false;
                        $('.btn-cart-control').prop('disabled', false);
                    }
                });
            };

            window.renderCart = function() {
                let subtotal = 0,
                    totalItems = 0;
                let container = $('#cartItems'),
                    mobileContainer = $('#mobileCartItems');

                if (cart.length === 0) {
                    let empty = '<div class="p-4 text-center text-muted small">Cart is empty</div>';
                    container.html(empty);
                    mobileContainer.html(empty);
                } else {
                    let desktopHtml = '',
                        mobileHtml = '';
                    cart.forEach((item, index) => {
                        subtotal += item.price * item.quantity;
                        totalItems += item.quantity;
                        let line = `<div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded">
                            <div class="flex-grow-1 min-width-0 pe-2 text-start">
                                <div class="fw-bold small text-truncate">${item.name}</div>
                                <div class="small text-muted">${item.quantity} x $${item.price.toFixed(2)}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="fw-bold small">$${(item.price * item.quantity).toFixed(2)}</div>
                                <button class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeFromCart(${index})"><i class="mdi mdi-close"></i></button>
                            </div>
                        </div>`;
                        desktopHtml += line;
                        mobileHtml += line;
                    });
                    container.html(desktopHtml);
                    mobileContainer.html(mobileHtml);
                }
                updateTotals(subtotal, totalItems);
                updateProductCartIndicators();
            };

            window.updateTotals = function(subtotal, totalItems) {
                let discount = parseFloat($('#discountInput').val() || $('#discountInputMobile').val() || 0);
                let taxable = Math.max(0, subtotal - discount);
                let tax = taxable * TAX_RATE;
                let grand = taxable + tax;

                $('#subTotal').text('$' + subtotal.toFixed(2));
                $('#grandTotal, #mobileTotal, #barGrandTotal').text('$' + grand.toFixed(2));
                $('#mobileItemCount, #barItemCount').text(totalItems);

                // Toggle disabled state for buttons and links
                $('#payBtn, #mobilePayBtn').prop('disabled', totalItems === 0);
                $('#proceedToCheckoutBtn').toggleClass('disabled', totalItems === 0);
            };

            window.decreaseCartQty = function(productId) {
                if (isCartUpdating) return;
                let index = cart.findIndex(i => (i.product_id == productId || i.id == productId));
                if (index !== -1) {
                    updateQty(index, -1);
                } else {
                    toastr.info("Item not in cart");
                }
            };

            $(document).on('click', '.clickable-product', function(e) {
                if (isCartUpdating) return;
                const card = $(this);
                if ($(e.target).closest('.remove-from-cart-btn').length) return; // Ignore if clicked on minus button

                addToCart(card.data('id'), card.data('name'), card.data('price'), card.data('stock'));
                card.addClass('active-pulse');
                setTimeout(() => card.removeClass('active-pulse'), 150);
            });

            let cardAuthState = {
                attempted: false,
                status: null,
                approvedAmount: 0
            };
            let hardwareAgent = {
                online: false,
                approved: false,
                terminal_id: '{{ Auth::user()->store->pos_terminal_id ?? '' }}'
            };

            const hardwareStatusMap = {
                online: {
                    text: "Agent: Online",
                    class: "bg-success text-white border-0",
                    icon: "mdi-robot-outline"
                },
                offline: {
                    text: "Agent: Offline",
                    class: "bg-danger text-white border-0",
                    icon: "mdi-robot-dead-outline"
                },
                unauthorized: {
                    text: "Agent: Unapproved",
                    class: "bg-warning text-dark border-0",
                    icon: "mdi-robot-confused-outline"
                }
            };

            function updateHardwareUI(status, scannerOnline = false, scaleOnline = false) {
                const badge = $('#hardwareStatusBadge');
                const config = hardwareStatusMap[status] || hardwareStatusMap.offline;
                badge.attr('class', `badge fw-bold ${config.class}`).css('font-size', '11px');
                badge.html(`<i class="mdi ${config.icon} me-1"></i>${config.text}`);

                // Update Scanner Badge
                const scannerBadge = $('#scannerStatusBadge');
                if (scannerOnline) {
                    scannerBadge.attr('class', 'badge bg-success text-white border-0 fw-bold').css('font-size', '11px');
                    scannerBadge.html('<i class="mdi mdi-barcode-scan me-1"></i>Scanner: OK');
                } else {
                    scannerBadge.attr('class', 'badge bg-light text-muted border fw-bold').css('font-size', '11px');
                    scannerBadge.html('<i class="mdi mdi-barcode-scan me-1"></i>Scanner: Offline');
                }

                // Update Scale Badge
                const scaleBadge = $('#scaleStatusBadge');
                if (scaleOnline) {
                    scaleBadge.attr('class', 'badge bg-success text-white border-0 fw-bold').css('font-size', '11px');
                    scaleBadge.html('<i class="mdi mdi-scale me-1"></i>Scale: OK');
                } else {
                    scaleBadge.attr('class', 'badge bg-light text-muted border fw-bold').css('font-size', '11px');
                    scaleBadge.html('<i class="mdi mdi-scale me-1"></i>Scale: Offline');
                }
            }

            function checkTerminalStatus() {
                if (!hardwareAgent.terminal_id) {
                    updateHardwareUI('offline');
                    return;
                }
                $.get("{{ route('store.sales.terminal-status') }}")
                    .done(function(data) {
                        // Controller returns normalized: { status: 'Approved', online: true }
                        // Also handle pattern: { success: true, registered: true }
                        const isOnline = (data && data.online === true) ||
                            (data && data.status === 'Approved') ||
                            (data && data.success === true && data.registered === true);

                        if (isOnline) {
                            hardwareAgent.online = true;
                            hardwareAgent.approved = true;
                            updateHardwareUI('online', data.scanner, data.scale);
                            startScannerPoll(); // begin auto-polling barcode scanner
                        } else {
                            hardwareAgent.online = false;
                            hardwareAgent.approved = false;
                            updateHardwareUI('offline', false, false);
                            stopScannerPoll();  // halt polling when agent goes offline
                        }
                    })
                    .fail(function() {
                        hardwareAgent.online = false;
                        updateHardwareUI('offline', false, false);
                    });
            }

            let lastBarcode       = null;
            let scannerPollTimer  = null;

            function startScannerPoll() {
                if (scannerPollTimer) return; // already running
                // Primer: silently read the scanner's current/cached barcode once
                // to set a baseline — prevents a stale buffer from triggering addToCart.
                $.get("{{ route('store.sales.scanner-scan') }}")
                    .done(function(data) {
                        if (data && data.success && data.scan && data.scan.barcode) {
                            lastBarcode = data.scan.barcode; // baseline, NOT added to cart
                        }
                    })
                    .always(function() {
                        // Start the regular poll only after the baseline is captured
                        if (!scannerPollTimer) {
                            scannerPollTimer = setInterval(pollScanner, 2000);
                        }
                    });
            }

            function stopScannerPoll() {
                clearInterval(scannerPollTimer);
                scannerPollTimer = null;
                lastBarcode = null; // reset baseline so next connection primes fresh
            }

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
                // Find product by barcode — pass all required args to addToCart
                $.get("{{ route('store.sales.search') }}", { search: barcode })
                    .done(function(data) {
                        if (data && data.length > 0) {
                            const product     = data[0];
                            const price       = parseFloat(product.selling_price || 0);
                            const displayPrice = Math.floor(price) + 0.9;
                            const stock       = parseInt(product.quantity || 0);
                            addToCart(product.id, product.product_name, displayPrice, stock);
                            toastr.success(`✅ Scanned: ${product.product_name}`);
                        } else {
                            toastr.warning(`⚠️ Barcode "${barcode}" not found in inventory.`);
                        }
                    })
                    .fail(function() {
                        toastr.error('Failed to look up barcode.');
                    });
            }

            // Mobile/Manual Sync Trigger — only checks terminal status.
            // Scanner polling is managed automatically by startScannerPoll/stopScannerPoll.
            function syncHardwareManual() {
                toastr.info('Checking hardware connectivity...');
                checkTerminalStatus();
            }

            // Initial check on load
            checkTerminalStatus();

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
                // Skip initial loadProducts() if products are already rendered by Blade
                if ($('#productGrid .clickable-product').length === 0) {
                    loadProducts();
                }
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


            window.readScale = function(index) {
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
                                // Sync quantity with server
                                let item = cart[index];
                                $.post("{{ route('store.sales.cart.update') }}", {
                                    _token: csrfToken,
                                    item_id: item.item_id || item.id,
                                    quantity: weight
                                });
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
            };

            /* ─── CART REVIEW MODAL ────────────────────────────── */
            window.openCartReview = function() {
                if (cart.length === 0) {
                    toastr.info('Your cart is empty. Add products first.');
                    return;
                }

                // Update hardware badge inside modal
                const hwBadge = $('#reviewHardwareBadge');
                if (hardwareAgent.online && hardwareAgent.approved) {
                    hwBadge.html('<i class="mdi mdi-scale text-success me-1"></i>Scale Ready');
                } else {
                    hwBadge.html('<i class="mdi mdi-scale text-muted me-1"></i>Scale Offline');
                }

                // Build items HTML
                let html = '';
                cart.forEach(function(item, index) {
                    const lineTotal   = (item.price * item.quantity).toFixed(2);
                    const unitLabel   = item.unit_type ? item.unit_type : 'unit';
                    const isWeighable = ['kg','lb','lbs','g','gram','pound'].includes(
                                           String(unitLabel).toLowerCase());

                    html += `
                    <div class="review-item" id="review-item-${index}">
                        <div class="review-item-info">
                            <div class="review-item-name">${item.name}</div>
                            <div class="review-item-price">$${item.price.toFixed(2)} / ${unitLabel}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                            <div class="review-qty-controls">
                                <button class="review-qty-btn" onclick="reviewQtyChange(${index}, -1)">
                                    <i class="mdi mdi-minus" style="font-size:12px;"></i>
                                </button>
                                <span class="review-qty-val" id="review-qty-${index}">${item.quantity}</span>
                                <button class="review-qty-btn" onclick="reviewQtyChange(${index}, 1)">
                                    <i class="mdi mdi-plus" style="font-size:12px;"></i>
                                </button>
                            </div>
                            <button class="review-scale-btn" onclick="readScaleReview(${index})" title="Read weight from scale">
                                <i class="mdi mdi-scale me-1"></i>Weigh
                            </button>
                            <span class="fw-bold small text-dark" id="review-line-${index}">$${lineTotal}</span>
                            <button class="btn btn-sm p-1 border-0 text-danger" onclick="reviewRemoveItem(${index})" title="Remove item">
                                <i class="mdi mdi-close-circle-outline fs-5"></i>
                            </button>
                        </div>
                    </div>`;
                });

                $('#cartReviewBody').html(html);
                updateReviewTotals();
                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById('cartReviewModal')
                ).show();
            };

            window.reviewQtyChange = function(index, change) {
                if (!cart[index]) return;
                const newQty = parseFloat(cart[index].quantity) + change;
                if (newQty < 1) {
                    reviewRemoveItem(index);
                    return;
                }
                if (cart[index].max && newQty > cart[index].max) {
                    toastr.warning('Stock limit reached');
                    return;
                }
                cart[index].quantity = newQty;
                $(`#review-qty-${index}`).text(newQty);
                $(`#review-line-${index}`).text('$' + (cart[index].price * newQty).toFixed(2));
                updateReviewTotals();
                renderCart();
                // Sync with server
                $.post("{{ route('store.sales.cart.update') }}", {
                    _token: csrfToken,
                    item_id: cart[index].item_id || cart[index].id,
                    quantity: newQty
                });
            };

            window.reviewRemoveItem = function(index) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('cartReviewModal')
                );
                if (modal) modal.hide();
                setTimeout(function() {
                    removeFromCart(index);
                    // Re-open if items remain
                    if (cart.length > 0) {
                        setTimeout(openCartReview, 350);
                    }
                }, 250);
            };

            window.readScaleReview = function(index) {
                if (!hardwareAgent.online || !hardwareAgent.approved) {
                    toastr.error('Hardware Agent not connected. Connect the POS Agent and click Sync.');
                    return;
                }
                if (!cart[index]) return;

                const btn = $(`#review-item-${index} .review-scale-btn`);
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Reading...'
                );

                $.get("{{ route('store.sales.scale-weight') }}")
                    .done(function(data) {
                        if (data && data.success && data.weight !== null) {
                            const weight = parseFloat(data.weight);
                            if (weight > 0) {
                                cart[index].quantity = weight;
                                $(`#review-qty-${index}`).text(weight);
                                $(`#review-line-${index}`).text(
                                    '$' + (cart[index].price * weight).toFixed(2)
                                );
                                updateReviewTotals();
                                renderCart();
                                // Sync to server
                                $.post("{{ route('store.sales.cart.update') }}", {
                                    _token: csrfToken,
                                    item_id: cart[index].item_id || cart[index].id,
                                    quantity: weight
                                });
                                toastr.success(`⚖ Weight captured: ${weight} lbs`);
                            } else {
                                toastr.warning('No weight detected. Place item on scale and try again.');
                            }
                        } else {
                            toastr.error('Failed to read from scale. Check scale connection.');
                        }
                    })
                    .fail(function() {
                        toastr.error('Scale error. Check POS Agent is running.');
                    })
                    .always(function() {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-scale me-1"></i>Weigh'
                        );
                    });
            };

            function updateReviewTotals() {
                let subtotal   = 0;
                let totalItems = 0;
                cart.forEach(function(i) {
                    subtotal   += i.price * i.quantity;
                    totalItems += parseFloat(i.quantity);
                });
                const discount = parseFloat($('#discountInput').val() || 0);
                const taxable  = Math.max(0, subtotal - discount);
                const tax      = taxable * TAX_RATE;
                const grand    = taxable + tax;
                $('#reviewSubtotal').text('$' + subtotal.toFixed(2));
                $('#reviewTax').text('$' + tax.toFixed(2));
                $('#reviewGrand').text('$' + grand.toFixed(2));
                $('#reviewItemCount').text(totalItems % 1 === 0 ? totalItems : totalItems.toFixed(2));
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
                        // The original code had switchToCashAfterCardFailure here, but it doesn't make sense to switch to cash if the sale is cancelled.
                        // Removing it as per "remove redundant functions" and logical flow.
                    }
                });
            }

            /* ─── HOLD ─────────────────────────────────────────── */
            function renderHeldCarts() {
                $.ajax({
                    url: "{{ route('store.sales.orders') }}",
                    data: {
                        status: 'held'
                    },
                    success: function(res) {
                        let orders = res.data || res;
                        let count = orders.length;
                        $('#heldCountText').text(count);

                        if (count > 0) {
                            $('#headerHeldBadge').show();
                        } else {
                            $('#headerHeldBadge').hide();
                        }

                        let html = '';
                        if (count === 0) {
                            html = '<div class="text-center py-4 text-muted small">No held orders found.</div>';
                        } else {
                            orders.forEach(order => {
                                html += `
                                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-2 bg-light bg-opacity-50">
                                    <div>
                                        <div class="fw-bold text-dark small">Order #${order.id}</div>
                                        <div class="text-muted" style="font-size:11px;">
                                            ${order.items_count || 0} items • $${parseFloat(order.total_amount).toFixed(2)}
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary py-1 px-3 rounded-pill fw-bold" onclick="restoreHeld(${order.id})" style="font-size:11px;">Restore</button>
                                        <button class="btn btn-sm btn-outline-danger border-0 p-1" onclick="deleteHeld(${order.id})">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </div>`;
                            });
                        }
                        $('#modalHeldCartsList').html(html);
                    }
                });
            }

            window.openHeldOrders = function() {
                renderHeldCarts();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('heldOrdersModal')).show();
            }

            // Sync cart from backend (active status)
            window.fetchCartSync = function() {
                $.ajax({
                    url: "{{ route('store.sales.pos') }}",
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(res) {
                        // Assuming the index returns JSON if AJAX
                        if (res.currentCart) {
                            cart = preserveMaxStock(res.currentCart.items.map(i => ({
                                id: i.product_id,
                                product_id: i.product_id,
                                name: i.product.product_name,
                                price: parseFloat(i.price),
                                quantity: i.quantity
                            })));
                            renderCart();
                        } else {
                            cart = [];
                            renderCart();
                        }
                    }
                });
            }

            window.restoreHeld = function(id) {
                Swal.fire({
                    title: 'Restore this order?',
                    text: "This will replace your current active cart.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#019934',
                    confirmButtonText: 'Restore'
                }).then(r => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: `/store/orders/held/${id}/restore`,
                            method: 'POST',
                            data: {
                                _token: csrfToken
                            },
                            success: function(res) {
                                if (res.success) {
                                    toastr.success(res.message);
                                    let modal = bootstrap.Modal.getInstance(document.getElementById(
                                        'heldOrdersModal'));
                                    if (modal) modal.hide();

                                    // Full page reload or manual sync? 
                                    // Manual sync is better for UX
                                    location
                                .reload(); // Simplest way to ensure all state is reset correctly
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            }
                        });
                    }
                });
            }

            window.deleteHeld = function(id) {
                Swal.fire({
                    title: 'Delete Held Order?',
                    text: "This cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/store/orders/held/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: csrfToken
                            },
                            success: function(res) {
                                if (res.success) {
                                    toastr.success(res.message);
                                    renderHeldCarts();
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            }
                        });
                    }
                });
            }

            function holdCart() {
                if (cart.length === 0) return Swal.fire('Empty', 'Add items first.', 'info');
                Swal.fire({
                    title: 'Hold Order?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    confirmButtonText: 'Hold'
                }).then(r => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: "{{ route('store.sales.cart.clear') }}",
                            method: 'POST',
                            data: {
                                _token: csrfToken,
                                hold: true // Backend should handle holding the items into a 'held' order
                            },
                            success: function() {
                                cart = [];
                                renderCart();
                                renderHeldCarts();
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
                        // Track invoice for manual hardware re-print
                        window.lastInvoiceNumber = res.invoice;

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

    @push('scripts')
        <script>
            let selectedPrinter = null;

            function printReceiptViaHardware() {
                const invoiceNo = window.lastInvoiceNumber;
                if (!invoiceNo) {
                    toastr.error('No invoice found. Please complete a sale first.');
                    return;
                }

                const btn = document.getElementById('hwPrintBtn');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking...';

                // 1. Fetch Printers First
                $.get("{{ route('store.sales.get-printers') }}")
                    .done(function(res) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;

                        if (!res.success || !res.printers || res.printers.length === 0) {
                            toastr.error('No printers found on POS Agent.');
                            return;
                        }

                        // 2. Show Modal & Populate
                        $('#printerListContainer').empty();
                        res.printers.forEach(p => {
                            const item = $(`<button class="list-group-item list-group-item-action py-3">
                            <i class="mdi mdi-printer me-2 text-primary"></i>${p}
                        </button>`);
                            item.on('click', function() {
                                $('#printerListContainer .list-group-item').removeClass(
                                    'active bg-primary text-white');
                                $(this).addClass('active bg-primary text-white');
                                selectedPrinter = p;
                                $('#confirmPrintBtn').removeClass('d-none');
                            });
                            $('#printerListContainer').append(item);
                        });

                        new bootstrap.Modal(document.getElementById('printerSelectModal')).show();
                    })
                    .fail(function() {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        toastr.error('Failed to reach POS Agent to fetch printers.');
                    });
            }

            function confirmHardwarePrint() {
                if (!selectedPrinter) return;
                const invoiceNo = window.lastInvoiceNumber;
                const btn = document.getElementById('confirmPrintBtn');
                const originalHtml = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Printing...';

                $.ajax({
                    url: "{{ route('store.sales.manual-print') }}",
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        invoice_number: invoiceNo,
                        printer_name: selectedPrinter
                    },
                    success: function(res) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        if (res.success) {
                            toastr.success('Receipt sent to ' + selectedPrinter);
                            bootstrap.Modal.getInstance(document.getElementById('printerSelectModal')).hide();
                        } else {
                            toastr.error('Printer error: ' + (res.message || 'Unknown error'));
                        }
                    },
                    error: function() {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        toastr.error('Failed to reach printer. Check agent connection.');
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
