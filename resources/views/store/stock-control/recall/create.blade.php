<x-app-layout title="Create Recall Request">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0"><i class="mdi mdi-plus-circle me-2"></i>Initiate Stock Return</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('store.stock-control.recall.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Product</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">-- Choose Product --</option>
                                @foreach($products as $stock)
                                    <option value="{{ $stock->product_id }}">
                                        {{ $stock->product->product_name }} (Qty: {{ $stock->quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Return Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reason</label>
                            <select name="reason" class="form-select" required>
                                <option value="expired">Expired / Near Expiry</option>
                                <option value="damaged">Damaged</option>
                                <option value="overstock">Overstock / Slow Moving</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Remarks</label>
                            <input type="text" name="reason_remarks" class="form-control" placeholder="Optional details...">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            Create Request
                        </button>
                        <a href="{{ route('store.stock-control.recall.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>