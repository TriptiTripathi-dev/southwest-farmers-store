<x-app-layout title="Edit Subcategory">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Subcategory</h4>
                        <form class="forms-sample" action="{{ route('store.subcategories.update', $subcategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-3">
                                <label>Parent Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $subcategory->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label>Subcategory Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $subcategory->name }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Subcategory Code</label>
                                <input type="text" name="code" class="form-control" value="{{ $subcategory->code }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary text-white me-2">Update</button>
                            <a href="{{ route('store.subcategories.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>