@php
    $tab = request('tab', 'receiving');
    $pageTitle = $tab === 'history' ? 'Order History' : 'Receiving Orders';
@endphp

<x-app-layout :title="$pageTitle">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi {{ $tab === 'history' ? 'mdi-history text-success' : 'mdi-truck-delivery text-info' }} me-2"></i> {{ $pageTitle }}
            </h4>
            <small class="text-muted">Manage and track store purchase orders and incoming receiving shipments</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($schedule)
                <div class="badge bg-soft-info text-info p-2 border border-info border-opacity-25">
                    <i class="mdi mdi-calendar-clock me-1"></i> Expected Order Day: <strong>{{ $schedule->expected_day }}</strong>
                </div>
            @endif
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form id="filterForm" class="row g-2 align-items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Order ID / Product</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search PO Number...">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Status</label>
                    <select id="statusSelect" class="form-select">
                        <option value="">All Status</option>
                        @if($tab === 'receiving')
                            <option value="approved">Approved</option>
                            <option value="dispatched">Dispatched</option>
                            <option value="in_transit">In Transit</option>
                            <option value="receiving">Receiving</option>
                        @else
                            <option value="completed">Completed</option>
                            <option value="partially_received">Partially Received</option>
                            <option value="cancelled">Cancelled</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Date</label>
                    <input type="date" id="dateInput" class="form-control">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="button" id="btnFilter" class="btn btn-success fw-bold px-4 flex-grow-1">
                        <i class="mdi mdi-filter me-1"></i> Filter
                    </button>
                    <button type="button" id="btnReset" class="btn btn-outline-secondary px-3">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="ordersTable" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="bg-light">
                        @if($tab === 'history')
                            <tr>
                                <th class="ps-3">PO Number</th>
                                <th>PO Date</th>
                                <th>Received Date</th>
                                <th class="text-center">QTY Ordered</th>
                                <th class="text-center">QTY Received</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        @else
                            <tr>
                                <th class="ps-3">PO Number</th>
                                <th>Received Date</th>
                                <th class="text-center">QTY Received</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        @endif
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
        $.fn.dataTable.ext.errMode = 'none';
        
        const isHistory = "{{ $tab }}" === "history";

        const columns = isHistory ? [
            { data: 'po_number', name: 'po_number', className: 'ps-3 fw-bold text-primary' },
            { data: 'created_at', name: 'created_at' },
            { data: 'received_date', name: 'received_date' },
            { data: 'qty_ordered', name: 'qty_ordered', className: 'text-center fw-bold' },
            { data: 'qty_received', name: 'qty_received', className: 'text-center fw-bold text-success' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end pe-3' }
        ] : [
            { data: 'po_number', name: 'po_number', className: 'ps-3 fw-bold text-primary' },
            { data: 'received_date', name: 'received_date' },
            { data: 'qty_received', name: 'qty_received', className: 'text-center fw-bold text-success' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end pe-3' }
        ];
        
        const table = $('#ordersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('store.orders.data') }}",
                data: function(d) {
                    d.tab = "{{ $tab }}";
                    d.search_term = $('#searchInput').val();
                    d.status_filter = $('#statusSelect').val();
                    d.date = $('#dateInput').val();
                }
            },
            columns: columns,
            language: {
                searchPlaceholder: "Search PO Number...",
                processing: '<div class="spinner-border text-primary" role="status"></div>'
            }
        });

        $('#btnFilter').on('click', function() {
            table.draw();
        });

        $('#btnReset').on('click', function() {
            $('#filterForm')[0].reset();
            table.draw();
        });
    });
</script>
@endpush

</x-app-layout>
