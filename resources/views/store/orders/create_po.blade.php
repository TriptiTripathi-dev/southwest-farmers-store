<x-app-layout title="Create Store PO">
<div class="container-fluid">
    <x-page-header>
        <a href="{{ route('store.orders.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left me-1"></i> Back to List
        </a>
    </x-page-header>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory.po.store') }}" method="POST">
        @csrf
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">Select Products</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="poTable">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th width="150">Quantity</th>
                            <th width="80">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="products[0][product_id]" class="form-select select2" required>
                                    <option value="">Select a product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="products[0][quantity]" class="form-control" min="1" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="mdi mdi-delete"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-primary btn-sm mt-2" id="addRow">
                    <i class="mdi mdi-plus"></i> Add Product
                </button>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm">
                <i class="mdi mdi-check-circle me-1"></i> Submit Store PO
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5' });
        
        let rowIdx = 1;
        $('#addRow').click(function() {
            let row = `<tr>
                <td>
                    <select name="products[${rowIdx}][product_id]" class="form-select select2" required>
                        <option value="">Select a product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="products[${rowIdx}][quantity]" class="form-control" min="1" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="mdi mdi-delete"></i></button>
                </td>
            </tr>`;
            $('#poTable tbody').append(row);
            $('.select2').select2({ theme: 'bootstrap-5' });
            rowIdx++;
        });

        $(document).on('click', '.remove-row', function() {
            if ($('#poTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert("You must have at least one product.");
            }
        });
    });
</script>
@endpush
</x-app-layout>
