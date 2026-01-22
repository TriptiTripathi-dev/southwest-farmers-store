<x-app-layout title="Request Stock">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-cart-plus text-primary me-2"></i> Request Stock
            </h4>
            <small class="text-muted">Request new stock from warehouse</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRequestModal">
            <i class="mdi mdi-plus me-1"></i> New Request
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Requested Qty</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>#{{ $request->id }}</td>
                            <td>{{ $request->product->product_name }}</td>
                            <td>{{ $request->requested_quantity }}</td>
                            <td>
                                <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'completed' ? 'success' : 'danger') }}">
                                    {{ ucwords($request->status) }}
                                </span>
                            </td>
                            <td>{{ ucwords($request->reason) }}</td>
                            <td>{{ $request->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No stock requests found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $requests->links() }}
        </div>
    </div>

    <!-- New Request Modal -->
    <div class="modal fade" id="newRequestModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">New Stock Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('store.stock-control.requests.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    @foreach(\App\Models\Product::where('is_active', true)->get() as $p)
                                        <option value="{{ $p->id }}">{{ $p->product_name }} ({{ $p->sku ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Select a product</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" min="1" required>
                                <div class="invalid-feedback">Enter quantity</div>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <select name="reason" class="form-select" required>
                                <option value="">Select Reason</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="urgent_sale">Urgent Sale</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback">Select reason</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-send me-1"></i> Send Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
</script>
@endpush

</x-app-layout>