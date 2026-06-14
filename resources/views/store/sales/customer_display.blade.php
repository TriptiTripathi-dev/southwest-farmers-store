<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display - {{ $store->store_name ?? 'Southwest Farmers' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #f8fafc;
            --panel-bg: #ffffff;
            --accent-color: #019934;
            --accent-glow: rgba(1, 153, 52, 0.15);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --success-color: #10b981;
            --border-color: #e2e8f0;
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 8px -1px rgba(15, 23, 42, 0.04);
        }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .glass-panel {
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        /* Header Area */
        header {
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .store-logo {
            font-weight: 800;
            font-size: 26px;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .store-logo i {
            font-size: 28px;
        }

        .welcome-text {
            font-size: 15px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Main Workspace Grid */
        .main-container {
            flex: 1;
            padding: 24px 40px;
            display: grid;
            grid-template-columns: 1.62fr 1fr;
            gap: 28px;
            height: calc(100vh - 120px);
            min-height: 0;
        }

        /* Left Side: Cart Panel */
        .cart-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .cart-header {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 5px;
        }

        .cart-items-container {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Custom Scrollbar */
        .cart-items-container::-webkit-scrollbar {
            width: 6px;
        }
        .cart-items-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .cart-items-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .cart-items-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .cart-item {
            display: grid;
            grid-template-columns: auto 1fr auto auto;
            align-items: center;
            gap: 16px;
            padding: 14px 20px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            animation: slideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideIn {
            from { transform: translateY(12px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .item-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(1, 153, 52, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--accent-color);
        }

        .item-details {
            min-width: 0;
        }

        .item-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-price-unit {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 1px;
        }

        .item-qty-badge {
            font-size: 14px;
            font-weight: 700;
            padding: 4px 12px;
            background: #ffffff;
            color: var(--text-primary);
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        .item-total-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            text-align: right;
            min-width: 80px;
        }

        /* Right Side: Order Summary Card */
        .summary-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 28px;
            height: 100%;
        }

        .summary-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
            color: var(--text-primary);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            font-size: 15px;
            color: var(--text-secondary);
        }

        .summary-row .val {
            font-weight: 600;
            color: var(--text-primary);
        }

        .summary-row.total-row {
            margin-top: 20px;
            border-top: 2px dashed #cbd5e1;
            padding-top: 20px;
            margin-bottom: 0;
        }

        .summary-row.total-row .label {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .summary-row.total-row .val {
            font-size: 30px;
            font-weight: 800;
            color: var(--accent-color);
        }

        /* Empty State / Promotional Screensaver */
        .empty-display-state {
            grid-column: span 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
        }

        .promo-carousel {
            max-width: 760px;
            width: 100%;
            padding: 40px;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            position: relative;
        }

        .promo-slide {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.99); }
            to { opacity: 1; transform: scale(1); }
        }

        .promo-title {
            font-size: 32px;
            font-weight: 800;
            margin-top: 20px;
            color: var(--accent-color);
        }

        .promo-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            margin-top: 8px;
        }

        /* Overlay Panels (Checkout & Success states) */
        .overlay-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(248, 250, 252, 0.98);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            animation: fadeIn 0.3s ease-out forwards;
        }

        .overlay-icon {
            font-size: 72px;
            margin-bottom: 20px;
            color: var(--accent-color);
        }

        .overlay-icon.success {
            color: var(--success-color);
            animation: pulseSuccess 2s infinite;
        }

        @keyframes pulseSuccess {
            0% { transform: scale(1); }
            50% { transform: scale(1.04); }
            100% { transform: scale(1); }
        }

        .overlay-title {
            font-size: 38px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .overlay-desc {
            font-size: 18px;
            color: var(--text-secondary);
            max-width: 550px;
        }

        .overlay-invoice {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent-color);
            margin-top: 15px;
            padding: 8px 24px;
            border-radius: 12px;
            background: rgba(1, 153, 52, 0.06);
            border: 1px solid rgba(1, 153, 52, 0.1);
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="glass-panel m-3 mt-3 mb-0">
        <div class="store-logo">
            <i class="fa-solid fa-store"></i>
            <span>{{ $store->store_name ?? 'Southwest Farmers' }}</span>
        </div>
        <div class="welcome-text">
            <span>Welcome! We appreciate your business.</span>
        </div>
    </header>

    <!-- Main Container: Empty state by default -->
    <div class="main-container" id="mainPosGrid" style="display: none;">
        <!-- Left Side: Scanned Items -->
        <div class="cart-wrapper">
            <div class="cart-header">
                <i class="fa-solid fa-shopping-basket text-primary"></i>
                <span>Scanned Items</span>
            </div>
            <div class="cart-items-container glass-panel" id="cartContainer">
                <!-- Items will be injected dynamically -->
            </div>
        </div>

        <!-- Right Side: Order Summary -->
        <div class="glass-panel summary-card">
            <div>
                <div class="summary-header">
                    <span>Order Summary</span>
                </div>
                <div class="summary-row">
                    <span class="label text-muted">Subtotal</span>
                    <span class="val font-semibold" id="subtotalVal">$0.00</span>
                </div>
                <div class="summary-row">
                    <span class="label text-muted">Discount</span>
                    <span class="val text-danger font-semibold" id="discountVal">-$0.00</span>
                </div>
                <div class="summary-row">
                    <span class="label text-muted">Tax / GST (8%)</span>
                    <span class="val font-semibold" id="taxVal">$0.00</span>
                </div>
            </div>
            <div>
                <div class="summary-row total-row">
                    <span class="label">Total Amount</span>
                    <span class="val" id="totalVal">$0.00</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Screensaver / Empty Display State -->
    <div class="empty-display-state" id="emptyDisplay">
        <div class="promo-carousel">
            <div class="promo-slide">
                <i class="fa-solid fa-tags text-warning" style="font-size: 50px;"></i>
                <div class="promo-title">Fresh Quality, Better Value</div>
                <div class="promo-subtitle">Explore local produce and special pricing storewide!</div>
            </div>
        </div>
    </div>

    <!-- Overlay Screen: Payment Processing -->
    <div class="overlay-screen" id="paymentOverlay">
        <div class="overlay-icon">
            <i class="fa-solid fa-spinner fa-spin"></i>
        </div>
        <div class="overlay-title" id="paymentTitle">Processing Payment</div>
        <div class="overlay-desc" id="paymentDesc">Please complete the transaction on the payment terminal.</div>
    </div>

    <!-- Overlay Screen: Success Display -->
    <div class="overlay-screen" id="successOverlay">
        <div class="overlay-icon success">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="overlay-title">Transaction Approved!</div>
        <div class="overlay-desc">Thank you for shopping with us today. Have a wonderful day!</div>
        <div class="overlay-invoice" id="successInvoice">Invoice #: INV-2026-0001</div>
    </div>

    <script>
        const mainPosGrid = document.getElementById('mainPosGrid');
        const emptyDisplay = document.getElementById('emptyDisplay');
        const cartContainer = document.getElementById('cartContainer');
        const subtotalVal = document.getElementById('subtotalVal');
        const discountVal = document.getElementById('discountVal');
        const taxVal = document.getElementById('taxVal');
        const totalVal = document.getElementById('totalVal');

        const paymentOverlay = document.getElementById('paymentOverlay');
        const successOverlay = document.getElementById('successOverlay');
        const successInvoice = document.getElementById('successInvoice');

        const paymentTitle = document.getElementById('paymentTitle');
        const paymentDesc = document.getElementById('paymentDesc');

        // Create Broadcast Channel
        const posChannel = new BroadcastChannel('pos_display_channel');

        posChannel.onmessage = function(event) {
            console.log('Received Event:', event.data);
            const data = event.data;

            if (data.type === 'CART_UPDATE') {
                // Hide overlays
                paymentOverlay.style.display = 'none';
                successOverlay.style.display = 'none';

                const cart = data.cart || [];
                const totals = data.totals || {};

                if (cart.length === 0) {
                    mainPosGrid.style.display = 'none';
                    emptyDisplay.style.display = 'flex';
                } else {
                    emptyDisplay.style.display = 'none';
                    mainPosGrid.style.display = 'grid';

                    // Update summary prices
                    subtotalVal.innerText = `$${parseFloat(totals.subtotal || 0).toFixed(2)}`;
                    discountVal.innerText = `-$${parseFloat(totals.discount || 0).toFixed(2)}`;
                    taxVal.innerText = `$${parseFloat(totals.tax || totals.gst || 0).toFixed(2)}`;
                    totalVal.innerText = `$${parseFloat(totals.total || 0).toFixed(2)}`;

                    // Render cart items
                    cartContainer.innerHTML = '';
                    cart.forEach(item => {
                        const itemHtml = `
                            <div class="cart-item">
                                <div class="item-icon-box">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-name">${item.name}</div>
                                    <div class="item-price-unit">$${parseFloat(item.price).toFixed(2)} each</div>
                                </div>
                                <div class="item-qty-badge">x${item.quantity}</div>
                                <div class="item-total-price">$${(parseFloat(item.price) * item.quantity).toFixed(2)}</div>
                            </div>
                        `;
                        cartContainer.insertAdjacentHTML('beforeend', itemHtml);
                    });
                }
            } 
            else if (data.type === 'PAYMENT_INITIATE') {
                successOverlay.style.display = 'none';
                
                const method = data.method || 'cash';
                if (method === 'card') {
                    paymentTitle.innerText = 'Card Payment Processing';
                    paymentDesc.innerText = 'Please follow instructions on the card reader terminal...';
                } else {
                    paymentTitle.innerText = 'Completing Order';
                    paymentDesc.innerText = 'Please wait while the cashier processes your payment...';
                }

                paymentOverlay.style.display = 'flex';
            } 
            else if (data.type === 'PAYMENT_STATUS_CHANGE') {
                if (data.status === 'declined' || data.status === 'cancelled') {
                    paymentOverlay.style.display = 'none';
                }
            }
            else if (data.type === 'CHECKOUT_SUCCESS') {
                paymentOverlay.style.display = 'none';
                successInvoice.innerText = `Invoice #: ${data.invoice}`;
                successOverlay.style.display = 'flex';

                // Automatically return to welcome/screensaver screen after 5 seconds
                setTimeout(() => {
                    successOverlay.style.display = 'none';
                    mainPosGrid.style.display = 'none';
                    emptyDisplay.style.display = 'flex';
                }, 5000);
            }
            else if (data.type === 'RESET') {
                paymentOverlay.style.display = 'none';
                successOverlay.style.display = 'none';
                mainPosGrid.style.display = 'none';
                emptyDisplay.style.display = 'flex';
            }
        };

        // Broadcast to request initial state if POS is already open
        posChannel.postMessage({ type: 'CUSTOMER_DISPLAY_READY' });
    </script>
</body>
</html>
