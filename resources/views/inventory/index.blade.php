<x-app-layout title="Store Stock">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Store Stock</h4>
                    <p class="text-muted small mb-0 mt-1">Real-time overview of available inventory</p>
                </div>
                
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('inventory.index') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="mdi mdi-magnify text-muted"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Search by product name or SKU...">
                            <button type="submit" class="btn btn-dark">Search</button>
                            @if(request('search'))
                                <a href="{{ route('inventory.index') }}" class="btn btn-light border">
                                    <i class="mdi mdi-close"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">PRODUCT</th>
                                    <th class="py-3 text-muted small fw-bold">CATEGORY</th>
                                    <th class="py-3 text-muted small fw-bold">SUBCATEGORY</th>
                                    <th class="py-3 text-muted small fw-bold">QUANTITY</th>
                                    <th class="py-3 text-muted small fw-bold">IN TRANSIT</th>
                                    <th class="py-3 text-muted small fw-bold">STATUS</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">LAST UPDATED</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $stock)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="mdi mdi-cube-outline text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="d-block text-muted font-monospace">UPC: {{ $stock->product->barcode ?: 'N/A' }}</small>
                                                <h6 class="mb-0 fw-semibold text-dark">{{ $stock->product->product_name }}</h6>
                                                <small class="text-muted">{{ $stock->product->unit }} | SKU: {{ $stock->product->sku ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">
                                            {{ $stock->product->category->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $stock->product->subcategory->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="h6 mb-0 fw-bold">{{ $stock->quantity }}</span>
                                    </td>
                                    <td>
                                        <span class="h6 mb-0 fw-bold text-primary">{{ (int) ($inTransitByProduct[$stock->product_id] ?? 0) }}</span>
                                    </td>
                                    <td>
                                        @php $inTransitQty = (int) ($inTransitByProduct[$stock->product_id] ?? 0); @endphp
                                        @if($stock->quantity <= 0 && $inTransitQty > 0)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 rounded-pill">In Transit</span>
                                        @elseif($stock->quantity > 10)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 rounded-pill">In Stock</span>
                                        @elseif($stock->quantity > 0)
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 rounded-pill">Low Stock</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 rounded-pill">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end text-muted small">
                                        {{ $stock->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mdi mdi-package-variant-closed fs-1 d-block mb-2 opacity-50"></i>
                                            <p class="mb-0">No stock found in your inventory.</p>
                                            <small>Go to the product catalog to request items.</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    {{ $stocks->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
