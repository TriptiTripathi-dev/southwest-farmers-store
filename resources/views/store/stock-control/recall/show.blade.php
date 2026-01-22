<x-app-layout title="Recall Request #{{ $recall->id }}">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-undo-variant text-warning me-2"></i> 
                Recall Request #{{ $recall->id }}
            </h4>
            <p class="text-muted mb-0">
                Product: {{ $recall->product->product_name }} (SKU: {{ $recall->product->sku ?? 'N/A' }})  
                | Requested: {{ $recall->requested_quantity }} units
            </p>
        </div>
        <a href="{{ route('store.stock-control.recall.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row g-4">
        <!-- Left Column: Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Request Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Status</span>
                            <span class="badge bg-{{ 
                                $recall->status == 'pending_store_approval' ? 'warning' : 
                                ($recall->status == 'completed' ? 'success' : 
                                ($recall->status == 'rejected_by_store' ? 'danger' : 'primary')) 
                            }} fs-6 px-3 py-2">
                                {{ ucwords(str_replace('_', ' ', $recall->status)) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Reason</span>
                            <strong>{{ ucwords(str_replace('_', ' ', $recall->reason)) }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Requested Qty</span>
                            <strong>{{ $recall->requested_quantity }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Approved Qty</span>
                            <strong>{{ $recall->approved_quantity ?? 'Pending' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Dispatched Qty</span>
                            <strong>{{ $recall->dispatched_quantity ?? 'Pending' }}</strong>
                        </li>
                        <li class="list-group-item">
                            <span class="d-block mb-1">Warehouse Remarks</span>
                            <small class="text-muted">{{ $recall->reason_remarks ?? 'None provided' }}</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column: Action Area -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Your Action Required</h5>
                </div>
                <div class="card-body">
                    @if($recall->status == 'pending_store_approval')
                        <div class="alert alert-warning mb-4">
                            <i class="mdi mdi-alert-outline me-2"></i>
                            Warehouse has requested recall. Please review and respond.
                        </div>

                        <form action="{{ route('store.stock-control.recall.approve', $recall) }}" method="POST" class="needs-validation mb-4" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Approved Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="approved_quantity" class="form-control" 
                                       min="1" max="{{ $recall->requested_quantity }}" required>
                                <div class="invalid-feedback">Enter valid quantity (1 to {{ $recall->requested_quantity }})</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Store Remarks (Optional)</label>
                                <textarea name="store_remarks" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="mdi mdi-check-circle me-1"></i> Approve
                                </button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    Reject
                                </button>
                            </div>
                        </form>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Reject Recall Request</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('store.stock-control.recall.reject', $recall) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                                                <textarea name="store_remarks" class="form-control" rows="4" required></textarea>
                                                <div class="invalid-feedback">Reason is required</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="mdi mdi-close-circle me-1"></i> Reject
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @elseif(in_array($recall->status, ['approved_by_store', 'partial_approved']))
                        <div class="alert alert-info mb-4">
                            <i class="mdi mdi-information-outline me-2"></i>
                            You approved {{ $recall->approved_quantity }} units. Now dispatch from your stock.
                        </div>

                        <form action="{{ route('store.stock-control.recall.dispatch', $recall) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Batches to Dispatch</label>
                                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered">
                                        <thead class="sticky-top bg-light">
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>Batch No</th>
                                                <th>Expiry Date</th>
                                                <th>Days Left</th>
                                                <th>Available Qty</th>
                                                <th>Dispatch Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($batches as $batch)
                                                @php
                                                    $daysLeft = $batch->expiry_date ? Carbon::parse($batch->expiry_date)->diffInDays(now(), false) : null;
                                                    $isExpired = $daysLeft !== null && $daysLeft <= 0;
                                                    $isNear = $daysLeft !== null && $daysLeft > 0 && $daysLeft <= 30;
                                                @endphp
                                                <tr class="{{ $isExpired ? 'table-danger' : ($isNear ? 'table-warning' : '') }}">
                                                    <td>
                                                        <input type="checkbox" name="batches[{{ $batch->id }}][selected]" class="batch-check">
                                                    </td>
                                                    <td>{{ $batch->batch_number }}</td>
                                                    <td>{{ $batch->expiry_date ? $batch->expiry_date->format('d M Y') : 'N/A' }}</td>
                                                    <td class="{{ $isExpired ? 'text-danger' : ($isNear ? 'text-warning' : '') }}">
                                                        {{ $daysLeft === null ? 'N/A' : ($daysLeft <= 0 ? 'Expired' : $daysLeft . ' days') }}
                                                    </td>
                                                    <td>{{ $batch->remaining_quantity }}</td>
                                                    <td>
                                                        <input type="number" name="batches[{{ $batch->id }}][quantity]" 
                                                               class="form-control form-control-sm batch-qty" 
                                                               min="0" max="{{ $batch->remaining_quantity }}" value="0" disabled>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center py-3">No available batches</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Total Dispatch Qty:</strong> 
                                    <span id="totalQty" class="fs-5 fw-bold text-primary">0</span>
                                </div>
                                <button type="submit" class="btn btn-success" id="dispatchBtn" disabled>
                                    <i class="mdi mdi-truck-fast me-1"></i> Dispatch Now
                                </button>
                            </div>
                        </form>

                    @elseif($recall->status == 'dispatched')
                        <div class="alert alert-success text-center py-5">
                            <i class="mdi mdi-check-circle-outline fs-1 d-block mb-3"></i>
                            <h5 class="mb-2">Dispatched Successfully</h5>
                            <p class="mb-0">You have dispatched {{ $recall->dispatched_quantity }} units for this recall.</p>
                        </div>

                    @else
                        <div class="alert alert-secondary text-center py-5">
                            No action required at this stage.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Batch selection logic
    $('.batch-check').on('change', function() {
        let qtyInput = $(this).closest('tr').find('.batch-qty');
        qtyInput.prop('disabled', !this.checked);
        if (!this.checked) qtyInput.val(0);
        updateTotal();
    });

    $('.batch-qty').on('input', function() {
        let val = parseInt($(this).val()) || 0;
        let max = parseInt($(this).attr('max')) || 0;
        if (val > max) $(this).val(max);
        if (val < 0) $(this).val(0);
        updateTotal();
    });

    $('#selectAll').on('change', function() {
        $('.batch-check').prop('checked', this.checked).trigger('change');
    });

    function updateTotal() {
        let total = 0;
        $('.batch-check:checked').each(function() {
            let qty = parseInt($(this).closest('tr').find('.batch-qty').val()) || 0;
            total += qty;
        });
        $('#totalQty').text(total);
        $('#dispatchBtn').prop('disabled', total === 0);
    }

    // Form validation
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
@endpush

@push('styles')
<style>
    .table-danger .batch-qty { background-color: #f8d7da; }
    .table-warning .batch-qty { background-color: #fff3cd; }
</style>
@endpush

</x-app-layout>