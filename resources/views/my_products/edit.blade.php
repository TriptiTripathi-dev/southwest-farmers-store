<x-app-layout title="Edit Product">
    <div class="container-fluid py-4">
        <div class="card shadow-sm col-md-8 mx-auto">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Product: {{ $stock->product->product_name }}</h5>
                @if($stock->product->is_global)
                    <span class="badge bg-info">Global Product</span>
                @else
                    <span class="badge bg-warning text-dark">Local Product</span>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('my-products.update', $stock->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-light border">
                        <strong>Note:</strong> 
                        @if($stock->product->is_global)
                            This is a Global product. You can only update the <u>Selling Price</u>.
                        @else
                            This is a Local product. You can update all details.
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" 
                               value="{{ $stock->product->product_name }}"
                               {{ $stock->product->is_global ? 'readonly' : '' }}>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" class="form-control fw-bold" 
                               value="{{ $stock->selling_price }}">
                    </div>

                    @if(!$stock->product->is_global)
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $stock->product->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Update Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    @endif

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">Update Product</button>
                        <a href="{{ route('my-products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>