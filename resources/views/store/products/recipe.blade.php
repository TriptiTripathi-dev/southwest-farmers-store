<x-app-layout title="Manage Recipe: {{ $product->product_name }}">
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Recipe for: <span class="text-primary">{{ $product->product_name }}</span></h4>
            <small class="text-muted">Define raw materials deducted when this item is sold.</small>
        </div>
        <a href="{{ route('store.products.index') }}" class="btn btn-secondary">Back to Products</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Add Ingredient</div>
                <div class="card-body">
                    <form action="{{ route('store.products.recipe.store', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Ingredient (Raw Material)</label>
                            <select name="ingredient_id" class="form-select select2" required>
                                <option value="">Select Item...</option>
                                @foreach($allProducts as $p)
                                    <option value="{{ $p->id }}">{{ $p->product_name }} ({{ $p->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity Required</label>
                            <input type="number" name="quantity" class="form-control" step="0.001" placeholder="e.g. 1 for 1 pc, 0.250 for 250g" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add to Recipe</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">Current Ingredients</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Ingredient Name</th>
                                <th>Qty Required</th>
                                <th>Unit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->ingredients as $item)
                            <tr>
                                <td>{{ $item->ingredient->product_name }}</td>
                                <td class="fw-bold">{{ $item->quantity + 0 }}</td> {{-- +0 removes trailing zeros --}}
                                <td>{{ $item->ingredient->unit }}</td>
                                <td>
                                    <form action="{{ route('store.products.recipe.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove this ingredient?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center p-4 text-muted">No ingredients defined yet. This item acts as a standalone product.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>