<x-app-layout title="Stock Alerts & Recalls">

<div class="container-fluid">

    {{-- HEADER --}}
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

    {{-- TABS NAVIGATION --}}
    <ul class="nav nav-tabs mb-3" id="stockTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="recalls-tab" data-bs-toggle="tab" data-bs-target="#recalls" type="button" role="tab" onclick="reloadTable('recallsTable')">
                <i class="mdi mdi-undo-variant me-1"></i> Recall History
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-danger" id="expiry-tab" data-bs-toggle="tab" data-bs-target="#expiry" type="button" role="tab" onclick="reloadTable('expiryTable')">
                <i class="mdi mdi-calendar-clock me-1"></i> Expiry & Damage
                @if($expiryCount > 0)
                    <span class="badge bg-danger ms-1">{{ $expiryCount }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-warning" id="lowstock-tab" data-bs-toggle="tab" data-bs-target="#lowstock" type="button" role="tab" onclick="reloadTable('lowStockTable')">
                <i class="mdi mdi-trending-down me-1"></i> Low Stock
                @if($lowStockCount > 0)
                    <span class="badge bg-warning text-dark ms-1">{{ $lowStockCount }}</span>
                @endif
            </button>
        </li>
    </ul>

    {{-- TABS CONTENT --}}
    <div class="tab-content" id="stockTabsContent">
        
        {{-- TAB 1: RECALL REQUESTS --}}
        <div class="tab-pane fade show active" id="recalls" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="recallsTable" class="table table-hover table-bordered align-middle w-100">
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: EXPIRY & DAMAGE --}}
        <div class="tab-pane fade" id="expiry" role="tabpanel">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold text-danger">Critically Expiring & Damaged Items</h5>
                        <small class="text-muted">Showing items expiring in 60 days or marked damaged</small>
                    </div>
                    <div class="table-responsive">
                        <table id="expiryTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Batch #</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Current Qty</th>
                                    <th>Damaged</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: LOW STOCK --}}
        <div class="tab-pane fade" id="lowstock" role="tabpanel">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold text-warning">Low Stock Items (< 10 units)</h5>
                    </div>
                    <div class="table-responsive">
                        <table id="lowStockTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th class="text-center">On Hand</th>
                                    <th>Reorder Suggestion</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
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
    // Helper to reload table data without full page refresh
    function reloadTable(tableId) {
        $('#' + tableId).DataTable().ajax.reload(null, false);
    }

    $(document).ready(function() {
        // 1. Recalls Table
        $('#recallsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('store.stock-control.recall.index') }}",
                data: function(d) { d.tab = 'recalls'; }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'product_name', name: 'product.product_name' },
                { data: 'requested_quantity', name: 'requested_quantity', className: 'fw-bold' },
                { data: 'reason', name: 'reason' },
                { data: 'status', name: 'status' },
                { data: 'initiator_name', name: 'initiator.name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[6, 'desc']]
        });

        // 2. Expiry Table
        $('#expiryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('store.stock-control.recall.index') }}",
                data: function(d) { d.tab = 'expiry'; }
            },
            columns: [
                { data: 'batch_number', name: 'product_batches.batch_number' },
                { data: 'product_name', name: 'products.product_name' },
                { data: 'sku', name: 'products.sku' },
                { data: 'category_name', name: 'product_categories.name' },
                { data: 'quantity', name: 'product_batches.quantity' },
                { data: 'damaged_quantity', name: 'product_batches.damaged_quantity' },
                { data: 'expiry_date', name: 'product_batches.expiry_date' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[6, 'asc']] // Order by expiry date
        });

        // 3. Low Stock Table
        $('#lowStockTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('store.stock-control.recall.index') }}",
                data: function(d) { d.tab = 'lowstock'; }
            },
            columns: [
                { data: 'product_name', name: 'product.product_name' },
                { data: 'sku', name: 'product.sku' },
                { data: 'category_name', name: 'product.category.name' },
                { data: 'quantity', name: 'quantity', className: 'text-center' },
                { data: 'reorder_suggestion', name: 'reorder_suggestion', orderable: false, searchable: false }
            ],
            order: [[3, 'asc']] // Order by quantity ascending
        });
        
        // Fix for DataTables width when inside hidden tabs
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });
</script>
@endpush

</x-app-layout>