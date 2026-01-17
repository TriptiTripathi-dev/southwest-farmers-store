<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('store.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Products (Bulk Local Create)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="import_category" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Subcategory <span class="text-danger">*</span></label>
                        <select name="subcategory_id" id="import_subcategory" class="form-select" required>
                            <option value="">Select Subcategory</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Excel File (xlsx, csv)</label>
                        <input type="file" name="file" class="form-control" required>
                        <small class="text-muted d-block mt-1">Columns: product_name, sku, selling_price, stock_quantity, unit, barcode</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success text-white">Upload & Create Products</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Ajax for Subcategory (Vanilla JS or jQuery)
    document.getElementById('import_category').addEventListener('change', function() {
        var catId = this.value;
        var subSelect = document.getElementById('import_subcategory');
        subSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch("{{ route('store.subcategories.get') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ category_id: catId })
        })
        .then(response => response.json())
        .then(data => {
            subSelect.innerHTML = '<option value="">Select Subcategory</option>';
            data.forEach(sub => {
                var option = document.createElement('option');
                option.value = sub.id;
                option.text = sub.name;
                subSelect.appendChild(option);
            });
        });
    });
</script>