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
                        <th class="ps-4">Request #</th>
                        <th>Requested By</th>
                        <th class="text-center">Total Items</th>
                        <th class="text-end">Total Amount</th>
                        <th>Dispatch Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $req)
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold text-primary">{{ $req->request_number }}</span>
                            </td>
                            <td>{{ $req->requestedBy->name ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $req->items->count() }} items
                                </span>
                            </td>
                            <td class="text-end fw-bold text-success">
                                ₹{{ number_format($req->total_amount, 2) }}
                            </td>
                            <td>{{ $req->updated_at->format('d M Y, h:i A') }}</td>
                            <td class="text-center">
                                <a href="{{ route('store.stock-control.requests.receive', $req->id) }}" class="btn btn-sm btn-success">
                                    <i class="mdi mdi-check-circle me-1"></i> Confirm Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="mdi mdi-information-outline fs-1 d-block mb-2"></i>
                                No pending received stock at the moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($pending->hasPages())
                <div class="card-footer bg-white">
                    {{ $pending->links() }}
                </div>
            @endif
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