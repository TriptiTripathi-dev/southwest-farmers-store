<x-website-layout title="Payment Failed | Southwest Farmers">
    <div class="py-5" style="min-height: 80vh; background: linear-gradient(135deg, #fef2f2 0%, #f9fafb 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 text-center">
                    <div class="bg-white rounded-5 shadow-lg p-5 border-0">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle" style="width: 100px; height: 100px; box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);">
                                <i class="mdi mdi-close-circle-outline display-3"></i>
                            </div>
                        </div>
                        
                        <h1 class="display-5 fw-black text-danger mb-3">Payment Failed</h1>
                        <p class="fs-5 text-muted mb-4">We were unable to process your payment transaction.</p>
                        
                        <!-- Error Callout -->
                        <div class="alert alert-danger border-0 rounded-4 p-4 text-start mb-4">
                            <h6 class="fw-bold text-danger mb-2"><i class="mdi mdi-alert-circle me-1"></i>Declination Reason:</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.95rem;">{{ $error }}</p>
                        </div>

                        <!-- Order Ref -->
                        <div class="bg-light rounded-4 p-4 mb-4 border">
                            <div class="row text-start g-3">
                                <div class="col-6">
                                    <small class="text-muted fw-bold d-block mb-1">ORDER NUMBER</small>
                                    <span class="fw-black text-dark fs-5">#{{ $order->invoice_number }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted fw-bold d-block mb-1">TOTAL AMOUNT</small>
                                    <span class="fw-black text-danger fs-5">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-3 d-sm-flex justify-content-center mb-4">
                            <button id="btnRetryPayment" class="btn btn-danger btn-lg px-4 rounded-pill fw-bold" onclick="retryPayment()">
                                <i class="mdi mdi-refresh me-2"></i>Retry Payment
                            </button>
                            <a href="{{ route('website.cart.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill fw-bold">
                                Go to Cart
                            </a>
                            <a href="{{ route('website.home') }}" class="btn btn-light btn-lg px-4 rounded-pill fw-bold border">
                                <i class="mdi mdi-home-outline me-2"></i>Home
                            </a>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top text-muted small">
                            <p class="mb-0">If you continue to experience issues, please use a different card or <a href="{{ route('website.contact') }}" class="text-primary fw-bold">Contact Support</a>.</p>
                        </div>
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
                text: 'Redirecting to payment page, please wait...',
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
                        btn.innerHTML = '<i class="mdi mdi-refresh me-2"></i>Retry Payment';
                    }
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Connection failed.', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-refresh me-2"></i>Retry Payment';
                }
            });
        }
    </script>
    @endpush
</x-website-layout>
