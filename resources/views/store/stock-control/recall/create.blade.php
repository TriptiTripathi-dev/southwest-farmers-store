<x-app-layout title="Create Recall Request">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div><h4 class="fw-bold mb-0 text-dark"><i class="mdi mdi-plus-circle text-danger me-2"></i> Initiate Stock Return</h4></div>
        <a href="{{ route('store.stock-control.recall.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm"><div class="card-body p-4">
                <form action="{{ route('store.stock-control.recall.store') }}" method="POST">
                    @csrf
                    @if(isset($selectedBatch))
                        <input type="hidden" name="batch_id" value="{{ $selectedBatch->id }}">
                        <div class="alert alert-warning">
                            <strong>Recalling Specific Batch:</strong> {{ $selectedBatch->batch_number }} (Expiry: {{ $selectedBatch->expiry_date }})
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="productSelect" class="form-select" required>
                            <option value="">-- Choose Product --</option>
                            @foreach($products as $stock)
                                <option value="{{ $stock->product_id }}" data-qty="{{ $stock->quantity }}" 
                                    {{ (isset($selectedBatch) && $selectedBatch->product_id == $stock->product_id) ? 'selected' : '' }}>
                                    {{ $stock->product->product_name }} (Total Stock: {{ $stock->quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Quantity</label>
                            <input type="number" name="quantity" id="quantityInput" class="form-control" min="1" required>
                            <div class="form-text">Max: <span id="displayQty">0</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Reason</label>
                            <select name="reason" class="form-select" required>
                                <option value="expired">Expired</option>
                                <option value="damaged">Damaged</option>
                                <option value="overstock">Overstock</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 mt-4">
                        <label class="form-label fw-bold">Remarks</label>
                        <textarea name="reason_remarks" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Submit Request</button>
                </form>
            </div></div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('productSelect').addEventListener('change', function() {
        let opt = this.options[this.selectedIndex];
        let qty = opt.getAttribute('data-qty');
        if(qty) {
            document.getElementById('displayQty').innerText = qty;
            let input = document.getElementById('quantityInput');
            input.max = qty;
            input.disabled = false;
        }
    });
    // Trigger change if pre-selected
    if(document.getElementById('productSelect').value) document.getElementById('productSelect').dispatchEvent(new Event('change'));
</script>
@endpush
</x-app-layout>