<x-app-layout title="Edit Category">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Edit Category</h4>
                            <span class="badge bg-warning text-dark">Local Category</span>
                        </div>

                        <form class="forms-sample" action="{{ route('store.categories.update', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-3">
                                <label>Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Category Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" value="{{ old('code', $category->code) }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary text-white me-2">Update Changes</button>
                            <a href="{{ route('store.categories.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>