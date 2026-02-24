<x-app-layout title="Purchase Orders">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-file-document text-primary me-2"></i> Purchase Orders
            </h4>
            <small class="text-muted">Manage store purchase orders and stock requests</small>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('store.stock-control.generate-replenishment') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary fw-bold px-4 rounded-pill shadow-sm">
                    <i class="mdi mdi-auto-fix me-1"></i> Replenish from Warehouse
                </button>
            </form>
            <a href="{{ route('store.stock-control.requests.create') }}" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                <i class="mdi mdi-plus me-1"></i> Create New PO
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search Request #..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="mdi mdi-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('store.stock-control.requests') }}" class="btn btn-outline-secondary w-100">
                        <i class="mdi mdi-refresh me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- PO List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Request #</th>
                            <th>Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Total Items</th>
                            <th class="text-end">Total Amount</th>
                            <th>Requested By</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr class="cursor-pointer" onclick="window.location='{{ route('store.stock-control.requests.show', $request->id) }}'">
                                <td class="ps-4">
                                    <span class="font-monospace fw-bold text-primary">{{ $request->request_number ?? 'REQ-' . str_pad($request->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $request->created_at->format('d M Y') }}</small>
                                    <br><small class="text-muted">{{ $request->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="text-center">
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="mdi mdi-clock-outline me-1"></i>Pending
                                        </span>
                                    @elseif($request->status == 'dispatched')
                                        <span class="badge bg-info">
                                            <i class="mdi mdi-truck-delivery me-1"></i>Dispatched
                                        </span>
                                    @elseif($request->status == 'completed')
                                        <span class="badge bg-success">
                                            <i class="mdi mdi-check-circle me-1"></i>Completed
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="mdi mdi-close-circle me-1"></i>Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                        {{ $request->total_items ?? $request->items->count() ?? 0 }} items
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    ₹{{ number_format($request->total_amount ?? $request->items->sum('total_cost') ?? 0, 2) }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ $request->requestedBy->name ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center" onclick="event.stopPropagation()">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('store.stock-control.requests.show', $request->id) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        @if($request->status == 'pending')
                                            <a href="{{ route('store.stock-control.requests.edit', $request->id) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="cancelPO({{ $request->id }})" title="Cancel">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-file-document-outline mb-2 fs-1"></i>
                                    <p>No purchase orders found</p>
                                    <a href="{{ route('store.stock-control.requests.create') }}" class="btn btn-primary btn-sm">
                                        <i class="mdi mdi-plus me-1"></i> Create First PO
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($requests->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function cancelPO(id) {
    if (confirm('Are you sure you want to cancel this purchase order?')) {
        fetch(`/store/stock-control/requests/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to cancel PO');
            }
        });
    }
}

// Add cursor pointer style
document.querySelectorAll('.cursor-pointer').forEach(row => {
    row.style.cursor = 'pointer';
});
</script>
@endpush

</x-app-layout>