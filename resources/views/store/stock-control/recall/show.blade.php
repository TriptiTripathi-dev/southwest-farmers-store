<x-app-layout title="Recall Request #{{ $recall->id }}">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Recall Request #{{ $recall->id }}</h4>
                <p class="mb-0 text-muted">Status: <span class="badge bg-primary">{{ ucwords($recall->status) }}</span></p>
            </div>
            <div class="d-flex gap-2">
                @if(in_array($recall->status, ['dispatched', 'received', 'completed']))
                <a href="{{ route('store.stock-control.recall.challan', $recall->id) }}" class="btn btn-outline-dark"><i class="mdi mdi-printer me-1"></i> Print Challan</a>
                @endif
                <a href="{{ route('store.stock-control.recall.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5>Summary</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span>Product</span> <strong>{{ $recall->product->product_name }}</strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span>Requested Qty</span> <strong>{{ $recall->requested_quantity }}</strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span>Reason</span> <strong>{{ ucwords($recall->reason) }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5>Action</h5>
                    </div>
                    <div class="card-body">
                        @if(in_array($recall->status, ['approved']))
                        <div class="alert alert-success">Request Approved! Select batches to dispatch.</div>
                        <form action="{{ route('store.stock-control.recall.dispatch', $recall) }}" method="POST">
                            @csrf
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Batch</th>
                                        <th>Expiry</th>
                                        <th>Stock</th>
                                        <th>Dispatch Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($batches as $batch)
                                    <tr>
                                        <td>{{ $batch->batch_number }}<input type="hidden" name="batches[{{$batch->id}}][batch_id]" value="{{$batch->id}}"></td>
                                        <td>{{ $batch->expiry_date }}</td>
                                        <td>{{ $batch->quantity }}</td>
                                        <td><input type="number" name="batches[{{$batch->id}}][quantity]" class="form-control form-control-sm" max="{{$batch->quantity}}" min="0" value="0"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button class="btn btn-primary w-100">Dispatch Now</button>
                        </form>
                        @elseif($recall->status == 'dispatched')
                        <div class="text-center py-4 text-success">
                            <i class="mdi mdi-check-circle display-1"></i>
                            <h4>Dispatched Successfully</h4>
                        </div>
                        @elseif($recall->status == 'pending_store_approval')
                        <div class="alert alert-info border-0 shadow-sm">
                            <i class="mdi mdi-information me-1"></i> This request is waiting for store approval.
                        </div>

                        <form action="{{ route('store.stock-control.recall.update-status', $recall->id) }}" method="POST" class="mt-3">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Select Action</label>
                                    <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="approved_by_store">Approve Request</option>
                                        <option value="rejected">Reject Request</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3" id="qtyGroup">
                                    <label class="form-label fw-bold">Approve Quantity</label>
                                    <input type="number" name="approved_quantity"
                                        class="form-control @error('approved_quantity') is-invalid @enderror"
                                        value="{{ $recall->requested_quantity }}"
                                        max="{{ $recall->requested_quantity }}" min="1">
                                    <small class="text-muted">Requested: {{ $recall->requested_quantity }}</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Store Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Optional remarks..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="mdi mdi-check-all me-1"></i> Update Recall Request
                            </button>
                        </form>


                       @else
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-lock-outline display-4"></i>
                                <p>This request is {{ $recall->status }}. No further actions required.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        // Agar Reject select karein to quantity hide ho jaye
        document.getElementById('statusSelect').addEventListener('change', function() {
            const qtyGroup = document.getElementById('qtyGroup');
            qtyGroup.style.display = (this.value === 'rejected') ? 'none' : 'block';
        });
    </script>
    @endpush
</x-app-layout>