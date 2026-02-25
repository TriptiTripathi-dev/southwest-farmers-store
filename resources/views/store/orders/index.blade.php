<x-app-layout title="Store Purchase Orders">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-cart-arrow-down text-primary me-2"></i> Purchase Orders
            </h4>
            <small class="text-muted">Manage and track your warehouse orders</small>
        </div>
        @if($schedule)
            <div class="badge bg-soft-info text-info p-2 border border-info border-opacity-25">
                <i class="mdi mdi-calendar-clock me-1"></i> Expected Order Day: <strong>{{ $schedule->expected_day }}</strong>
            </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="ordersTable" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">PO Number</th>
                            <th>Date</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('store.orders.data') }}",
            columns: [
                { data: 'po_number', name: 'po_number', className: 'ps-3 fw-bold' },
                { data: 'created_at', name: 'created_at' },
                { data: 'total_items', name: 'total_items' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end pe-3' }
            ],
            language: {
                searchPlaceholder: "Search PO Number...",
                processing: '<div class="spinner-border text-primary" role="status"></div>'
            }
        });
    });
</script>
@endpush

</x-app-layout>
