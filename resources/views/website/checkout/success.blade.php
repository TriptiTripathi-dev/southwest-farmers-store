<x-website-layout title="Order Successful | Southwest Farmers">
    <div class="py-5" style="min-height: 80vh; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Success Header Card -->
                    <div class="bg-white rounded-5 shadow-lg p-5 border-0 text-center mb-4">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 100px; height: 100px; box-shadow: 0 10px 20px rgba(0, 154, 54, 0.3);">
                                <i class="mdi mdi-check-decagram display-3"></i>
                            </div>
                        </div>
                        
                        <h1 class="display-5 fw-black text-dark mb-3">Payment Successful!</h1>
                        <p class="fs-5 text-muted mb-4">Thank you for your purchase. Your payment has been processed successfully.</p>
                        
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="{{ route('website.home') }}" class="btn btn-success btn-lg px-4 rounded-pill fw-bold">
                                <i class="mdi mdi-home-outline me-2"></i>Return to Home
                            </a>
                            <a href="{{ route('website.orders.show', $order->id) }}" class="btn btn-primary btn-lg px-4 rounded-pill fw-bold">
                                <i class="mdi mdi-package-variant-closed me-2"></i>Track Order
                            </a>
                            <a href="{{ route('website.products.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill fw-bold">
                                Continue Shopping
                            </a>
                        </div>
                    </div>

                    <!-- Order Details Card -->
                    <div class="bg-white rounded-5 shadow-lg p-5 border-0">
                        <h4 class="fw-black text-dark mb-4 pb-2 border-bottom">Order Information</h4>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-sm-6">
                                <span class="text-muted small fw-bold d-block text-uppercase">Order Number</span>
                                <strong class="fs-5 text-dark">#{{ $order->invoice_number }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small fw-bold d-block text-uppercase">Order Date</span>
                                <strong class="fs-5 text-dark">{{ $order->created_at->format('M d, Y, h:i A') }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small fw-bold d-block text-uppercase">Payment Method</span>
                                <strong class="fs-5 text-dark">{{ strtoupper($order->payment_method) }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small fw-bold d-block text-uppercase">Transaction ID</span>
                                <strong class="fs-5 text-primary">{{ $order->transaction_id ?? 'N/A' }}</strong>
                            </div>
                        </div>

                        <h5 class="fw-bold text-dark mb-3">Items Purchased</h5>
                        <div class="table-responsive mb-4">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small fw-bold">
                                        <th scope="col" style="min-width: 200px;">Product</th>
                                        <th scope="col" class="text-center">Quantity</th>
                                        <th scope="col" class="text-end">Price</th>
                                        <th scope="col" class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $item->product->image ? Storage::disk('r2')->url($item->product->image) : 'https://placehold.co/50x50/ecfdf5/10b981?text=' . urlencode($item->product->product_name) }}" 
                                                         class="rounded-3 border" 
                                                         style="width: 50px; height: 50px; object-fit: contain; background: #f8fafc;">
                                                    <div>
                                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">{{ $item->product->product_name }}</h6>
                                                        <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-dark">{{ $item->quantity }}</td>
                                            <td class="text-end text-muted">${{ number_format($item->price, 2) }}</td>
                                            <td class="text-end fw-bold text-dark">${{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Block -->
                        <div class="bg-light rounded-4 p-4 border ms-auto" style="max-width: 400px;">
                            <div class="d-flex justify-content-between text-muted fw-bold small mb-2">
                                <span>Subtotal:</span>
                                <span class="text-dark">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between text-success fw-bold small mb-2">
                                    <span>Discount:</span>
                                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between text-muted fw-bold small mb-3">
                                <span>Shipping:</span>
                                <span class="text-success">FREE</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <span class="fw-black text-dark text-uppercase">Total Paid:</span>
                                <span class="fw-black text-primary fs-3">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-5 text-center text-muted small">
                            <p class="mb-1">A confirmation email has been sent to <strong>{{ auth('customer')->user()->email }}</strong></p>
                            <p class="mb-0">Need help? <a href="{{ route('website.contact') }}" class="text-primary fw-bold">Contact Support</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-website-layout>
