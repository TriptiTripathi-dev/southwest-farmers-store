<x-app-layout title="Confirm Receipt">

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
            <div>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-truck-check text-success me-2"></i> Confirm Receipt
                </h4>
                <small class="text-muted">Confirm quantities received for Order #{{ $order->po_number }}</small>
            </div>
            <a href="{{ route('store.orders.show', $order->id) }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
        </div>

        <form action="{{ route('store.orders.confirm-receive', $order->id) }}" method="POST" id="receiveForm"
            class="needs-validation" novalidate>
            @csrf

            <div class="row">
                <div class="col-md-9">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-success text-white py-3">
                            <h6 class="mb-0"><i class="mdi mdi-format-list-bulleted me-2"></i>Order Items</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">Product</th>
                                            <th class="text-center">Requested</th>
                                            <th class="text-center">Dispatched</th>
                                            <th class="text-center" style="width: 150px;">Received Qty</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $index => $item)
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="fw-bold">{{ $item->product->product_name }}</div>
                                                    <small
                                                        class="text-muted font-monospace">{{ $item->product->upc ?? 'N/A' }}</small>
                                                    <input type="hidden" name="items[{{ $index }}][id]"
                                                        value="{{ $item->id }}">
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary">{{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-primary bg-opacity-10 text-primary">{{ $item->dispatched_quantity ?: $item->quantity }}</span>
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        name="items[{{ $index }}][received_quantity]"
                                                        class="form-control form-control-sm text-center received-input"
                                                        value="{{ $item->dispatched_quantity ?: $item->quantity }}"
                                                        min="0"
                                                        max="{{ $item->dispatched_quantity ?: $item->quantity }}"
                                                        data-index="{{ $index }}" required>
                                                </td>
                                                <td class="text-center">
                                                    <div id="status-{{ $index }}">
                                                        <span
                                                            class="badge bg-success bg-opacity-10 text-success">Matching</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <label class="form-label fw-bold">Receipt Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control" rows="3"
                                placeholder="Add any notes about discrepancies, damaged items, etc..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-dark">Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Items:</span>
                                <span class="fw-bold text-dark">{{ $order->items->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Total Amount:</span>
                                <span class="fw-bold text-success">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-success w-100 btn-lg shadow-sm">
                                <i class="mdi mdi-check-all me-1"></i> Update Inventory
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm small">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Inventory will be updated immediately upon confirmation. Transaction logs will be created for
                        each item.
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.received-input').on('input', function() {
                    const index = $(this).data('index');
                    const max = parseInt($(this).attr('max'));
                    const val = parseInt($(this).val()) || 0;
                    const statusDiv = $(`#status-${index}`);

                    if (val === max) {
                        statusDiv.html(
                            '<span class="badge bg-success bg-opacity-10 text-success">Matching</span>');
                    } else if (val < max && val > 0) {
                        statusDiv.html(
                            '<span class="badge bg-warning bg-opacity-10 text-warning">Partial</span>');
                    } else if (val === 0) {
                        statusDiv.html(
                            '<span class="badge bg-danger bg-opacity-10 text-danger">Missing</span>');
                    } else if (val > max) {
                        $(this).val(max);
                        statusDiv.html(
                            '<span class="badge bg-success bg-opacity-10 text-success">Matching</span>');
                    }
                });

                $('#receiveForm').on('submit', function(e) {
                    let hasShortage = false;
                    $('.received-input').each(function() {
                        const max = parseInt($(this).attr('max'));
                        const val = parseInt($(this).val()) || 0;
                        if (val < max) hasShortage = true;
                    });

                    if (hasShortage) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Confirm Shortage?',
                            text: "You have entered a received quantity less than what was dispatched. Inventory will only be updated for quantity received.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Confirm Receipt'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>
