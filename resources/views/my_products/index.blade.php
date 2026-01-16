<x-app-layout title="My Products">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Store Inventory</h4>
            <a href="{{ route('my-products.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Add / Import Product
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Product</th>
                            <th>Type</th> <th>SKU</th>
                            <th>My Stock</th>
                            <th>My Price</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/'.$stock->product->image) }}" 
                                         class="rounded" width="40" height="40" alt="img"
                                         onerror="this.src='https://via.placeholder.com/40'">
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ $stock->product->product_name }}</h6>
                                        <small class="text-muted">{{ $stock->product->category->name ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($stock->product->is_global)
                                    <span class="badge bg-info">Global</span>
                                @else
                                    <span class="badge bg-warning text-dark">Local</span>
                                @endif
                            </td>
                            <td>{{ $stock->product->sku }}</td>
                            <td>
                                <span class="fw-bold {{ $stock->quantity < 5 ? 'text-danger' : 'text-success' }}">
                                    {{ $stock->quantity }}
                                </span>
                            </td>
                            <td>${{ number_format($stock->selling_price, 2) }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('my-products.edit', $stock->id) }}" class="btn btn-sm btn-light border">
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-3">
                    {{ $stocks->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>