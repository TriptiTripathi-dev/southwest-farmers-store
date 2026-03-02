@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            <form action="{{ route('store.orders.update', $order->id) }}" method="POST">
                @csrf
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Edit PO #{{ $order->po_number }}</h4>
                        <p class="text-muted small mb-0 mt-1">Status: <span
                                class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('store.orders.show', $order->id) }}"
                            class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">Save
                            Changes</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom p-3">
                                <h6 class="fw-bold m-0"><i class="mdi mdi-format-list-bulleted me-2"></i>Order Items</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3 text-muted small fw-bold">PRODUCT</th>
                                                <th class="py-3 text-muted small fw-bold">UNIT COST</th>
                                                <th class="py-3 text-center text-muted small fw-bold" style="width: 150px;">
                                                    QUANTITY</th>
                                                <th class="pe-4 py-3 text-end text-muted small fw-bold">SUBTOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->items as $index => $item)
                                                <tr>
                                                    <td class="ps-4 py-3">
                                                        <input type="hidden" name="items[{{ $index }}][id]"
                                                            value="{{ $item->id }}">
                                                        <div class="fw-bold text-dark">{{ $item->product->product_name }}
                                                        </div>
                                                        <div class="text-muted small font-monospace">UPC:
                                                            {{ $item->product->upc ?? 'N/A' }}</div>
                                                    </td>
                                                    <td>₹{{ number_format($item->unit_cost, 2) }}</td>
                                                    <td class="text-center">
                                                        <input type="number" name="items[{{ $index }}][quantity]"
                                                            class="form-control form-control-sm text-center fw-bold rounded-3 border-2 item-qty"
                                                            value="{{ $item->quantity }}" min="1"
                                                            data-cost="{{ $item->unit_cost }}">
                                                    </td>
                                                    <td class="pe-4 text-end fw-bold">₹<span
                                                            class="item-subtotal">{{ number_format($item->quantity * $item->unit_cost, 2) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-light fw-bold text-dark">
                                            <tr>
                                                <td colspan="3" class="ps-4 py-3 text-end">Grand Total</td>
                                                <td class="pe-4 text-end fs-5">₹<span
                                                        id="grandTotal">{{ number_format($order->total_amount, 2) }}</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom p-3">
                                <h6 class="fw-bold m-0 text-primary"><i class="mdi mdi-shield-check me-2"></i>Manager
                                    Approval Required</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Mandatory Reason for Edit</label>
                                    <textarea name="edit_reason" class="form-control bg-light border-0 rounded-3" rows="4"
                                        placeholder="Explain why these quantities are being adjusted (e.g., manager overridden for upcoming event)..."
                                        required></textarea>
                                    <div class="form-text small mt-2">
                                        <i class="mdi mdi-information-outline me-1"></i> These changes will be logged for
                                        audit purposes.
                                    </div>
                                </div>

                                <div class="alert alert-warning border-0 rounded-3 mb-0 small">
                                    <i class="mdi mdi-alert-circle-outline me-2"></i> Quantity edits after initial
                                    generation are restricted to manager-level authorization and require an audit trail.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.item-qty').on('input change', function() {
                    let qty = parseInt($(this).val()) || 0;
                    let cost = parseFloat($(this).data('cost')) || 0;
                    let subtotal = qty * cost;

                    $(this).closest('tr').find('.item-subtotal').text(subtotal.toLocaleString('en-IN', {
                        minimumFractionDigits: 2
                    }));

                    calculateGrandTotal();
                });

                function calculateGrandTotal() {
                    let total = 0;
                    $('.item-qty').each(function() {
                        let qty = parseInt($(this).val()) || 0;
                        let cost = parseFloat($(this).data('cost')) || 0;
                        total += qty * cost;
                    });
                    $('#grandTotal').text(total.toLocaleString('en-IN', {
                        minimumFractionDigits: 2
                    }));
                }
            });
        </script>
    @endpush
@endsection
