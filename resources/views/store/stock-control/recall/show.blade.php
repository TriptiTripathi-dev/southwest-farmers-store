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
                  
                   {{-- Case 1: Pending --}}
@if(in_array($recall->status, ['pending_warehouse_approval', 'pending_store_approval', 'pending']))
    <div class="text-center py-4">
        <div class="mb-3">
            <i class="mdi mdi-clock-outline text-warning display-1"></i>
        </div>
        <h4>Request Sent</h4>
        <p class="text-muted">
            Waiting for approval. You cannot dispatch stock yet.<br>
            Current Status: <strong>{{ ucwords(str_replace('_', ' ', $recall->status)) }}</strong>
        </p>
    </div>

{{-- Case 2: Approved (Show Dispatch Form) --}}
@elseif(in_array($recall->status, ['approved', 'approved_by_store', 'partial_approved']))
    
    <div class="alert alert-success mb-4">
        <i class="mdi mdi-check-circle me-2"></i>
        <strong>Approved!</strong> Please select batches below to dispatch {{ $recall->approved_quantity ?? $recall->requested_quantity }} units.
    </div>

    <form action="{{ route('store.stock-control.recall.dispatch', $recall) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="mb-4">
            <label class="form-label fw-bold">Select Batches to Dispatch</label>
            <div class="table-responsive border rounded" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="40" class="text-center"><input type="checkbox" id="selectAll"></th>
                            <th>Batch No</th>
                            <th>Expiry</th>
                            <th>Stock</th>
                            <th width="120">Dispatch Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="batches[{{ $batch->id }}][selected]" class="batch-check form-check-input">
                                    <input type="hidden" name="batches[{{ $batch->id }}][batch_id]" value="{{ $batch->id }}">
                                </td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $batch->quantity }}</span>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="batches[{{ $batch->id }}][quantity]" 
                                           class="form-control form-control-sm batch-qty" 
                                           min="0" 
                                           max="{{ $batch->quantity }}" 
                                           value="0" 
                                           disabled>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i> No available batches found in stock.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
            <div>
                <small class="text-muted d-block">Total Selected</small>
                <span id="totalQty" class="fs-4 fw-bold text-primary">0</span> <small class="text-muted">units</small>
            </div>
            <button type="submit" class="btn btn-primary" id="dispatchBtn" disabled>
                <i class="mdi mdi-truck-delivery me-1"></i> Dispatch Stock
            </button>
        </div>
    </form>

{{-- Case 3: Dispatched --}}
@elseif($recall->status == 'dispatched')
    <div class="text-center py-5">
        <i class="mdi mdi-truck-check text-success display-1 mb-3"></i>
        <h4 class="text-success">Dispatched Successfully</h4>
        <p class="text-muted">
            You sent <strong>{{ $recall->dispatched_quantity }}</strong> units on {{ $recall->updated_at->format('d M Y') }}.
        </p>
    </div>

{{-- Case 4: Rejected --}}
@elseif(str_contains($recall->status, 'rejected'))
    <div class="alert alert-danger">
        <h5 class="alert-heading"><i class="mdi mdi-close-circle me-1"></i> Request Rejected</h5>
        <p>{{ $recall->warehouse_remarks ?? $recall->store_remarks ?? 'No reason provided.' }}</p>
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