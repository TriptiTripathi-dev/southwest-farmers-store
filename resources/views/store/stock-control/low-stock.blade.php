<x-app-layout title="Low Stock & Reorder">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-alert-circle-outline text-danger me-2"></i> Low Stock & Reorder
            </h4>
            <small class="text-muted">Products below minimum stock level - request now</small>
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
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table id="lowStockTable" class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Current Qty</th>
                        <th>Suggested Reorder</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let table = $('#lowStockTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("store.stock-control.low-stock.data") }}',
            data: function(d) {
                d.category_id = $('#categoryFilter').val();
            }
        },
        columns: [
            { data: 'product_name' },
            { data: 'sku' },
            { data: 'category_name' },
            { data: 'current_qty' },
            { data: 'suggested_reorder' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#categoryFilter').on('change', function() {
        table.draw();
    });

    $('#lowStockTable').on('click', '.quick-request', function() {
        let productId = $(this).data('product-id');
        let qty = $(this).data('qty');

        Swal.fire({
            title: 'Quick Stock Request',
            html: `<input id="swal-qty" class="swal2-input" type="number" value="${qty}" min="1">`,
            showCancelButton: true,
            confirmButtonText: 'Send Request',
            preConfirm: () => {
                let qty = document.getElementById('swal-qty').value;
                if (!qty || qty < 1) {
                    Swal.showValidationMessage('Enter valid quantity');
                }
                return { product_id: productId, quantity: qty };
            }
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("store.stock-control.low-stock.request") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: result.value.product_id,
                        quantity: result.value.quantity
                    },
                    success: function(res) {
                        Swal.fire('Success', res.message, 'success');
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to send request', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush

</x-app-layout>