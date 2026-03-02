<x-website-layout title="Your Shopping Cart">
    <div class="py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="container">
            <div class="d-flex align-items-center gap-3 mb-2">
                <i class="mdi mdi-cart-outline fs-3 text-primary"></i>
                <h1 class="fw-black mb-0 text-dark">Shopping Cart</h1>
            </div>
            <p class="text-muted mb-0">Manage your items and proceed to checkout</p>
        </div>
    </div>

    <div class="py-5">
        <div class="container">
            @if(!$cart || $cart->items->isEmpty())
                <div class="text-center">
                    <div class="bg-white rounded-4 p-5 shadow-sm border-0">
                        <i class="mdi mdi-cart-off text-muted" style="font-size: 5rem;"></i>
                        <h2 class="fw-bold mt-4 mb-2">Your cart is empty</h2>
                        <p class="text-muted mb-4 fs-5">Time to discover something amazing!</p>
                        <a href="{{ route('website.products.index') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                            <i class="mdi mdi-shopping-outline me-2"></i>Start Shopping
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="bg-white rounded-4 border-0 shadow-sm overflow-hidden">
                            <!-- Header -->
                            <div class="bg-light px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">{{ $cart->items->count() }} Item{{ $cart->items->count() !== 1 ? 's' : '' }}</h5>
                                <span class="badge bg-primary-subtle text-primary fs-6 fw-bold">
                                    <i class="mdi mdi-package-variant me-1"></i>Ready to checkout
                                </span>
                            </div>

                            <!-- Items -->
                            <div>
                                @foreach($cart->items as $item)
                                    <div class="cart-item px-4 py-4 border-bottom d-flex gap-4 align-items-start transition" id="item-{{ $item->id }}">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://placehold.co/120x120/ecfdf5/10b981?text=' . urlencode($item->product->product_name) }}" 
                                                 class="rounded-3" alt="{{ $item->product->product_name }}"
                                                 style="width: 120px; height: 120px; object-fit: contain; background: #f8fafc;">
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold mb-2 text-dark">{{ $item->product->product_name }}</h5>
                                            <div class="d-flex gap-3 align-items-center mb-3">
                                                <span class="fs-5 fw-bold text-primary">${{ number_format($item->product->price, 2) }}</span>
                                                @if($item->product->sku)
                                                    <span class="badge bg-secondary-subtle text-secondary fw-bold">SKU: {{ $item->product->sku }}</span>
                                                @endif
                                            </div>
                                            <p class="text-muted small mb-0">
                                                <i class="mdi mdi-check-circle text-success me-1"></i>In Stock
                                            </p>
                                        </div>

                                        <!-- Quantity & Actions -->
                                        <div class="d-flex gap-3 align-items-center flex-shrink-0">
                                            <div class="d-flex align-items-center border rounded-3 p-2" style="gap: 0.5rem;">
                                                <button class="btn btn-sm btn-link text-dark p-0 fw-bold" onclick="updateItemQty({{ $item->id }}, -1)" style="width: 32px; height: 32px;">
                                                    <i class="mdi mdi-minus"></i>
                                                </button>
                                                <span class="fw-bold text-dark px-3 quantity-display" id="qty-{{ $item->id }}" style="min-width: 2rem; text-align: center;">{{ $item->quantity }}</span>
                                                <button class="btn btn-sm btn-link text-dark p-0 fw-bold" onclick="updateItemQty({{ $item->id }}, 1)" style="width: 32px; height: 32px;">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                            </div>
                                            <button class="btn btn-sm btn-danger-subtle text-danger fw-bold rounded-3 p-2" onclick="removeItem({{ $item->id }})" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: none;">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div class="col-lg-4">
                        <div class="bg-white rounded-4 border-0 shadow-sm p-4 position-sticky" style="top: 20px;">
                            <!-- Header -->
                            <h5 class="fw-bold mb-4 pb-3 border-bottom d-flex align-items-center gap-2">
                                <i class="mdi mdi-receipt text-primary fs-5"></i>
                                Order Summary
                            </h5>

                            <!-- Promo Code -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">HAVE A PROMO CODE?</label>
                                <div class="input-group">
                                    <input type="text" id="promoCodeInput" class="form-control border-end-0" placeholder="Enter code" value="{{ $cart->coupon_code }}">
                                    <button class="btn btn-outline-primary fw-bold px-3" onclick="applyPromo()">APPLY</button>
                                </div>
                                <div id="promoFeedback" class="small mt-1 d-none"></div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted fw-500">Subtotal</span>
                                    @php $subtotal = $cart->items->sum('total'); @endphp
                                    <span class="fw-bold text-dark" id="summary-subtotal">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="transition-all {{ $cart->discount_amount > 0 ? '' : 'd-none' }}" id="discount-row">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success fw-500">Discount ({{ $cart->coupon_code }})</span>
                                        <span class="fw-bold text-success" id="summary-discount">-${{ number_format($cart->discount_amount, 2) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted fw-500">Tax (0%)</span>
                                    <span class="fw-bold text-dark">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted fw-500">Shipping</span>
                                    <span class="badge bg-success-subtle text-success fw-bold">
                                        <i class="mdi mdi-truck-fast me-1"></i>FREE
                                    </span>
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="mb-4 pt-3 border-top">
                                <label class="form-label small fw-bold text-muted mb-3">SELECT PAYMENT METHOD</label>
                                <div class="d-grid gap-2">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_card" value="card" checked>
                                    <label class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-3 rounded-3" for="pay_card">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-credit-card-outline fs-4 me-3"></i>
                                            <span class="fw-bold">Credit / Debit Card</span>
                                        </div>
                                        <i class="mdi mdi-check-circle fs-5 opacity-0 active-check"></i>
                                    </label>

                                    <input type="radio" class="btn-check" name="payment_method" id="pay_cod" value="cod">
                                    <label class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-3 rounded-3" for="pay_cod">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-cash-multiple fs-4 me-3"></i>
                                            <span class="fw-bold">Cash on Delivery</span>
                                        </div>
                                        <i class="mdi mdi-check-circle fs-5 opacity-0 active-check"></i>
                                    </label>
                                </div>
                            </div>

                            <style>
                                .btn-check:checked + .btn { border-color: var(--bs-primary); background: var(--bs-primary-bg-subtle); color: var(--bs-primary); }
                                .btn-check:checked + .btn .active-check { opacity: 1 !important; }
                            </style>

                            <!-- Total -->
                            <div class="bg-primary-subtle rounded-3 p-3 mb-4 text-center border">
                                <p class="text-muted small fw-bold mb-1">FINAL TOTAL</p>
                                <h3 class="fw-black mb-0 text-primary" id="summary-total">${{ number_format($cart->total_amount, 2) }}</h3>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-3">
                                <button class="btn btn-primary btn-lg fw-bold rounded-3 py-3" onclick="proceedToCheckout()">
                                    <i class="mdi mdi-shield-check-outline me-2"></i>Place Your Order
                                </button>
                                <a href="{{ route('website.products.index') }}" class="btn btn-outline-secondary btn-lg fw-bold rounded-3 text-decoration-none py-2">
                                    <i class="mdi mdi-arrow-left me-2"></i>Back to Shop
                                </a>
                            </div>

                            <!-- Trust Badges -->
                            <div class="mt-4 pt-4 border-top">
                                <div class="d-flex gap-2 flex-wrap justify-content-center">
                                    <span class="badge bg-light text-dark fw-bold small">
                                        <i class="mdi mdi-lock text-success me-1"></i>Secure
                                    </span>
                                    <span class="badge bg-light text-dark fw-bold small">
                                        <i class="mdi mdi-truck text-info me-1"></i>Fast Shipping
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function updateItemQty(itemId, delta) {
            const qtyEl = document.getElementById(`qty-${itemId}`);
            let newQty = parseInt(qtyEl.textContent) + delta;
            if (newQty < 1) return;

            const originalQty = qtyEl.textContent;
            qtyEl.textContent = '...';

            fetch(`{{ route('website.cart.index') }}/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quantity: newQty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    qtyEl.textContent = newQty;
                    document.getElementById('summary-subtotal').textContent = `$${data.cart_subtotal}`;
                    document.getElementById('summary-total').textContent = `$${data.cart_total}`;
                    
                    const discountRow = document.getElementById('discount-row');
                    const summaryDiscount = document.getElementById('summary-discount');
                    if (parseFloat(data.discount) > 0) {
                        discountRow.classList.remove('d-none');
                        summaryDiscount.textContent = `-$${data.discount}`;
                    } else {
                        discountRow.classList.add('d-none');
                    }
                } else {
                    qtyEl.textContent = originalQty;
                }
            })
            .catch(() => {
                qtyEl.textContent = originalQty;
            });
        }

        function applyPromo() {
            const code = document.getElementById('promoCodeInput').value;
            const feedback = document.getElementById('promoFeedback');
            const btn = event.target;

            if (!code) return;

            btn.disabled = true;
            feedback.classList.remove('d-none', 'text-success', 'text-danger');
            feedback.classList.add('text-muted');
            feedback.textContent = 'Applying...';

            fetch(`{{ route('website.cart.coupon') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                feedback.classList.remove('text-muted');
                feedback.classList.add(data.success ? 'text-success' : 'text-danger');
                feedback.textContent = data.message;

                if (data.success) {
                    document.getElementById('summary-total').textContent = `$${data.total}`;
                    document.getElementById('summary-discount').textContent = `-$${data.discount}`;
                    document.getElementById('discount-row').classList.remove('d-none');
                } else {
                    document.getElementById('discount-row').classList.add('d-none');
                }
            });
        }

        function proceedToCheckout() {
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            alert('Order placed successfully with ' + method.toUpperCase() + '! (Demo Checkout)');
            // In a real app, this would redirect or submit a form to a checkout handler
        }

        function removeItem(itemId) {
            if (!confirm('Remove this item from your cart?')) return;

            const itemEl = document.getElementById(`item-${itemId}`);
            itemEl.style.opacity = '0.5';

            fetch(`{{ route('website.cart.index') }}/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    itemEl.style.transition = 'all 0.3s ease';
                    itemEl.style.opacity = '0';
                    itemEl.style.height = '0';
                    itemEl.style.padding = '0';
                    itemEl.style.margin = '0';
                    
                    setTimeout(() => {
                        itemEl.remove();
                        document.getElementById('summary-subtotal').textContent = `$${data.cart_subtotal}`;
                        document.getElementById('summary-total').textContent = `$${data.cart_total}`;
                        
                        const discountRow = document.getElementById('discount-row');
                        const summaryDiscount = document.getElementById('summary-discount');
                        if (parseFloat(data.discount) > 0) {
                            discountRow.classList.remove('d-none');
                            summaryDiscount.textContent = `-$${data.discount}`;
                        } else {
                            discountRow.classList.add('d-none');
                        }

                        const badge = document.querySelector('.navbar .badge');
                        if (badge) badge.textContent = data.cart_count;

                        if (data.cart_count === 0) {
                            setTimeout(() => location.reload(), 300);
                        }
                    }, 300);
                }
            })
            .catch(err => {
                itemEl.style.opacity = '1';
                alert('Error removing item');
            });
        }
    </script>
    @endpush
</x-website-layout>