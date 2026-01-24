<x-app-layout title="My Recall Requests">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-undo-variant text-danger me-2"></i> Stock Returns (Recall)
            </h4>
            <small class="text-muted">Manage items you are sending back to the Warehouse</small>
        </div>
        
        {{-- NEW: Create Button for Store-Initiated Recall --}}
        <a href="{{ route('store.stock-control.recall.create') }}" class="btn btn-danger">
            <i class="mdi mdi-plus-circle me-1"></i> Create Return Request
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th class="text-center">Return Qty</th>
                            <th>Reason</th>
                            <th class="text-center">Status</th>
                            <th>Created Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recalls as $recall)
                            <tr>
                                <td>
                                    <span class="fw-bold">#{{ str_pad($recall->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $recall->product->product_name }}</div>
                                    <small class="text-muted">{{ $recall->product->sku }}</small>
                                </td>
                                <td class="text-center fw-bold fs-6">{{ $recall->requested_quantity }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucwords(str_replace('_', ' ', $recall->reason)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($recall->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending Dispatch</span>
                                    @elseif($recall->status == 'dispatched')
                                        <span class="badge bg-info text-white">Dispatched</span>
                                    @elseif($recall->status == 'received')
                                        <span class="badge bg-success">Received by Warehouse</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $recall->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $recall->created_at->format('d M Y') }} <br> <small class="text-muted">{{ $recall->created_at->format('h:i A') }}</small></td>
                                <td class="text-end">
                                    @if($recall->status == 'pending')
                                        <a href="{{ route('store.stock-control.recall.show', $recall) }}" 
                                           class="btn btn-sm btn-danger shadow-sm">
                                            <i class="mdi mdi-truck-delivery me-1"></i> Dispatch Now
                                        </a>
                                    @else
                                        <a href="{{ route('store.stock-control.recall.show', $recall->id) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="mdi mdi-eye me-1"></i> View Details
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="opacity-50 mb-2">
                                        <i class="mdi mdi-package-variant-closed-remove fs-1"></i>
                                    </div>
                                    <h5>No return requests found</h5>
                                    <p class="small mb-3">You haven't initiated any stock returns yet.</p>
                                    <a href="{{ route('store.stock-control.recall.create') }}" class="btn btn-sm btn-outline-danger">
                                        Create First Request
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $recalls->links() }}
            </div>
        </div>
    </div>

</div>

</x-app-layout>