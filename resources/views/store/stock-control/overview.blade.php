<x-app-layout title="My Stock Overview">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-package-variant-closed text-primary me-2"></i> My Stock Overview
            </h4>
            <small class="text-muted">Current inventory in your store</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary">
                <i class="mdi mdi-refresh me-1"></i> Refresh
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Low Stock</label>
                    <select id="lowStockFilter" class="form-select">
                        <option value="">All</option>
                        <option value="1">Low Stock Only (< 10)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table id="myStockTable" class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Selling Price</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
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

<script>
$(document).ready(function() {
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
            { data: 'product_name' },
            { data: 'sku' },
            { data: 'category_name' },
            { data: 'quantity' },
            { data: 'selling_price' },
            { data: 'value' }
        ],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        order: [[3, 'asc']]
    });

    $('#categoryFilter, #lowStockFilter').on('change', function() {
        table.draw();
    });
});
</script>
@endpush

</x-app-layout>