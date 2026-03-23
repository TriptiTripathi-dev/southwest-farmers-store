<x-app-layout title="POS Checkout (USA)">
    @push('styles')
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">
        <style>
            :root {
                --pos-primary: #019934;
                --pos-primary-dark: #004d1a;
                --pos-bg: #f3f4f6;
                --pos-border: #e2e8f0;
                --pos-text: #1e293b;
            }
            body { font-family: 'Manrope', sans-serif; background: var(--pos-bg); color: var(--pos-text); }
            .checkout-container { max-width: 1250px; margin: 30px auto; padding: 0 20px; }
            .back-btn { color: #64748b; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 24px; transition: color 0.2s; font-size: 14px; }
            .back-btn:hover { color: var(--pos-primary); }
            
            .card-custom { background: #fff; border: 1px solid var(--pos-border); border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); overflow: hidden; }
            .section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; border-bottom: 1.5px solid #f1f5f9; padding-bottom: 15px; }
            .section-header i { font-size: 24px; color: var(--pos-primary); background: #f0fdf4; padding: 8px; border-radius: 12px; }
            .section-header h2 { font-size: 1.15rem; font-weight: 800; margin: 0; color: #334155; }
            
            /* Table Styles */
            .table-items thead { background: #f8fafc; }
            .table-items th { font-weight: 700; text-transform: uppercase; font-size: 11px; color: #94a3b8; padding: 18px 16px; border: none; letter-spacing: 0.5px; }
            .table-items td { padding: 20px 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
            .product-info { display: flex; align-items: center; gap: 15px; }
            .product-thumb { width: 56px; height: 56px; border-radius: 12px; object-fit: cover; background: #f1f5f9; border: 1px solid var(--pos-border); }
            
            /* Payment Grid */
            .payment-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; }
            .pay-method-card { border: 2px solid #f1f5f9; border-radius: 15px; padding: 18px 10px; text-align: center; cursor: pointer; transition: all 0.2s; background: #fff; display: flex; flex-direction: column; align-items: center; gap: 8px; position: relative; }
            .pay-method-card i { font-size: 28px; color: #94a3b8; }
            .pay-method-card .label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; }
            .pay-method-card.active { border-color: var(--pos-primary); background: #f0fdf4; box-shadow: 0 4px 12px rgba(1, 153, 52, 0.12); }
            .pay-method-card.active i, .pay-method-card.active .label { color: var(--pos-primary); }
            .pay-method-card.active::after { content: '✓'; position: absolute; top: 8px; right: 10px; background: var(--pos-primary); color: #fff; width: 18px; height: 18px; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 900; }
            
            /* Totals */
            .summary-box { background: #f8fafc; border-radius: 18px; padding: 24px; margin-top: 20px; border: 1px dashed #cbd5e1; }
            .summary-row { display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 15px; font-weight: 600; color: #64748b; }
            .summary-row.total-main { font-size: 1.85rem; font-weight: 900; color: var(--pos-primary); border-top: 2px solid #e2e8f0; padding-top: 18px; margin-top: 15px; }
            
            .btn-checkout-finalize { background: linear-gradient(135deg, var(--pos-primary), var(--pos-primary-dark)); color: #fff; border: none; border-radius: 16px; padding: 20px; width: 100%; font-weight: 800; font-size: 1.25rem; box-shadow: 0 10px 25px rgba(1, 153, 52, 0.35); transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 25px; }
            .btn-checkout-finalize:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(1, 153, 52, 0.45); }
            .btn-checkout-finalize:active { transform: translateY(0); }
            .btn-checkout-finalize:disabled { opacity: 0.5; cursor: not-allowed; filter: grayscale(0.5); }

            /* Mobile visibility fixes */
            @media (max-width: 768px) {
                .checkout-container { margin: 15px auto; }
                .card-custom { border-radius: 15px; }
                .table-items td, .table-items th { padding: 12px 10px; }
                .product-thumb { width: 44px; height: 44px; }
                .product-info { gap: 10px; }
                .product-name { font-size: 13px !important; }
                .summary-row.total-main { font-size: 1.5rem; }
            }
        </style>
    @endpush

    <div class="checkout-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('store.sales.pos') }}" class="back-btn m-0">
                <i class="mdi mdi-arrow-left"></i>
                <span>RETURN TO CATALOG</span>
            </a>
            <button class="btn btn-outline-warning fw-bold d-flex align-items-center gap-2 rounded-pill px-3" onclick="openHeldOrders()">
                <i class="mdi mdi-pause-circle-outline fs-5"></i>
                <span>HELD ORDERS <span class="badge bg-warning text-dark ms-1" id="heldCountBadge">0</span></span>
            </button>
        </div>

        <div class="row g-4">
            {{-- Order Details Section --}}
            <div class="col-lg-7">
                <div class="card-custom h-100 p-4">
                    <div class="section-header">
                        <i class="mdi mdi-shopping-outline"></i>
                        <h2>Order Items</h2>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-items">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Product Details</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentCart->items as $item)
                                @php $rp = floor($item->product->price) + 0.9; @endphp
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://placehold.co/100x100?text=F' }}" class="product-thumb">
                                            <div class="min-w-0">
                                                <div class="fw-bold text-dark product-name text-truncate" style="font-size: 15px;">{{ $item->product->product_name }}</div>
                                                <small class="text-muted d-block" style="font-size: 12px;">${{ number_format($rp, 2) }} / {{ $item->product->unit_type }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border fw-bold px-3 py-2" style="font-size: 13px;">
                                            {{ (float)$item->quantity }} <small>{{ $item->product->unit_type }}</small>
                                        </span>
                                    </td>
                                    <td class="text-end fw-extrabold text-primary" style="font-size: 15px;">
                                        ${{ number_format($rp * $item->quantity, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Checkout Actions Section --}}
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    {{-- Customer Card --}}
                    <div class="card-custom p-4">
                        <div class="section-header">
                            <i class="mdi mdi-account-card-outline"></i>
                            <h2>Customer Information</h2>
                        </div>
                        
                        <div class="customer-selector" id="customerSearchContainer">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" id="customerSearch" class="form-control border-start-0 fs-6" placeholder="Find customer by phone or name..." autocomplete="off">
                                <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                    <i class="mdi mdi-account-plus"></i>
                                </button>
                            </div>
                            <div id="customerDropdown" class="position-absolute bg-white border rounded-3 shadow hidden w-100 mt-1" style="z-index: 1050; max-height: 250px; overflow-y: auto;"></div>
                        </div>

                        <div id="customerActiveBadge" class="mt-3 p-3 rounded-4 d-none border-0" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border" style="width: 44px; height: 44px;">
                                        <i class="mdi mdi-account text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6" id="activeCustName">-</div>
                                        <div class="text-muted small fw-bold" id="activeCustPhone">-</div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-danger border-0 fw-bold" onclick="clearSelectedCustomer()">REMOVE</button>
                            </div>
                        </div>
                        <input type="hidden" id="selectedCustomerId" value="">
                    </div>

                    {{-- Payment & Total Card --}}
                    <div class="card-custom p-4">
                        <div class="section-header">
                            <i class="mdi mdi-shield-check-outline"></i>
                            <h2>Payment & Finalize</h2>
                        </div>

                        <div class="payment-grid">
                            <div class="pay-method-card active" onclick="setPaymentMethod('cash', this)">
                                <i class="mdi mdi-cash-multiple"></i>
                                <span class="label">Cash</span>
                            </div>
                            <div class="pay-method-card" onclick="setPaymentMethod('card', this)">
                                <i class="mdi mdi-credit-card-outline"></i>
                                <span class="label">Card</span>
                            </div>
                            <div class="pay-method-card" onclick="setPaymentMethod('check', this)">
                                <i class="mdi mdi-checkbook"></i>
                                <span class="label">Check</span>
                            </div>
                        </div>
                        <input type="hidden" id="paymentMethodValue" value="cash">

                        {{-- Card Terminal UI --}}
                        @if($paxEnabled)
                        <div id="cardTerminalPanel" class="d-none mb-3 p-3 rounded-4 border-2 border-dashed bg-light text-center">
                            <div class="text-muted small fw-bold mb-2">PAX TERMINAL STATUS</div>
                            <div id="cardStatusText" class="fw-bold text-primary mb-3">Awaiting Authorization</div>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold" onclick="launchCardAuth()">
                                <i class="mdi mdi-lock-outline me-1"></i>Start Auth
                            </button>
                        </div>
                        @endif

                        <div class="summary-box">
                            @php
                                $subtotalRaw = 0;
                                foreach($currentCart->items as $i) {
                                    $subtotalRaw += (floor($i->product->price) + 0.9) * $i->quantity;
                                }
                                $taxRate = 0.08;
                            @endphp
                            <div class="summary-row">
                                <span>Cart Subtotal</span>
                                <span id="summarySubtotal">${{ number_format($subtotalRaw, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Estimated Tax (8%)</span>
                                <span id="summaryTax">${{ number_format($subtotalRaw * $taxRate, 2) }}</span>
                            </div>
                            <div class="summary-row align-items-center">
                                <span>Applied Discount</span>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" id="discountValue" class="form-control form-control-sm text-end fw-bold" style="width: 85px; border-radius: 8px;" value="0" min="0" step="0.01">
                                    <span id="discountDisplayLabel" class="text-danger">-$0.00</span>
                                </div>
                            </div>
                            <div class="summary-row total-main">
                                <span>ORDER TOTAL</span>
                                <span id="summaryGrandTotal">${{ number_format($subtotalRaw * (1 + $taxRate), 2) }}</span>
                            </div>
                        </div>

                        <button id="btnCompleteSale" class="btn-checkout-finalize" onclick="initiateFinalCheckout()">
                            <i class="mdi mdi-check-circle-outline"></i>
                            <span>FINALIZE TRANSACTION</span>
                        </button>

                        <button class="btn btn-outline-secondary w-100 rounded-4 py-3 fw-bold mt-3 d-flex align-items-center justify-content-center gap-2" style="border-width: 2px;" onclick="holdCart()">
                            <i class="mdi mdi-pause-circle-outline fs-4"></i>
                            <span>HOLD ORDER FOR LATER</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form id="createCustomerForm">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold fs-4">Add Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg bg-light border-0" required placeholder="John Doe">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold text-muted small uppercase">Phone Number</label>
                            <input type="text" name="phone" class="form-control form-control-lg bg-light border-0" required placeholder="555-0199">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3">Save & Select</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Card Auth Modal (PAX Integration) -->
    <div class="modal fade" id="cardAuthModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 text-center">
                <div class="modal-body py-5">
                    <div id="paxStatusIcon" class="mb-4">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                    </div>
                    <h4 id="paxStatusTitle" class="fw-bold mb-2">Connecting to PAX...</h4>
                    <p id="paxStatusDesc" class="text-muted mb-4">Please wait while we reach the payment terminal.</p>
                    
                    <div id="paxAmountDisplay" class="d-none badge bg-light text-primary fs-5 px-4 py-2 rounded-pill border mb-4">
                        AMOUNT: <span id="paxAmountVal">$0.00</span>
                    </div>

                    <div class="d-grid gap-2 px-4">
                        <button id="btnCancelPax" class="btn btn-outline-danger rounded-pill py-2 fw-bold d-none" onclick="cancelPaxPayment()">
                            <i class="mdi mdi-close-circle-outline me-1"></i>CANCEL PAYMENT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Printer Selection -->
    <div class="modal fade" id="printerModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-printer me-2"></i>Select Hardware Printer</h5>
                </div>
                <div class="modal-body p-0">
                    <div id="printerSpinner" class="text-center py-5">
                        <div class="spinner-grow text-primary" role="status"></div>
                        <div class="mt-3 text-muted fw-bold">Fetching local printer list...</div>
                    </div>
                    <div id="printerList" class="list-group list-group-flush"></div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-link text-muted fw-bold" onclick="skipPrinting()">Close Without Printing</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" id="executePrintBtn" disabled onclick="triggerHardwarePrint()">
                        <i class="mdi mdi-printer-check me-1"></i>Confirm Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Held Orders Modal -->
    <div class="modal fade" id="heldOrdersModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title fw-extrabold"><i class="mdi mdi-pause-circle-outline me-2"></i>Held Orders List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="modalHeldCartsList" class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                        <div class="p-5 text-center text-muted">
                            <i class="mdi mdi-loading mdi-spin fs-1 mb-3 d-block"></i>
                            <div class="fw-bold">Fetching held orders...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const TAX_RATE = 0.08;
            const csrfToken = '{{ csrf_token() }}';
            let cartItems = @json($currentCart->items);
            let cartSubtotal = 0;
            
            // Calc dynamic subtotal
            cartItems.forEach(i => {
                let rp = Math.floor(i.product.price) + 0.9;
                cartSubtotal += rp * i.quantity;
            });

            function refreshTotals() {
                let discount = parseFloat($('#discountValue').val() || 0);
                let taxable = Math.max(0, cartSubtotal - discount);
                let tax = taxable * TAX_RATE;
                let total = taxable + tax;

                $('#summarySubtotal').text('$' + cartSubtotal.toFixed(2));
                $('#summaryTax').text('$' + tax.toFixed(2));
                $('#discountDisplayLabel').text('-$' + discount.toFixed(2));
                $('#summaryGrandTotal').text('$' + total.toFixed(2));
            }

            $('#discountValue').on('input', refreshTotals);

            // Customer Logic
            $('#customerSearch').on('keyup', function() {
                let val = $(this).val();
                if (val.length < 2) { $('#customerDropdown').addClass('hidden'); return; }
                $.get("{{ route('store.sales.customers.search') }}", { term: val }, function(data) {
                    let html = data.length === 0 ? '<div class="p-3 text-center text-muted">No customers found</div>' : 
                        data.map(c => `<div class="p-3 border-bottom cursor-pointer hover-bg-light" onclick="pickCustomer(${c.id},'${c.name}','${c.phone}')">
                            <div class="fw-bold">${c.name}</div><div class="text-muted small">${c.phone}</div>
                        </div>`).join('');
                    $('#customerDropdown').html(html).removeClass('hidden');
                });
            });

            window.pickCustomer = function(id, name, phone) {
                $('#selectedCustomerId').val(id);
                $('#activeCustName').text(name);
                $('#activeCustPhone').text(phone);
                $('#customerActiveBadge').removeClass('d-none');
                $('#customerSearchContainer').addClass('d-none');
                $('#customerDropdown').addClass('hidden');
            }

            window.clearSelectedCustomer = function() {
                $('#selectedCustomerId').val('');
                $('#customerActiveBadge').addClass('d-none');
                $('#customerSearchContainer').removeClass('d-none');
                $('#customerSearch').val('').focus();
            }

            $('#createCustomerForm').on('submit', function(e) {
                e.preventDefault();
                $.post("{{ route('store.sales.customers.store') }}", $(this).serialize(), function(res) {
                    if (res.success) {
                        $('#addCustomerModal').modal('hide');
                        pickCustomer(res.customer.id, res.customer.name, res.customer.phone);
                        Swal.fire({ icon: 'success', title: 'Customer Saved', timer: 1000, showConfirmButton: false });
                    }
                });
            });

            // Payment Logic
            let activePayment = 'cash';
            const paxEnabled = {{ $paxEnabled ? 'true' : 'false' }};
            window.setPaymentMethod = function(method, el) {
                $('.pay-method-card').removeClass('active');
                $(el).addClass('active');
                activePayment = method;
                $('#paymentMethodValue').val(method);

                if (paxEnabled) {
                    $('#cardTerminalPanel').toggleClass('d-none', method !== 'card');
                } else {
                    $('#cardTerminalPanel').addClass('d-none');
                }
            }
            
            // --- PAX REAL INTEGRATION ---
            let paxModal = null;
            let cardState = { status: null, amount: 0, ref_num: null };

            window.launchCardAuth = function() {
                const total = parseFloat($('#summaryGrandTotal').text().replace('$',''));
                $('#paxAmountVal').text('$' + total.toFixed(2));
                
                // 1. Show processing modal
                paxModal = new bootstrap.Modal(document.getElementById('cardAuthModal'));
                paxModal.show();
                
                updatePaxUI('Connecting...', 'Checking terminal status...', 'spinner');
                
                // 2. Check Status
                $.get("{{ route('store.sales.payment-status') }}")
                    .done(res => {
                        if (res.success && res.online) {
                            // 3. Initiate Payment
                            startPaxTransaction(total);
                        } else {
                            handlePaxError(res.message || 'Terminal is OFFLINE. Please check connection.');
                        }
                    })
                    .fail(() => handlePaxError('Could not reach PAX Agent.'));
            }

            function startPaxTransaction(amount) {
                updatePaxUI('Processing...', 'Follow instructions on PAX device.', 'spinner', true);
                $('#paxAmountDisplay').removeClass('d-none');
                $('#btnCancelPax').removeClass('d-none');

                const orderId = 'ORD-' + Date.now(); // Unique order ID for initiation

                $.post("{{ route('store.sales.payment-initiate') }}", {
                    _token: csrfToken,
                    amount: amount,
                    order_id: orderId
                }).done(res => {
                    if (res.success) {
                        cardState = {
                            status: 'approved',
                            amount: amount,
                            ref_num: res.refNum || null
                        };
                        updatePaxUI('APPROVED', 'Card authorized successfully.', 'success');
                        setTimeout(() => {
                            paxModal.hide();
                            $('#cardStatusText').text(`APPROVED: $${amount.toFixed(2)}`).removeClass('text-danger').addClass('text-success');
                            // Auto-trigger finalize if you want, or let user click button.
                            // initiateFinalCheckout(); 
                        }, 1500);
                    } else {
                        handlePaxError(res.message || 'Payment declined or failed.');
                    }
                }).fail(xhr => {
                    handlePaxError(xhr.responseJSON?.message || 'Transaction failed.');
                });
            }

            window.cancelPaxPayment = function() {
                $('#btnCancelPax').prop('disabled', true).text('Cancelling...');
                $.post("{{ route('store.sales.payment-cancel') }}", { _token: csrfToken })
                    .always(() => {
                        handlePaxError('Payment cancelled by user.');
                    });
            }

            function updatePaxUI(title, desc, type, showCancel = false) {
                $('#paxStatusTitle').text(title);
                $('#paxStatusDesc').text(desc);
                
                let iconHtml = '';
                if (type === 'spinner') {
                    iconHtml = '<div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>';
                } else if (type === 'success') {
                    iconHtml = '<i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>';
                    $('#btnCancelPax').addClass('d-none');
                } else if (type === 'error') {
                    iconHtml = '<i class="mdi mdi-alert-circle text-danger" style="font-size: 4rem;"></i>';
                    $('#btnCancelPax').addClass('d-none');
                }
                $('#paxStatusIcon').html(iconHtml);
            }

            function handlePaxError(msg) {
                updatePaxUI('Failed', msg, 'error');
                cardState.status = 'failed';
                $('#cardStatusText').text('CARD FAILED').addClass('text-danger');
                setTimeout(() => {
                    if (paxModal) paxModal.hide();
                }, 3000);
            }

            // FINAL CHECKOUT
            window.initiateFinalCheckout = function() {
                if (!$('#selectedCustomerId').val()) return Swal.fire('Customer Required', 'Please select or add a customer to continue.', 'error');
                if (activePayment === 'card' && cardState.status !== 'approved') return Swal.fire('Incomplete Payment', 'Please complete card terminal authorization.', 'error');

                const totalNum = parseFloat($('#summaryGrandTotal').text().replace('$',''));
                
                const data = {
                    _token: csrfToken,
                    customer_id: $('#selectedCustomerId').val(),
                    payment_method: activePayment,
                    subtotal: cartSubtotal,
                    tax_amount: cartSubtotal * TAX_RATE,
                    gst_amount: cartSubtotal * TAX_RATE,
                    discount_amount: parseFloat($('#discountValue').val() || 0),
                    total_amount: totalNum,
                    cart: JSON.stringify(cartItems.map(i => {
                        let rp = Math.floor(i.product.price) + 0.9;
                        return { id: i.product_id, quantity: i.quantity, price: rp };
                    })),
                    card_auth_status: cardState.status,
                    card_approved_amount: cardState.amount
                };

                Swal.fire({ title: 'Processing Transaction', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.post("{{ route('store.sales.checkout') }}", data)
                    .done(res => {
                        if (res.success) handleCheckoutSuccess(res);
                        else Swal.fire('Checkout Failed', res.message, 'error');
                    })
                    .fail(xhr => Swal.fire('System Error', xhr.responseJSON?.message || 'Check connection.', 'error'));
            }

            let lastInvoice = null;
            function handleCheckoutSuccess(res) {
                lastInvoice = res.invoice;
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Successful',
                    text: `Invoice #${lastInvoice} created.`,
                    confirmButtonText: 'Print Receipt & Finish',
                    allowEscapeKey: false,
                    allowOutsideClick: false
                }).then((res) => {
                    startPrinterFlow();
                });
            }

            // Printer Logic
            function startPrinterFlow() {
                $('#printerSpinner').removeClass('d-none');
                $('#printerList').empty();
                $('#executePrintBtn').prop('disabled', true);
                new bootstrap.Modal('#printerModal').show();

                $.get("{{ route('store.sales.get-printers') }}")
                    .done(data => {
                        $('#printerSpinner').addClass('d-none');
                        if (data.success && data.printers && data.printers.length > 0) {
                            data.printers.forEach(p => {
                                $('#printerList').append(`
                                    <button class="list-group-item list-group-item-action d-flex justify-content-between p-3" onclick="setTargetPrinter('${p}', this)">
                                        <div class="fw-bold"><i class="mdi mdi-printer-pos me-2 text-primary"></i>${p}</div>
                                        <div class="text-success small fw-bold">Ready</div>
                                    </button>
                                `);
                            });
                        } else {
                            $('#printerList').html('<div class="p-4 text-center text-warning fw-bold">No retail printers found on Agent.</div>');
                        }
                    })
                    .fail(() => {
                        $('#printerSpinner').addClass('d-none');
                        $('#printerList').html('<div class="p-4 text-center text-danger fw-bold">Agent connection failed.</div>');
                    });
            }

            let printerToUse = null;
            window.setTargetPrinter = function(name, el) {
                $('.list-group-item').removeClass('active').removeClass('bg-primary-subtle');
                $(el).addClass('active').addClass('bg-primary-subtle');
                printerToUse = name;
                $('#executePrintBtn').prop('disabled', false);
            }

            window.triggerHardwarePrint = function() {
                if (!lastInvoice || !printerToUse) return;
                $('#executePrintBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Printing...');

                $.post("{{ route('store.sales.manual-print') }}", {
                    _token: csrfToken,
                    invoice_number: lastInvoice,
                    printer_name: printerToUse
                }).done(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Print Success',
                            text: 'Receipt sent to printer successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('store.sales.pos') }}";
                        });
                    } else {
                        Swal.fire('Print Error', res.message, 'error').then(() => {
                            window.location.href = "{{ route('store.sales.pos') }}";
                        });
                    }
                }).fail(() => {
                    Swal.fire('Agent Error', 'Could not reach hardware agent.', 'error').then(() => {
                        window.location.href = "{{ route('store.sales.pos') }}";
                    });
                });
            }

            window.skipPrinting = function() {
                window.location.href = "{{ route('store.sales.pos') }}";
            }

            // --- HELD ORDERS LOGIC ---
            window.openHeldOrders = function() {
                renderHeldCarts();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('heldOrdersModal')).show();
            }

            window.holdCart = function() {
                Swal.fire({
                    title: 'Hold this order?',
                    text: "You can restore it later from the POS or Checkout screen.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Yes, Hold it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('store.sales.cart.clear') }}", { 
                            _token: csrfToken, 
                            hold: true 
                        }, function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Order Held',
                                    text: 'This order has been moved to Held Orders.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('store.sales.pos') }}";
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Could not hold order.', 'error');
                            }
                        });
                    }
                });
            }

            window.renderHeldCarts = function() {
                $.ajax({
                    url: "{{ route('store.sales.orders') }}",
                    method: 'GET',
                    data: { status: 'held' },
                    success: function(res) {
                        let orders = res.data || res;
                        let count = orders.length;
                        $('#heldCountBadge').text(count);
                        
                        let container = $('#modalHeldCartsList');
                        if (count === 0) {
                            container.html('<div class="p-5 text-center text-muted">No held orders found.</div>');
                        } else {
                            let html = orders.map(o => `
                                <div class="list-group-item p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-0">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Held Order #${o.id}</h6>
                                            <div class="text-muted small">Items: ${o.items_count} | Total: $${parseFloat(o.total_amount).toFixed(2)}</div>
                                            <div class="text-muted tiny mt-1" style="font-size: 10px;">${new Date(o.updated_at).toLocaleString()}</div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-primary px-3 fw-bold rounded-pill" onclick="restoreHeld(${o.id})">
                                                RESTORE
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger px-2 rounded-circle" onclick="deleteHeld(${o.id})">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                            container.html(html);
                        }
                    }
                });
            }

            window.restoreHeld = function(id) {
                Swal.fire({
                    title: 'Restore this order?',
                    text: "This will replace your current active cart items.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#019934',
                    confirmButtonText: 'Restore'
                }).then(r => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: `/store/orders/held/${id}/restore`,
                            method: 'POST',
                            data: { _token: csrfToken },
                            success: function(res) {
                                if (res.success) {
                                    toastr.success(res.message);
                                    let modal = bootstrap.Modal.getInstance(document.getElementById('heldOrdersModal'));
                                    if (modal) modal.hide();
                                    location.reload(); 
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
                            data: { _token: csrfToken },
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

            // Initial badge sync
            renderHeldCarts();
        });
    </script>
    <style>
        .cursor-pointer { cursor: pointer; }
        .hover-bg-light:hover { background: #f8fafc; }
        .hidden { display: none !important; }
        .fw-extrabold { font-weight: 800; }
    </style>
    @endpush
</x-app-layout>
