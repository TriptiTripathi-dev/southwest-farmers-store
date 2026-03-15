<x-website-layout title="Order Successful | Southwest Farmers">
    <div class="py-5" style="min-height: 80vh; display: flex; align-items: center; background: #f8fafc;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center">
                    <div class="bg-white rounded-5 shadow-lg p-5 border-0">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 100px; height: 100px; box-shadow: 0 10px 20px rgba(0, 154, 54, 0.3);">
                                <i class="mdi mdi-check-decagram display-3"></i>
                            </div>
                        </div>
                        
                        <h1 class="display-4 fw-black text-dark mb-3">Order Successful!</h1>
                        <p class="fs-5 text-muted mb-5">Thank you for your purchase. Your order has been placed and is being processed.</p>
                        
                        <div class="bg-light rounded-4 p-4 mb-5 border">
                            <div class="row text-start g-3">
                                <div class="col-6">
                                    <small class="text-muted fw-bold d-block mb-1">ORDER NUMBER</small>
                                    <span class="fw-black text-dark fs-5">#{{ $order->invoice_number }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted fw-bold d-block mb-1">TOTAL AMOUNT</small>
                                    <span class="fw-black text-primary fs-5">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="col-12 border-top pt-3">
                                    <small class="text-muted fw-bold d-block mb-1">PAYMENT METHOD</small>
                                    <span class="fw-bold text-dark">{{ strtoupper($order->payment_method) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-3 d-sm-flex justify-content-center">
                            <a href="{{ route('website.orders.show', $order->id) }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                                <i class="mdi mdi-package-variant-closed me-2"></i>Track Order
                            </a>
                            <a href="{{ route('website.products.index') }}" class="btn btn-outline-secondary btn-lg px-5 rounded-pill fw-bold">
                                Continue Shopping
                            </a>
                        </div>
                        
                        <div class="mt-5 text-muted small">
                            <p class="mb-0">A confirmation email has been sent to <strong>{{ auth('customer')->user()->email }}</strong></p>
                            <p>Need help? <a href="{{ route('website.contact') }}" class="text-primary fw-bold">Contact Support</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-website-layout>
