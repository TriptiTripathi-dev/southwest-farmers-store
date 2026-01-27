<x-app-layout title="Expiry & Damage Alert">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-timer-alert text-danger me-2"></i> Expiry & Damage Alert
            </h4>
            <small class="text-muted">Monitor batches nearing expiry or damaged in your store</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Expiry Within</label>
                    <select id="expiryDays" class="form-select">
                        <option value="30">Next 30 Days</option>
                        <option value="60" selected>Next 60 Days</option>
                        <option value="90">Next 90 Days</option>
                        <option value="all">All Upcoming & Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="damagedOnly">
                        <label class="form-check-label">Damaged Only</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="applyFilters" class="btn btn-primary w-100">
                        <i class="mdi mdi-filter me-1"></i> Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table id="expiryTable" class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Batch No</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                        <th>Qty Available</th>
                        <th>Damaged</th>
                        <th>Value ($)</th>
                        <th>Status</th>
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
    let table = $('#expiryTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("store.stock-control.expiry.data") }}',
            data: function(d) {
                d.days = $('#expiryDays').val();
                d.category_id = $('#categoryFilter').val();
                d.damaged_only = $('#damagedOnly').is(':checked') ? 1 : 0;
            }
        },
        columns: [
            { data: 'product_name' },
            { data: 'batch_number' },
            { data: 'expiry_date' },
            { data: 'days_left' },
            { data: 'quantity' },
            { data: 'damaged_quantity' },
            { data: 'value', render: data => '$ ' + parseFloat(data || 0).toLocaleString('en-IN') },
            { data: 'status' }
        ],
        order: [[3, 'asc']],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    $('#applyFilters, #expiryDays, #categoryFilter, #damagedOnly').on('change click', function() {
        table.draw();
    });
});
</script>
@endpush

</x-app-layout>