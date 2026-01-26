<x-app-layout title="Create Recall Request">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-plus-circle text-danger me-2"></i> Initiate Stock Return
            </h4>
            <small class="text-muted">Send items back to the warehouse</small>
        </div>
        <a href="{{ route('store.stock-control.recall.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form action="{{ route('store.stock-control.recall.store') }}" method="POST" id="recallForm">
                        @csrf

                        {{-- 1. Product Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="productSelect" class="form-select form-select-lg" required>
                                <option value="" selected disabled>-- Choose Product --</option>
                                @foreach($products as $stock)
                                    <option value="{{ $stock->product_id }}" 
                                            data-qty="{{ $stock->quantity }}"
                                            data-sku="{{ $stock->product->sku ?? 'N/A' }}"
                                            {{ (isset($selectedBatch) && $selectedBatch->product_id == $stock->product_id) ? 'selected' : '' }}>
                                        {{ $stock->product->product_name }} (Stock: {{ $stock->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            
                            {{-- Info Box --}}
                            <div id="stockInfo" class="alert alert-info mt-2 d-none">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Available Stock:</strong> <span id="displayQty">0</span></span>
                                    <span><strong>SKU:</strong> <span id="displaySku">-</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- 2. Quantity --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Return Quantity <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="quantity" id="quantityInput" 
                                           class="form-control form-control-lg" 
                                           min="1" required disabled>
                                    <span class="input-group-text">Units</span>
                                </div>
                                <div class="form-text text-danger d-none" id="qtyError">Quantity cannot exceed stock.</div>
                            </div>

                            {{-- 3. Reason --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Reason <span class="text-danger">*</span></label>
                                <select name="reason" class="form-select form-select-lg" required>
                                    <option value="expired">Expired / Near Expiry</option>
                                    <option value="damaged">Damaged Item</option>
                                    <option value="overstock">Overstock</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        {{-- 4. Remarks --}}
                        <div class="mb-4 mt-4">
                            <label class="form-label fw-bold">Remarks</label>
                            <textarea name="reason_remarks" class="form-control" rows="3" 
                                      placeholder="E.g., Batch #123 damaged in transit...">{{ isset($selectedBatch) ? 'Batch: '.$selectedBatch->batch_number : '' }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('productSelect');
        const quantityInput = document.getElementById('quantityInput');
        const stockInfo = document.getElementById('stockInfo');
        const displayQty = document.getElementById('displayQty');
        const displaySku = document.getElementById('displaySku');
        const qtyError = document.getElementById('qtyError');

        function updateStockInfo() {
            const selected = productSelect.options[productSelect.selectedIndex];
            const qty = selected.getAttribute('data-qty');
            const sku = selected.getAttribute('data-sku');

            if (qty) {
                stockInfo.classList.remove('d-none');
                displayQty.textContent = qty;
                displaySku.textContent = sku;
                quantityInput.disabled = false;
                quantityInput.max = qty;
            }
        }

        // Trigger on load if pre-selected
        if (productSelect.value) updateStockInfo();

        productSelect.addEventListener('change', updateStockInfo);

        quantityInput.addEventListener('input', function() {
            if (parseInt(this.value) > parseInt(this.max)) {
                this.classList.add('is-invalid');
                qtyError.classList.remove('d-none');
            } else {
                this.classList.remove('is-invalid');
                qtyError.classList.add('d-none');
            }
        });
    });
</script>
@endpush

</x-app-layout>