<x-app-layout title="Recall Request #{{ $recall->id }}">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-undo-variant text-warning me-2"></i> 
                Recall Request #{{ $recall->id }}
            </h4>
        </div>
        <a href="{{ route('store.stock-control.recall.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Request Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <small class="text-muted d-block">Product</small>
                            <strong>{{ $recall->product->product_name }}</strong>
                            <br><small>{{ $recall->product->sku }}</small>
                        </li>
                        <li class="list-group-item">
                            <small class="text-muted d-block">Requested Qty</small>
                            <span class="fs-5 fw-bold">{{ $recall->requested_quantity }}</span>
                        </li>
                        <li class="list-group-item">
                            <small class="text-muted d-block">Reason</small>
                            <span class="badge bg-light text-dark border">{{ $recall->reason }}</span>
                        </li>
                        <li class="list-group-item">
                            <small class="text-muted d-block">Current Status</small>
                            @if($recall->status == 'pending_warehouse_approval')
                                <span class="badge bg-warning text-dark">Waiting for Approval</span>
                            @elseif($recall->status == 'approved')
                                <span class="badge bg-primary">Approved - Ready to Dispatch</span>
                            @elseif($recall->status == 'dispatched')
                                <span class="badge bg-info text-white">Dispatched</span>
                            @elseif($recall->status == 'received')
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-secondary">{{ $recall->status }}</span>
                            @endif
                        </li>
                        @if($recall->warehouse_remarks)
                        <li class="list-group-item">
                            <small class="text-muted d-block">Warehouse Remarks</small>
                            <p class="mb-0 text-info">{{ $recall->warehouse_remarks }}</p>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Action</h5>
                </div>
                <div class="card-body p-4">

                    {{-- CASE 1: Pending Approval --}}
                    @if($recall->status == 'pending_warehouse_approval')
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="mdi mdi-clock-outline text-warning display-1"></i>
                            </div>
                            <h4>Request Sent to Warehouse</h4>
                            <p class="text-muted">
                                You cannot dispatch stock yet. <br>
                                Please wait for the Warehouse Admin to approve this request.
                            </p>
                        </div>

                    {{-- CASE 2: Approved (Ready to Dispatch) --}}
                    @elseif($recall->status == 'approved')
                        <div class="alert alert-success mb-4">
                            <i class="mdi mdi-check-circle me-2"></i> 
                            <strong>Good news!</strong> Warehouse has approved this request. You can now dispatch the stock.
                        </div>

                        <form action="{{ route('store.stock-control.recall.dispatch', $recall->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Confirm Dispatch Quantity</label>
                                <input type="number" name="dispatch_qty" 
                                       class="form-control form-control-lg" 
                                       value="{{ $recall->requested_quantity }}" 
                                       max="{{ $recall->requested_quantity }}" min="1">
                                <div class="form-text">Ensure you physically pack this amount.</div>
                            </div>

                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="mdi mdi-truck-delivery me-2"></i> Dispatch Stock Now
                            </button>
                        </form>

                    {{-- CASE 3: Dispatched --}}
                    @elseif($recall->status == 'dispatched')
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="mdi mdi-truck-check text-success display-1"></i>
                            </div>
                            <h4 class="text-success">Stock Dispatched</h4>
                            <p class="text-muted">
                                You have successfully processed this recall. <br>
                                <strong>Dispatched Qty:</strong> {{ $recall->dispatched_quantity }}
                            </p>
                        </div>
                    
                    {{-- CASE 4: Rejected --}}
                    @elseif($recall->status == 'rejected')
                        <div class="alert alert-danger">
                            <i class="mdi mdi-close-circle me-2"></i> This request was rejected by Warehouse.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>