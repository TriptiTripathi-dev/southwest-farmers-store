<x-app-layout title="Purchase Order Details">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-file-document text-primary me-2"></i> Purchase Order Details
            </h4>
            <small class="text-muted">Viewing details for {{ $request->request_number }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('store.stock-control.requests') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back to List
            </a>
            @if($request->status == 'pending')
                <button type="button" class="btn btn-danger" onclick="cancelPO({{ $request->id }})">
                    <i class="mdi mdi-close me-1"></i> Cancel PO
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Order Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3" style="width: 50px;">#</th>
                                    <th>Product</th>
                                    <th class="text-center">Requested Qty</th>
                                    @if($request->status != 'pending')
                                        <th class="text-center">Dispatched</th>
                                        <th class="text-center">Received</th>
                                    @endif
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end pe-3">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->items as $index => $item)
                                    <tr>
                                        <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->product->product_name }}</div>
                                            <small class="text-muted font-monospace">{{ $item->product->sku }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3">
                                                {{ $item->quantity }}
                                            </span>
                                        </td>
                                        @if($request->status != 'pending')
                                            <td class="text-center">
                                                <span class="badge bg-info bg-opacity-10 text-info px-3">
                                                    {{ $item->dispatched_quantity ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $item->received_quantity >= ($item->dispatched_quantity ?? 0) ? 'bg-success' : 'bg-warning' }} bg-opacity-10 {{ $item->received_quantity >= ($item->dispatched_quantity ?? 0) ? 'text-success' : 'text-warning' }} px-3">
                                                    {{ $item->received_quantity ?? 0 }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="text-end text-muted">₹{{ number_format($item->unit_cost, 2) }}</td>
                                        <td class="text-end fw-bold pe-3">₹{{ number_format($item->total_cost, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="{{ $request->status == 'pending' ? 4 : 6 }}" class="text-end fw-bold py-3">Total Amount:</td>
                                    <td class="text-end fw-bold text-success fs-5 py-3 pe-3">₹{{ number_format($request->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($request->store_remarks)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Store Remarks</h6>
                    <p class="mb-0 text-muted">{{ $request->store_remarks }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small text-muted d-block">Status</label>
                        @if($request->status == 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                <i class="mdi mdi-clock-outline me-1"></i> Pending Approval
                            </span>
                        @elseif($request->status == 'dispatched')
                            <span class="badge bg-info px-3 py-2 fs-6">
                                <i class="mdi mdi-truck-delivery me-1"></i> Dispatched
                            </span>
                        @elseif($request->status == 'completed')
                            <span class="badge bg-success px-3 py-2 fs-6">
                                <i class="mdi mdi-check-circle me-1"></i> Completed
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2 fs-6">
                                <i class="mdi mdi-close-circle me-1"></i> Rejected
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block">Request Date</label>
                        <span class="fw-bold">{{ $request->created_at->format('d M Y, h:i A') }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block">Requested By</label>
                        <span class="fw-bold">{{ $request->requestedBy->name ?? 'N/A' }}</span>
                    </div>

                    @if($request->approvedBy)
                    <hr>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Approved By</label>
                        <span class="fw-bold">{{ $request->approvedBy->name }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block">Approved At</label>
                        <span class="fw-bold">{{ $request->approved_at ? $request->approved_at->format('d M Y, h:i A') : 'N/A' }}</span>
                    </div>
                    @endif

                    @if($request->warehouse_remarks)
                    <hr>
                    <div class="mb-0">
                        <label class="small text-muted d-block">Warehouse Remarks</label>
                        <span class="text-danger small">{{ $request->warehouse_remarks }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($request->status == 'dispatched')
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 border-success border-opacity-25">
                <div class="card-body text-center">
                    <h6 class="fw-bold text-success mb-3">Order is Dispatched!</h6>
                    <p class="small text-success mb-3">Please verify the items received and confirm receipt to update your stock.</p>
                    <a href="{{ route('store.stock-control.received') }}" class="btn btn-success w-100">
                        <i class="mdi mdi-check-circle me-1"></i> Confirm Receipt
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelPO(id) {
    Swal.fire({
        title: 'Cancel Purchase Order?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, cancel it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/store/stock-control/requests/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Cancelled!', data.message, 'success').then(() => {
                        window.location.href = "{{ route('store.stock-control.requests') }}";
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to cancel PO', 'error');
                }
            });
        }
    })
}
</script>
@endpush

</x-app-layout>
