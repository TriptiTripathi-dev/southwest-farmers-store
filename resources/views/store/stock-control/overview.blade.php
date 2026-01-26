<x-app-layout title="My Stock Overview">

<div class="container-fluid px-4 py-4">

    {{-- CLEAN MODERN HEADER --}}
    <div class="mb-4">
        <div class="card border-0 shadow" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-white">
                        <h3 class="fw-bold mb-2">
                            <i class="mdi mdi-package-variant-closed me-2"></i>Stock Inventory Management
                        </h3>
                        <p class="mb-0 opacity-90 small">
                            <i class="mdi mdi-chart-line me-1"></i>Real-time tracking and analytics
                        </p>
                    </div>
                    <button class="btn btn-light shadow-sm" onclick="window.location.reload()">
                        <i class="mdi mdi-refresh me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- CLEAN ANALYTICS CARDS --}}
    <div class="row g-4 mb-4">
        
        {{-- Sales Chart - Cleaner Design --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                            <i class="mdi mdi-chart-donut text-primary fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Sales Distribution by Area</h6>
                            <small class="text-muted">Total units sold across all locations</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 280px; position: relative;">
                        <canvas id="areaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products - Cleaner Table --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header text-white border-0 py-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 rounded p-2 me-3">
                            <i class="mdi mdi-trophy text-white fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Top Performers</h6>
                            <small class="opacity-90">Best selling per area</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="px-3 py-2 fw-semibold small">AREA</th>
                                    <th class="px-3 py-2 fw-semibold small">PRODUCT</th>
                                    <th class="px-3 py-2 text-end fw-semibold small">QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProductsByArea as $area => $data)
                                <tr>
                                    <td class="px-3 py-2">
                                        <span class="badge bg-light text-dark small">{{ $area }}</span>
                                    </td>
                                    <td class="px-3 py-2 small">{{ \Illuminate\Support\Str::limit($data->product_name, 20) }}</td>
                                    <td class="px-3 py-2 text-end">
                                        <span class="badge bg-success small">{{ number_format($data->qty) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4 small">
                                        <i class="mdi mdi-information-outline d-block mb-1"></i>
                                        No sales data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CLEAN FILTER SECTION --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold mb-0">
                <i class="mdi mdi-filter-outline me-2 text-primary"></i> Filter Options
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted mb-2">CATEGORY</label>
                    <select id="categoryFilter" class="form-select shadow-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted mb-2">STOCK LEVEL</label>
                    <select id="lowStockFilter" class="form-select shadow-sm">
                        <option value="">All Stock Levels</option>
                        <option value="1">Low Stock Only (Below 10 units)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- CLEAN DATA TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0">
                    <i class="mdi mdi-view-list me-2 text-primary"></i> Stock Inventory Details
                </h6>
                <span class="badge bg-success-subtle text-success px-3 py-2">
                    <i class="mdi mdi-circle-small"></i> Live Data
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="myStockTable" class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-3 fw-semibold">PRODUCT</th>
                            <th class="px-3 py-3 fw-semibold">SKU</th>
                            <th class="px-3 py-3 fw-semibold">CATEGORY</th>
                            <th class="px-3 py-3 fw-semibold text-center">QUANTITY</th>
                            <th class="px-3 py-3 fw-semibold text-end">PRICE</th>
                            <th class="px-3 py-3 fw-semibold text-end">TOTAL VALUE</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
    body {
        background-color: #f8f9fa;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    
    .card {
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08) !important;
    }
    
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .form-select,
    .form-control {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
    }
    
    .form-select:focus,
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
    }
    
    .dataTables_wrapper {
        padding: 1.25rem;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        margin-left: 0.5rem;
        font-size: 0.9rem;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 0.375rem 2rem 0.375rem 0.75rem;
        margin: 0 0.5rem;
        font-size: 0.9rem;
    }
    
    .dataTables_wrapper .dataTables_info {
        font-size: 0.875rem;
        color: #6c757d;
        padding-top: 0.5rem;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0.5rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #667eea;
        color: white !important;
        border: 1px solid #667eea;
    }
    
    .dataTables_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid #e9ecef;
        box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.1);
    }
    
    /* Export Buttons */
    .dt-buttons {
        margin-bottom: 0.75rem;
    }
    
    .dt-buttons .btn {
        border-radius: 0.375rem;
        margin: 0 0.25rem;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border: none;
    }
    
    .dt-buttons .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .dt-buttons .btn-success {
        background: #28a745;
        color: white;
    }
    
    .dt-buttons .btn-danger {
        background: #dc3545;
        color: white;
    }
    
    .dt-buttons .btn-info {
        background: #17a2b8;
        color: white;
    }
    
    /* Scrollbar Styling */
    .table-responsive::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    
    // Initialize Clean DataTable
    let table = $('#myStockTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("store.stock-control.overview.data") }}',
            data: function(d) {
                d.category_id = $('#categoryFilter').val();
                d.low_stock = $('#lowStockFilter').val();
            }
        },
        columns: [
            { 
                data: 'product_name',
                className: 'px-3',
                render: function(data) {
                    return '<div class="fw-semibold text-dark">' + data + '</div>';
                }
            },
            { 
                data: 'sku',
                className: 'px-3',
                render: function(data) {
                    return '<code style="background: #f1f3f5; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.85rem;">' + data + '</code>';
                }
            },
            { 
                data: 'category_name',
                className: 'px-3',
                render: function(data) {
                    return '<span class="badge bg-primary-subtle text-primary" style="font-weight: 500;">' + data + '</span>';
                }
            },
            { 
                data: 'quantity',
                className: 'px-3 text-center',
                render: function(data) {
                    let color = data < 10 ? 'danger' : (data < 50 ? 'warning' : 'success');
                    return '<span class="badge bg-' + color + '" style="min-width: 50px; font-size: 0.85rem;">' + data + '</span>';
                }
            },
            { 
                data: 'selling_price',
                className: 'px-3 text-end',
                render: function(data) {
                    return '<span class="text-success fw-semibold">' + data + '</span>';
                }
            },
            { 
                data: 'value',
                className: 'px-3 text-end',
                render: function(data) {
                    return '<span class="fw-bold text-primary">' + data + '</span>';
                }
            }
        ],
        dom: '<"row"<"col-sm-6"l><"col-sm-6 text-end"B>>' +
             '<"row mt-2"<"col-sm-12"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row mt-3"<"col-sm-5"i><"col-sm-7"p>>',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-secondary',
                text: '<i class="mdi mdi-content-copy me-1"></i>Copy'
            },
            {
                extend: 'csv',
                className: 'btn btn-success',
                text: '<i class="mdi mdi-file-delimited me-1"></i>CSV'
            },
            {
                extend: 'excel',
                className: 'btn btn-success',
                text: '<i class="mdi mdi-file-excel me-1"></i>Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger',
                text: '<i class="mdi mdi-file-pdf me-1"></i>PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-info',
                text: '<i class="mdi mdi-printer me-1"></i>Print'
            }
        ],
        order: [[3, 'asc']],
        pageLength: 25,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
            infoEmpty: "No products available",
            infoFiltered: "(filtered from _MAX_ total)",
            zeroRecords: "No matching products found",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });

    // Filter Change Handler
    $('#categoryFilter, #lowStockFilter').on('change', function() {
        table.draw();
    });

    // Initialize Clean Chart
    const ctx = document.getElementById('areaChart').getContext('2d');
    const areaLabels = {!! json_encode($areaLabels) !!};
    const areaData = {!! json_encode($areaData) !!};

    if(areaLabels.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: areaLabels,
                datasets: [{
                    data: areaData,
                    backgroundColor: [
                        '#667eea', '#764ba2', '#f093fb', '#f5576c', 
                        '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'
                    ],
                    borderWidth: 4,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { 
                            boxWidth: 12,
                            padding: 12,
                            font: {
                                size: 13,
                                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = ((value / total) * 100).toFixed(1);
                                return ' ' + value.toLocaleString() + ' units (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    } else {
        ctx.font = "14px -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto";
        ctx.fillStyle = "#adb5bd";
        ctx.textAlign = "center";
        ctx.fillText("No sales data available", ctx.canvas.width / 2, ctx.canvas.height / 2);
    }
});
</script>
@endpush

</x-app-layout>