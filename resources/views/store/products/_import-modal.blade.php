<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('store.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary"><i class="mdi mdi-upload me-2"></i>Import Products</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info small rounded-3 mb-3">
                        <i class="mdi mdi-information me-1"></i> File must have columns: <strong>product_name, upc, selling_price, stock_quantity, unit, barcode, cost_price</strong>.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select bg-light border-0 shadow-sm py-2" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="import_category" class="form-select bg-light border-0 shadow-sm py-2" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Subcategory <span class="text-danger">*</span></label>
                        <select name="subcategory_id" id="import_subcategory" class="form-select bg-light border-0 shadow-sm py-2" required>
                            <option value="">Select Subcategory</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Excel/CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control bg-light border-0 shadow-sm py-2" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    </div>

                    {{-- SAMPLE --}}
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('store.products.sample') }}" class="text-decoration-none small text-success">
                            <i class="mdi mdi-download"></i> Download Sample Format
                        </a>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success text-white rounded-pill px-4 fw-bold shadow-sm">Start Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const importCat = document.getElementById('import_category');
        if (importCat) {
            importCat.addEventListener('change', function() {
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
        }
    });
</script>