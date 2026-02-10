<x-app-layout title="Inter-Store Transfers">
<div class="container-fluid">
    
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-paper-plane me-1"></i> Request Stock
        </div>
        <div class="card-body">
            <form action="{{ route('transfers.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Request From Store</label>
                    <select name="from_store_id" class="form-select" required>
                        <option value="">-- Select Store --</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->store_name }} ({{ $store->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Send Request</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-shipping-fast me-1"></i> Requests Received (Action Required)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>ID</th><th>To Store</th><th>Product</th><th>Qty</th><th>Action</th></tr></thead>
                            <tbody>
                                @forelse($outgoing as $t)
                                <tr>
                                    <td>{{ $t->transfer_number }}</td>
                                    <td>{{ $t->toStore->store_name ?? 'N/A' }}</td>
                                    <td>{{ $t->product->product_name ?? 'N/A' }}</td>
                                    <td class="fw-bold">{{ $t->quantity_sent }}</td>
                                    <td>
                                        @if($t->status == 'pending')
                                        <form action="{{ route('transfers.dispatch', $t->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to dispatch this stock? It will be deducted immediately.');">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Dispatch</button>
                                        </form>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">No pending requests to fulfill.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-inbox me-1"></i> My Sent Requests
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>ID</th><th>From Store</th><th>Product</th><th>Sent</th><th>Action</th></tr></thead>
                            <tbody>
                                @forelse($incoming as $t)
                                <tr>
                                    <td>{{ $t->transfer_number }}</td>
                                    <td>{{ $t->fromStore->store_name ?? 'N/A' }}</td>
                                    <td>{{ $t->product->product_name ?? 'N/A' }}</td>
                                    <td>{{ $t->quantity_sent }}</td>
                                    <td>
                                        @if($t->status == 'dispatched')
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openReceiveModal('{{ $t->id }}', '{{ $t->product->product_name }}', {{ $t->quantity_sent }})">
                                                Receive
                                            </button>
                                        @elseif($t->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-success">Received ({{ $t->quantity_received }})</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">No active requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="receiveModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="receiveForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Confirm Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Receiving: <strong id="modalProductName"></strong></p>
                <p>Expected Qty: <strong id="modalExpectedQty"></strong></p>
                <div class="mb-3">
                    <label>Actual Received Quantity</label>
                    <input type="number" name="received_qty" id="modalReceivedQty" class="form-control" required>
                    <small class="text-muted">Enter less than expected if there is a shortage.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Confirm & Add to Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReceiveModal(id, productName, qty) {
    document.getElementById('modalProductName').innerText = productName;
    document.getElementById('modalExpectedQty').innerText = qty;
    document.getElementById('modalReceivedQty').value = qty; // Default to full amount
    
    // Set dynamic route
    let form = document.getElementById('receiveForm');
    form.action = "/store/transfers/" + id + "/receive";
    
    new bootstrap.Modal(document.getElementById('receiveModal')).show();
}
</script>
</x-app-layout>