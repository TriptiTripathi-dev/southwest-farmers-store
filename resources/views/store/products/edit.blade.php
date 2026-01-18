<x-app-layout title="Edit Product">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Edit Product</h4>
                            <span class="badge bg-info text-dark">Local Product</span>
                        </div>

                        <div class="alert alert-warning small">
                            <i class="mdi mdi-pencil"></i>
                            You are editing a <strong>Local Product</strong>. Changes will apply immediately.
                        </div>

                        <form class="forms-sample"
                              action="{{ route('store.products.update', $product->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_select" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Subcategory</label>
                                    <select name="subcategory_id" id="subcategory_select" class="form-select">
                                        <option value="">Select Subcategory</option>
                                        @foreach($subcategories as $sub)
                                            <option value="{{ $sub->id }}"
                                                {{ $product->subcategory_id == $sub->id ? 'selected' : '' }}>
                                                {{ $sub->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="product_name"
                                           class="form-control"
                                           value="{{ $product->product_name }}"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>SKU (Unique Code) <span class="text-danger">*</span></label>
                                    <input type="text" name="sku"
                                           class="form-control"
                                           value="{{ $product->sku }}"
                                           required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-success fw-bold">
                                        Selling Price (₹) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01"
                                           name="selling_price"
                                           class="form-control border-success fw-bold"
                                           value="{{ $product->selling_price }}"
                                           required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Barcode</label>
                                    <input type="text" name="barcode"
                                           class="form-control"
                                           value="{{ $product->barcode }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Unit</label>
                                    <input type="text" name="unit"
                                           class="form-control"
                                           value="{{ $product->unit ?? 'pcs' }}">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Product Image</label>
                                <input type="file" name="image" class="form-control">

                                @if($product->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$product->image) }}"
                                             class="img-thumbnail"
                                             width="120">
                                    </div>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <label>Description</label>
                                <textarea name="description"
                                          class="form-control"
                                          rows="3">{{ $product->description }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success text-white me-2">
                                Update
                            </button>

                            <a href="{{ route('store.products.index') }}" class="btn btn-light">
                                Cancel
                            </a>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('category_select').addEventListener('change', function () {
            let catId = this.value;
            let subSelect = document.getElementById('subcategory_select');

            subSelect.innerHTML = '<option>Loading...</option>';
            subSelect.disabled = true;

            if (catId) {
                fetch("{{ route('store.subcategories.get') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ category_id: catId })
                })
                .then(res => res.json())
                .then(data => {
                    subSelect.innerHTML = '<option value="">Select Subcategory</option>';
                    data.forEach(sub => {
                        subSelect.innerHTML +=
                            `<option value="${sub.id}">${sub.name}</option>`;
                    });
                    subSelect.disabled = false;
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
