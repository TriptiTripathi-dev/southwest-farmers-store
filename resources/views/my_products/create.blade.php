<x-app-layout title="Add Product">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-pills card-header-pills" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="import-tab" data-bs-toggle="tab" data-bs-target="#import" type="button">
                                    <i class="mdi mdi-cloud-download me-1"></i> Import Global Product
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button">
                                    <i class="mdi mdi-plus-circle me-1"></i> Create Local Product
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            
                            <div class="tab-pane fade show active" id="import" role="tabpanel">
                                <form action="{{ route('my-products.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="import">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Select Warehouse Product</label>
                                        <select name="product_id" class="form-select" required>
                                            <option value="">-- Search Product --</option>
                                            @foreach($globalProducts as $gp)
                                                <option value="{{ $gp->id }}">{{ $gp->product_name }} (SKU: {{ $gp->sku }})</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">These are items supplied by the warehouse. You cannot edit their details.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Your Selling Price</label>
                                        <input type="number" step="0.01" name="selling_price" class="form-control" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Add to Inventory</button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="create" role="tabpanel">
                                <form action="{{ route('my-products.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="create">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Product Name</label>
                                            <input type="text" name="product_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SKU</label>
                                            <input type="text" name="sku" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" class="form-select" required>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Selling Price</label>
                                            <input type="number" step="0.01" name="selling_price" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Image</label>
                                            <input type="file" name="image" class="form-control">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success">Create Local Product</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>