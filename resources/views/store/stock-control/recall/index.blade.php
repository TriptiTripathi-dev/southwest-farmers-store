<x-app-layout title="Stock Alerts & Recalls">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-alert-decagram text-danger me-2"></i> Stock Alerts & Recalls
            </h4>
            <small class="text-muted">Manage recalls, expiration alerts, and low stock warnings</small>
        </div>
        <a href="{{ route('store.stock-control.recall.create') }}" class="btn btn-danger">
            <i class="mdi mdi-plus-circle me-1"></i> Create Recall Request
        </a>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs mb-3" id="stockTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold" id="recalls-tab" data-bs-toggle="tab" data-bs-target="#recalls" onclick="reloadTable('recallsTable')">
                <i class="mdi mdi-undo-variant me-1"></i> Recall History
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-danger" id="expiry-tab" data-bs-toggle="tab" data-bs-target="#expiry" onclick="reloadTable('expiryTable')">
                <i class="mdi mdi-calendar-clock me-1"></i> Expiry & Damage
                @if($expiryCount > 0) <span class="badge bg-danger ms-1">{{ $expiryCount }}</span> @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-warning" id="lowstock-tab" data-bs-toggle="tab" data-bs-target="#lowstock" onclick="reloadTable('lowStockTable')">
                <i class="mdi mdi-trending-down me-1"></i> Low Stock
                @if($lowStockCount > 0) <span class="badge bg-warning text-dark ms-1">{{ $lowStockCount }}</span> @endif
            </button>
        </li>
    </ul>

    {{-- CONTENT --}}
    <div class="tab-content">
        <div class="tab-pane fade show active" id="recalls">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <table id="recallsTable" class="table table-hover w-100">
                    <thead class="table-light"><tr><th>ID</th><th>Product</th><th>Qty</th><th>Reason</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                </table>
            </div></div>
        </div>

        <div class="tab-pane fade" id="expiry">
            <div class="card border-0 shadow-sm border-start border-danger border-4"><div class="card-body">
                <table id="expiryTable" class="table table-hover w-100">
                    <thead class="table-light"><tr><th>Batch</th><th>Product</th><th>SKU</th><th>Category</th><th>Expiry</th><th>Qty</th><th>Damaged</th><th>Status</th><th>Action</th></tr></thead>
                </table>
            </div></div>
        </div>

        <div class="tab-pane fade" id="lowstock">
            <div class="card border-0 shadow-sm border-start border-warning border-4"><div class="card-body">
                <table id="lowStockTable" class="table table-hover w-100">
                    <thead class="table-light"><tr><th>Product</th><th>SKU</th><th>Category</th><th>On Hand</th><th>Suggestion</th></tr></thead>
                </table>
            </div></div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    function reloadTable(id) { $('#' + id).DataTable().ajax.reload(null, false); }

    $(document).ready(function() {
        $('#recallsTable').DataTable({ processing: true, serverSide: true, ajax: { url: "{{ route('store.stock-control.recall.index') }}", data: { tab: 'recalls' } }, columns: [ {data:'id'}, {data:'product_name'}, {data:'requested_quantity'}, {data:'reason'}, {data:'status'}, {data:'created_at'}, {data:'action'} ], order: [[5, 'desc']] });
        
        $('#expiryTable').DataTable({ processing: true, serverSide: true, ajax: { url: "{{ route('store.stock-control.recall.index') }}", data: { tab: 'expiry' } }, columns: [ {data:'batch_number'}, {data:'product_name'}, {data:'sku'}, {data:'category_name'}, {data:'expiry_date'}, {data:'quantity'}, {data:'damaged_quantity'}, {data:'status'}, {data:'action'} ], order: [[4, 'asc']] });
        
        $('#lowStockTable').DataTable({ processing: true, serverSide: true, ajax: { url: "{{ route('store.stock-control.recall.index') }}", data: { tab: 'lowstock' } }, columns: [ {data:'product_name'}, {data:'sku'}, {data:'category_name'}, {data:'quantity'}, {data:'reorder_suggestion'} ], order: [[3, 'asc']] });
        
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) { $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust(); });
    });
</script>
@endpush
</x-app-layout>