<x-app-layout title="Recall Requests">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-undo-variant text-warning me-2"></i> Recall Requests
            </h4>
            <small class="text-muted">Manage warehouse-initiated recall requests for your store</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Requested Qty</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Initiated By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recalls as $recall)
                            <tr>
                                <td>#{{ str_pad($recall->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $recall->product->product_name }}</td>
                                <td class="fw-bold">{{ $recall->requested_quantity }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $recall->reason)) }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $recall->status == 'pending_store_approval' ? 'warning' : 
                                        ($recall->status == 'completed' ? 'success' : 
                                        ($recall->status == 'rejected_by_store' ? 'danger' : 'primary')) 
                                    }}">
                                        {{ ucwords(str_replace('_', ' ', $recall->status)) }}
                                    </span>
                                </td>
                                <td>{{ $recall->initiator->name ?? 'Warehouse Admin' }}</td>
                                <td>{{ $recall->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('store.stock-control.recall.show', $recall) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="mdi mdi-eye me-1"></i> View & Respond
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-information-outline fs-3 d-block mb-2"></i>
                                    No recall requests at this time.
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