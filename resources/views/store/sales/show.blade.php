<x-app-layout title="Order Details #{{ $sale->invoice_number }}">
    <div class="container-fluid py-4" style="background-color: #f8fafc;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small">
                        <li class="breadcrumb-item"><a href="{{ route('store.sales.orders') }}" class="text-decoration-none text-success fw-bold">Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $sale->invoice_number }}</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-black text-dark" style="letter-spacing: -0.5px;">Order #{{ $sale->invoice_number }}</h3>
                <span class="text-muted small">Placed on {{ $sale->created_at->format('M d, Y, h:i A') }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('store.sales.orders') }}" class="btn btn-white border rounded-pill px-3 fw-bold text-muted hover-shadow-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('store.sales.orders.invoice', $sale->id) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="mdi mdi-file-pdf-box me-1"></i> Print Invoice
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content: Left Column -->
            <div class="col-lg-8">
                <!-- Items Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-shopping-outline text-success me-2 fs-4"></i> Items Ordered
                        </h5>
                        <span class="badge bg-light text-muted border rounded-pill px-3">{{ $sale->items->count() }} items</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted fw-bold small">
                                <tr>
                                    <th class="ps-4 py-3">Product</th>
                                    <th class="text-center py-3">Price</th>
                                    <th class="text-center py-3">Quantity</th>
                                    <th class="text-end pe-4 py-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->items as $item)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                @if ($item->product->image)
                                                    <img src="{{ Storage::disk('r2')->url($item->product->image) }}"
                                                         class="rounded-3 border me-3" width="50" height="50"
                                                         style="object-fit: contain; background-color: #f8fafc;">
                                                @else
                                                    <div class="rounded-3 border me-3 bg-light d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="mdi mdi-image text-muted fs-4"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong class="text-dark d-block" style="font-size: 0.95rem;">{{ $item->product->product_name }}</strong>
                                                    <span class="text-muted small">UPC: {{ $item->product->upc ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold text-muted">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end pe-4 fw-black text-dark">
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="text-muted text-uppercase fw-bold small mb-2 d-flex align-items-center">
                            <i class="mdi mdi-note-text-outline me-1 fs-5"></i> Order Notes / Payment Remarks
                        </h6>
                        <div class="p-3 bg-light rounded-3 text-dark font-monospace" style="font-size: 0.9rem; line-height: 1.6;">
                            {{ $sale->notes ?? 'No notes or payment codes associated with this order.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Content: Right Column -->
            <div class="col-lg-4">
                <!-- Customer Details -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                            <i class="mdi mdi-account-circle-outline text-success me-2 fs-4"></i> Customer Details
                        </h5>
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 text-uppercase fw-black fs-4" style="width: 54px; height: 54px; min-width: 54px;">
                                {{ substr($sale->customer->name ?? 'W', 0, 1) }}
                            </div>
                            <div>
                                <h5 class="mb-1 text-dark fw-bold">{{ $sale->customer->name ?? 'Walk-in Customer' }}</h5>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold py-1 px-2.5 small">{{ $sale->customer->party_type ?? 'Retail' }}</span>
                            </div>
                        </div>
                        <div class="space-y-3 pt-3 border-top">
                            <div class="d-flex align-items-center gap-3 text-dark small mb-2.5">
                                <i class="mdi mdi-phone-outline text-muted fs-5"></i>
                                <span>{{ $sale->customer->phone ?? 'No phone number' }}</span>
                            </div>
                            @if (optional($sale->customer)->email)
                                <div class="d-flex align-items-center gap-3 text-dark small mb-2.5">
                                    <i class="mdi mdi-email-outline text-muted fs-5"></i>
                                    <span>{{ $sale->customer->email }}</span>
                                </div>
                            @endif
                            @if (optional($sale->customer)->address)
                                <div class="d-flex align-items-start gap-3 text-dark small">
                                    <i class="mdi mdi-map-marker-outline text-muted fs-5 mt-0.5"></i>
                                    <span>{{ $sale->customer->address }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                            <i class="mdi mdi-receipt-text-outline text-success me-2 fs-4"></i> Order Summary
                        </h5>

                        <div class="space-y-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center py-2.5 border-bottom border-light">
                                <span class="text-muted">Order reference</span>
                                <span class="fw-bold text-dark">#{{ $sale->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2.5 border-bottom border-light">
                                <span class="text-muted">Source</span>
                                <span class="badge bg-{{ ($sale->source ?? 'pos') === 'website' ? 'info' : 'secondary' }} bg-opacity-10 text-{{ ($sale->source ?? 'pos') === 'website' ? 'info' : 'secondary' }} text-uppercase px-3 py-1.5 rounded-pill fw-bold">
                                    {{ $sale->source ?? 'POS' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2.5 border-bottom border-light">
                                <span class="text-muted">Fulfillment Status</span>
                                @php
                                    $statusClass = match($sale->status ?? 'paid') {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed', 'paid' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} text-uppercase px-3 py-1.5 rounded-pill fw-bold">{{ $sale->status ?? 'PAID' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2.5 border-bottom border-light">
                                <span class="text-muted">Payment Type</span>
                                <span class="badge bg-success bg-opacity-10 text-success text-uppercase px-3 py-1.5 rounded-pill fw-bold">{{ $sale->payment_method }}</span>
                            </div>
                        </div>

                        <!-- Status Update Dropdown for Web Orders -->
                        @if(($sale->source ?? 'pos') === 'website')
                            <form action="{{ route('store.sales.orders.update-status', $sale->id) }}" method="POST" class="mb-4 bg-light p-3 rounded-3 border">
                                @csrf
                                @method('PATCH')
                                <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Fulfillment Status Control</label>
                                <div class="input-group">
                                    <select name="status" class="form-select form-select-sm fw-bold">
                                        <option value="pending" {{ $sale->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $sale->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ $sale->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $sale->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success px-3 fw-bold">Update</button>
                                </div>
                            </form>
                        @endif

                        <!-- Pricing Breakdown -->
                        <div class="bg-light rounded-3 p-3">
                            <div class="d-flex justify-content-between mb-2 small font-monospace">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold text-dark">${{ number_format($sale->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small font-monospace">
                                <span class="text-muted">Tax / GST:</span>
                                <span class="fw-bold text-dark">${{ number_format($sale->tax_amount ?? $sale->gst_amount ?? 0, 2) }}</span>
                            </div>
                            @if($sale->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 small font-monospace text-success">
                                    <span>Discount:</span>
                                    <span class="fw-bold">-${{ number_format($sale->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between border-top border-secondary border-opacity-10 pt-2 mt-2 align-items-center">
                                <span class="fw-bold text-dark">Grand Total:</span>
                                <span class="fw-black text-success fs-4">${{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
