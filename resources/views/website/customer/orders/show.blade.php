<x-website-layout title="Order Details #{{ $order->invoice_number }} - Southwest Farmers">
    <div class="py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="container">
            <div class="d-flex align-items-center gap-3 mb-2">
                <a href="{{ route('website.orders.index') }}" class="btn btn-light rounded-circle p-2 border shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-arrow-left fs-5 text-dark"></i>
                </a>
                <h1 class="fw-black mb-0 text-dark">Order Details</h1>
            </div>
            <p class="text-muted mb-0">Invoice #{{ $order->invoice_number }} &nbsp;·&nbsp; Placed on {{ $order->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Order Content -->
                <div class="col-lg-8">
                    <!-- Order Status Card -->
                    <div class="bg-white rounded-4 border-0 shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Order Status</h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-2 rounded-pill text-uppercase">
                                {{ $order->status ?? 'pending' }}
                            </span>
                        </div>
                        
                        <div class="position-relative py-3">
                            <div class="progress" style="height: 4px;">                                @php
                                    $progress = [
                                        'pending' => 15,
                                        'processing' => 50,
                                        'completed' => 100,
                                        'paid' => 100,
                                        'cancelled' => 0,
                                        'payment_failed' => 0,
                                    ][$order->status ?? 'pending'] ?? 15;
                                    $isFailed = in_array($order->status, ['cancelled', 'payment_failed']);
                                @endphp
                                <div class="progress-bar {{ $isFailed ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $isFailed ? 100 : $progress }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-3 text-center small fw-bold text-muted">
                                <div style="width: 25%">
                                    <i class="mdi mdi-file-document-outline d-block fs-4 {{ $isFailed ? 'text-danger' : ($progress >= 15 ? 'text-success' : '') }}"></i>
                                    {{ $isFailed ? 'CANCELLED' : 'PLACED' }}
                                </div>
                                <div style="width: 25%">
                                    <i class="mdi mdi-package-variant-closed d-block fs-4 {{ !$isFailed && $progress >= 50 ? 'text-success' : '' }}"></i>
                                    PACKED
                                </div>
                                <div style="width: 25%">
                                    <i class="mdi mdi-truck-delivery-outline d-block fs-4 {{ !$isFailed && $progress >= 100 ? 'text-success' : '' }}"></i>
                                    SHIPPED
                                </div>
                                <div style="width: 25%">
                                    <i class="mdi mdi-check-decagram d-block fs-4 {{ !$isFailed && $progress >= 100 ? 'text-success' : '' }}"></i>
                                    DELIVERED
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Card -->
                    <div class="bg-white rounded-4 border-0 shadow-sm overflow-hidden">
                        <div class="p-4 border-bottom bg-light">
                            <h5 class="fw-bold mb-0">Order Items ({{ $order->items->count() }})</h5>
                        </div>
                        <div>
                            @foreach($order->items as $item)
                            <div class="p-4 border-bottom d-flex gap-4 align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="{{ $item->product->image ? Storage::disk('r2')->url($item->product->image) : 'https://placehold.co/80x80/ecfdf5/10b981?text=' . urlencode($item->product->product_name) }}" 
                                         class="rounded-3 border" 
                                         style="width: 80px; height: 80px; object-fit: contain; background: #f8fafc;">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $item->product->product_name }}</h6>
                                    <p class="text-muted small mb-0">Price: ${{ number_format($item->price, 2) }} &nbsp;·&nbsp; Qty: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-end">
                                    <span class="fw-black text-dark fs-5">${{ number_format($item->total, 2) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-white rounded-4 border-0 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-4 pb-3 border-bottom">Price Summary</h5>
                        <div class="space-y-3 mb-4">
                            <div class="d-flex justify-content-between text-muted fw-bold small">
                                <span>SUBTOTAL</span>
                                <span class="text-dark">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-success fw-bold small">
                                <span>DISCOUNT</span>
                                <span>-${{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted fw-bold small">
                                <span>TAX (0%)</span>
                                <span class="text-dark">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted fw-bold small">
                                <span>SHIPPING</span>
                                <span class="text-success">FREE</span>
                            </div>
                        </div>
                        <div class="bg-primary-subtle rounded-3 p-3 text-center border">
                            <p class="text-muted small fw-bold mb-1">TOTAL PAID</p>
                            <h3 class="fw-black mb-0 text-primary">${{ number_format($order->total_amount, 2) }}</h3>
                        </div>
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-muted small fw-bold mb-2">PAYMENT METHOD</p>
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-credit-card-outline fs-4 text-theme"></i>
                                <span class="fw-bold text-dark">{{ strtoupper($order->payment_method) }}</span>
                            </div>
                        </div>
                        @if(in_array($order->status, ['pending', 'payment_failed']) && $order->payment_method === 'card')
                        <div class="mt-4 pt-4 border-top">
                            <button id="btnRetryPayment" class="btn btn-primary w-100 rounded-pill fw-bold py-3 shadow" onclick="retryPayment()">
                                <i class="mdi mdi-credit-card me-2"></i>Retry Card Payment
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-4 border-0 shadow-sm p-4">
                        <h5 class="fw-bold mb-4 pb-3 border-bottom">Delivery Address</h5>
                        <div class="d-flex gap-3">
                            <div class="d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="mdi mdi-map-marker text-primary fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ auth('customer')->user()->name }}</h6>
                                <p class="text-muted small mb-0">{{ auth('customer')->user()->address ?? 'Address not specified' }}</p>
                                <p class="text-muted small mb-0">{{ auth('customer')->user()->phone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center d-grid gap-2">
                        <a href="{{ route('website.orders.invoice', $order->id) }}" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="mdi mdi-file-pdf-box me-2"></i>Download PDF Invoice
                        </a>
                        <a href="{{ route('website.contact') }}" class="btn btn-outline-secondary w-100 rounded-pill fw-bold py-2">
                             <i class="mdi mdi-lifebuoy me-2"></i>Need Help with this Order?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function retryPayment() {
            const btn = document.getElementById('btnRetryPayment');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';
            }

            Swal.fire({
                title: 'Initializing Payment',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch("{{ route('website.orders.retry-payment', $order->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.redirect) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Initialized',
                        text: 'Redirecting to payment gateway...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire('Error', data.message || 'Unable to start payment.', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="mdi mdi-credit-card me-2"></i>Retry Card Payment';
                    }
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Connection failed.', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-credit-card me-2"></i>Retry Card Payment';
                }
            });
        }
    </script>
    @endpush
</x-website-layout>
