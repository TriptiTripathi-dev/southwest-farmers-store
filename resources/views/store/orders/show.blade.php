<x-app-layout title="Order Details">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-information-outline text-primary me-2"></i> Order Details: {{ $order->po_number }}
            </h4>
            <div class="mt-1">
                <span class="badge bg-soft-secondary text-secondary me-2">Created: {{ $order->created_at->format('d M Y, h:i A') }}</span>
                @php
                    $statusClass = match($order->status) {
                        'pending' => 'bg-warning text-dark',
                        'approved' => 'bg-info text-white',
                        'dispatched' => 'bg-primary text-white',
                        'completed' => 'bg-success text-white',
                        'cancelled' => 'bg-danger text-white',
                        default => 'bg-secondary text-white'
                    };
                @endphp
                <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('store.orders.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back to List
            </a>
            @if($order->status === 'dispatched')
                <a href="{{ route('store.orders.receive', $order->id) }}" class="btn btn-success shadow-sm">
                    <i class="mdi mdi-truck-check me-1"></i> Confirm Receipt
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold">Order Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th class="text-center">Requested Qty</th>
                                    <th class="text-center">Dispatched Qty</th>
                                    <th class="text-center">Received Qty</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end pe-3">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold">{{ $item->product->product_name }}</div>
                                            <small class="text-muted">{{ $item->product->sku }}</small>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">{{ $item->dispatched_quantity ?: '-' }}</td>
                                        <td class="text-center">{{ $item->received_quantity ?: '-' }}</td>
                                        <td class="text-end">₹{{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end pe-3 fw-bold">₹{{ number_format($item->total_cost, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold ps-3 py-3">Grand Total:</td>
                                    <td class="text-end pe-3 fw-bold py-3 text-primary h5 mb-0">₹{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($order->warehouse_remarks || $order->store_remarks)
                <div class="row">
                    @if($order->store_remarks)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-bold">Store Remarks</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0 text-muted">{{ $order->store_remarks }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($order->warehouse_remarks)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-bold">Warehouse Remarks</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0 text-muted">{{ $order->warehouse_remarks }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">Timeline</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 position-relative timeline">
                        <li class="mb-4 ps-4 position-relative">
                            <div class="timeline-dot bg-success"></div>
                            <small class="text-muted d-block">Requested At</small>
                            <span class="fw-bold">{{ $order->created_at->format('d M, h:i A') }}</span>
                        </li>
                        <li class="mb-4 ps-4 position-relative">
                            <div class="timeline-dot {{ $order->approved_at ? 'bg-success' : 'bg-light' }}"></div>
                            <small class="text-muted d-block">Approved At</small>
                            <span class="fw-bold">{{ $order->approved_at ? $order->approved_at->format('d M, h:i A') : 'Pending' }}</span>
                        </li>
                        <li class="mb-4 ps-4 position-relative">
                            <div class="timeline-dot {{ $order->dispatched_at ? 'bg-success' : 'bg-light' }}"></div>
                            <small class="text-muted d-block">Dispatched At</small>
                            <span class="fw-bold">{{ $order->dispatched_at ? $order->dispatched_at->format('d M, h:i A') : 'Pending' }}</span>
                        </li>
                        <li class="ps-4 position-relative">
                            <div class="timeline-dot {{ $order->received_at ? 'bg-success' : 'bg-light' }}"></div>
                            <small class="text-muted d-block">Completed At</small>
                            <span class="fw-bold">{{ $order->received_at ? $order->received_at->format('d M, h:i A') : 'Pending' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
    .timeline::before {
        content: '';
        position: absolute;
        left: 4px;
        top: 10px;
        bottom: 10px;
        width: 1px;
        background: #e9ecef;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 6px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #f8f9fa;
        z-index: 1;
    }
</style>
@endpush

</x-app-layout>
