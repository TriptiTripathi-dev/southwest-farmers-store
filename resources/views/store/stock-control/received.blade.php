<x-app-layout title="Pending Received">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-truck-delivery text-success me-2"></i> Pending Received
            </h4>
            <small class="text-muted">Confirm stock received from warehouse</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Request ID</th>
                        <th>Product</th>
                        <th>Dispatched Qty</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $req)
                        <tr>
                            <td>#{{ $req->id }}</td>
                            <td>{{ $req->product->product_name }}</td>
                            <td>{{ $req->dispatched_quantity }}</td>
                            <td>{{ $req->updated_at->format('d M Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#receiveModal{{ $req->id }}">
                                    Confirm Receipt
                                </button>
                            </td>
                        </tr>

                        <!-- Receive Modal -->
                        <div class="modal fade" id="receiveModal{{ $req->id }}">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Confirm Receipt - #{{ $req->id }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('store.stock-control.received.confirm', $req->id) }}" method="POST" class="needs-validation" novalidate>
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Received Quantity <span class="text-danger">*</span></label>
                                                <input type="number" name="received_quantity" class="form-control" 
                                                       value="{{ $req->dispatched_quantity }}" min="1" 
                                                       max="{{ $req->dispatched_quantity }}" required>
                                                <div class="invalid-feedback">Enter valid quantity (1 to {{ $req->dispatched_quantity }})</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Remarks</label>
                                                <textarea name="remarks" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-check-circle me-1"></i> Confirm
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="mdi mdi-information-outline fs-3 d-block mb-2"></i>
                                No pending received stock
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $pending->links() }}
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