<x-app-layout title="Order Details #{{ $sale->invoice_number }}">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('store.sales.orders') }}">Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $sale->invoice_number }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark">Order Details</h4>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('store.sales.orders') }}" class="btn btn-light border">
                    <i class="mdi mdi-arrow-left me-1"></i> Back
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="mdi mdi-printer me-1"></i> Print Invoice
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-uppercase text-muted small">Items Ordered</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/'.$item->product->image) }}" class="rounded me-3" width="40" height="40" style="object-fit:cover;">
                                            @else
                                                <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                                    <i class="mdi mdi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="fw-bold text-dark d-block">{{ $item->product->product_name }}</span>
                                                <small class="text-muted">{{ $item->product->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold">
                                        ${{ number_format($item->price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold">Order Note</small>
                        <p class="mb-0 mt-1 text-dark">{{ $sale->notes ?? 'No notes added for this order.' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Customer Details</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-md bg-soft-primary rounded-circle d-flex align-items-center justify-content-center me-3 text-primary fw-bold fs-4">
                                {{ substr($sale->customer->name ?? 'W', 0, 1) }}
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">{{ $sale->customer->name ?? 'Walk-in Customer' }}</h5>
                                <small class="text-muted">{{ $sale->customer->phone ?? 'No Phone' }}</small>
                            </div>
                        </div>
                        @if(optional($sale->customer)->email)
                        <div class="d-flex align-items-center gap-2 text-muted mb-2">
                            <i class="mdi mdi-email-outline"></i> {{ $sale->customer->email }}
                        </div>
                        @endif
                        @if(optional($sale->customer)->address)
                        <div class="d-flex align-items-center gap-2 text-muted">
                            <i class="mdi mdi-map-marker-outline"></i> {{ $sale->customer->address }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Order Summary</h6>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Order ID</span>
                            <span class="fw-bold text-dark">#{{ $sale->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Date</span>
                            <span class="fw-bold text-dark">{{ $sale->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Payment</span>
                            <span class="badge bg-success bg-opacity-10 text-success text-uppercase">{{ $sale->payment_method }}</span>
                        </div>

                        <hr class="border-dashed">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">${{ number_format($sale->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax / GST</span>
                            <span class="fw-bold">${{ number_format($sale->tax_amount ?? $sale->gst_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Discount</span>
                            <span class="fw-bold text-danger">-${{ number_format($sale->discount_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-3">
                            <h5 class="fw-bold text-dark">Total</h5>
                            <h5 class="fw-bold text-primary">${{ number_format($sale->total_amount, 2) }}</h5>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>