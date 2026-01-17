<x-app-layout title="Edit Product">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Edit Product</h4>
                            @if($stock->product->store_id == null)
                                <span class="badge bg-info text-white">Warehouse Product</span>
                            @else
                                <span class="badge bg-warning text-dark">Local Product</span>
                            @endif
                        </div>

                        @if($stock->product->store_id == null)
                            <div class="alert alert-warning">
                                <i class="mdi mdi-lock"></i> Warehouse Product: You can only edit the <strong>Selling Price</strong>.
                            </div>
                        @endif

                        <form class="forms-sample" action="{{ route('store.products.update', $stock->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')

                            <div class="form-group mb-3">
                                <label>Product Name</label>
                                <input type="text" name="product_name" class="form-control" value="{{ $stock->product->product_name }}" 
                                       {{ $stock->product->store_id == null ? 'readonly disabled' : '' }}>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>SKU</label>
                                    <input type="text" class="form-control" value="{{ $stock->product->sku }}" readonly disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-success fw-bold">Selling Price (₹) <span class="text-danger">*</span></label>
                                    <input type="number" name="selling_price" class="form-control border-success" value="{{ $stock->selling_price }}" required>
                                </div>
                            </div>

                            @if($stock->product->store_id != null)
                                <div class="form-group mb-3">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="3">{{ $stock->product->description }}</textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control">
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary text-white me-2">Update</button>
                            <a href="{{ route('store.products.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>