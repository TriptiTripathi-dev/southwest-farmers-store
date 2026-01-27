<x-app-layout title="Inter-Store Transfers">
<div class="container-fluid">
    
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Request Stock from Another Store</div>
        <div class="card-body">
            <form action="{{ route('transfers.store') }}" method="POST" class="row align-items-end">
                @csrf
                <div class="col-md-4">
                    <label>Request From</label>
                    <select name="from_store_id" class="form-select" required>
                        <option value="1">Main Warehouse</option>
                        <option value="2">Store B</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Product ID</label>
                    <input type="number" name="product_id" class="form-control" placeholder="Product ID" required>
                </div>
                <div class="col-md-2">
                    <label>Quantity</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Send Request</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">Requests to Fulfill (Outgoing)</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>To</th><th>Product</th><th>Qty</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($outgoing as $t)
                            <tr>
                                <td>Store #{{ $t->to_store_id }}</td>
                                <td>{{ $t->product->product_name ?? $t->product_id }}</td>
                                <td>{{ $t->quantity_requested }}</td>
                                <td>
                                    @if($t->status == 'pending')
                                    <form action="{{ route('transfers.dispatch', $t->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Dispatch (FIFO)</button>
                                    </form>
                                    @else
                                        <span class="badge bg-secondary">{{ $t->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">My Requests (Incoming)</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>From</th><th>Product</th><th>Qty</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($incoming as $t)
                            <tr>
                                <td>Store #{{ $t->from_store_id }}</td>
                                <td>{{ $t->product->product_name ?? $t->product_id }}</td>
                                <td>{{ $t->quantity_requested }}</td>
                                <td>
                                    @if($t->status == 'dispatched')
                                    <form action="{{ route('transfers.receive', $t->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-primary">Receive Stock</button>
                                    </form>
                                    @else
                                        <span class="badge bg-secondary">{{ $t->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>