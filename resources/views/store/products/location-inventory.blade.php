<x-app-layout title="Product Location Inventory">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Product -> Location Inventory</h4>
                    <p class="text-muted small mb-0">
                        UPC: <span class="font-monospace">{{ $product->barcode ?: 'N/A' }}</span>
                        | {{ $product->product_name }}
                    </p>
                </div>
                <a href="{{ route('store.products.index') }}" class="btn btn-light border">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Product List
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Warehouse Quantity</div>
                        <div class="h4 fw-bold mb-0 text-primary">{{ number_format($warehouseQty, 2) }}</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Category / Subcategory</div>
                        <div class="fw-semibold">{{ $product->category->name ?? 'N/A' }} / {{ $product->subcategory->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="mb-0 fw-bold">Store Visibility</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold">LOCATION</th>
                                <th class="py-3 text-muted small fw-bold">CURRENT QTY</th>
                                <th class="py-3 text-muted small fw-bold">IN TRANSIT</th>
                                <th class="py-3 text-muted small fw-bold">MIN QTY</th>
                                <th class="py-3 text-muted small fw-bold">MAX QTY</th>
                                <th class="pe-4 py-3 text-muted small fw-bold">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locationStocks as $stock)
                                @php
                                    $storeName = $stock->store->store_name ?? ('Store #' . $stock->store_id);
                                    $displayStoreName = \Illuminate\Support\Str::startsWith($storeName, 'SWF - ') ? $storeName : ('SWF - ' . $storeName);
                                    $inTransit = (int) ($inTransitByStore[$stock->store_id] ?? 0);
                                    $minQty = (int) ($stock->min_stock ?? 0);
                                    $isLow = $minQty > 0 && $stock->quantity <= $minQty;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $displayStoreName }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                    <td class="text-primary fw-bold">{{ $inTransit }}</td>
                                    <td>{{ $minQty }}</td>
                                    <td>{{ (int) ($stock->max_stock ?? 0) }}</td>
                                    <td class="pe-4">
                                        @if($stock->quantity <= 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Out of Stock</span>
                                        @elseif($isLow)
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success">In Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        No store inventory rows found for this product.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
