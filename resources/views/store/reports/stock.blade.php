<x-app-layout title="Stock Inventory Report">
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold text-dark">Stock Inventory Report</h4>
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                <i class="mdi mdi-printer me-1"></i> Print Report
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Total Inventory Value</h6>
                                <h3 class="mb-0 fw-bold">₹{{ number_format($totalValue, 2) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 p-2 rounded">
                                <i class="mdi mdi-cash-multiple fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50">Total Quantity</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($totalQty) }} Units</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 p-2 rounded">
                                <i class="mdi mdi-package-variant fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted">Unique Products</h6>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalItems }}</h3>
                            </div>
                            <div class="bg-light p-2 rounded text-dark">
                                <i class="mdi mdi-barcode fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('store.reports.stock') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search Product Name or SKU..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="all">All Categories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="in" {{ request('status') == 'in' ? 'selected' : '' }}>In Stock</option>
                            <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Product Name</th>
                                <th>Department</th>
                                <th>Category</th>
                                <th>SKU</th>
                                <th class="text-center">Current Stock</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Value</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocks as $stock)
                            @php
                            $stockValue = $stock->quantity * $stock->price;
                            $minLevel = $stock->min_stock_level ?? 5; // Default 5 if not set
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $stock->product_name }}</td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                        {{ $stock->product->department->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $stock->category_name ?? 'Uncategorized' }}</span></td>
                                <td class="text-muted small">{{ $stock->sku }}</td>
                                <td class="text-center fw-bold">{{ $stock->quantity }}</td>
                                <td class="text-end">₹{{ number_format($stock->price, 2) }}</td>
                                <td class="text-end fw-bold text-success">₹{{ number_format($stockValue, 2) }}</td>
                                <td class="text-center">
                                    @if($stock->quantity == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($stock->quantity <= $minLevel)
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                        @else
                                        <span class="badge bg-success">In Stock</span>
                                        @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-package-variant mb-2 fs-1"></i>
                                    <p>No stock records found matching your filters.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $stocks->withQueryString()->links() }}
            </div>
        </div>

    </div>
</x-app-layout>