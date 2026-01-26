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
                <div class="card-header bg-light"><h5>Summary</h5></div>
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
                <div class="card-header bg-light"><h5>Action</h5></div>
                <div class="card-body">
                    @if(in_array($recall->status, ['approved', 'approved_by_store']))
                        <div class="alert alert-success">Request Approved! Select batches to dispatch.</div>
                        <form action="{{ route('store.stock-control.recall.dispatch', $recall) }}" method="POST">
                            @csrf
                            <table class="table">
                                <thead><tr><th>Batch</th><th>Expiry</th><th>Stock</th><th>Dispatch Qty</th></tr></thead>
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
                    @else
                        <div class="text-center py-4 text-muted">Waiting for approval or completed.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>