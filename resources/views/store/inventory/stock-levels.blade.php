<x-app-layout title="Stock Levels">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="mdi mdi-trending-up text-success me-2"></i> Stock Replenishment Levels
            </h4>
            <small class="text-muted">Set Minimum and Maximum stock levels to trigger warehouse auto-orders</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="stockLevelsTable" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Product Name</th>
                            <th>SKU</th>
                            <th class="text-center">Min Stock</th>
                            <th class="text-center">Max Stock</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Update Stock Levels</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="item_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Minimum Stock (Trigger)</label>
                        <input type="number" name="min_stock" id="min_stock" class="form-control" required min="0">
                        <div class="form-text">Auto-order will be created when quantity falls below this value.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Maximum Stock (Order Target)</label>
                        <input type="number" name="max_stock" id="max_stock" class="form-control" required min="0">
                        <div class="form-text">The level you wish to reach when auto-ordering.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" id="saveBtn">Save Changes</button>
                </div>
            </form>
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
    $(document).ready(function() {
        let table = $('#stockLevelsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('store.inventory.stock-levels.data') }}",
            columns: [
                { data: 'product_name', name: 'product_name', className: 'ps-3 fw-bold' },
                { data: 'sku', name: 'sku' },
                { data: 'min_stock', name: 'min_stock', className: 'text-center' },
                { data: 'max_stock', name: 'max_stock', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end pe-3' }
            ]
        });

        $('#stockLevelsTable').on('click', '.edit-levels', function() {
            let id = $(this).data('id');
            let min = $(this).data('min');
            let max = $(this).data('max');

            $('#item_id').val(id);
            $('#min_stock').val(min);
            $('#max_stock').val(max);
            $('#editModal').modal('show');
        });

        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            $('#saveBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
            
            $.ajax({
                url: "{{ route('store.inventory.stock-levels.update') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#editModal').modal('hide');
                    Swal.fire('Success', res.message, 'success');
                    table.draw();
                },
                error: function(err) {
                    let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Something went wrong';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false).text('Save Changes');
                }
            });
        });
    });
</script>
@endpush

</x-app-layout>
