<x-app-layout title="Create Category">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Add New Category</h4>
                        <form class="forms-sample" action="{{ route('store.categories.store') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label>Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Category Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary text-white me-2">Submit</button>
                            <a href="{{ route('store.categories.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>