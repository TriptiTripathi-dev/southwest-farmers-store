<x-app-layout title="Kitchen Inventory">
    <div class="content">
        <div class="container-fluid">

            <x-page-header>
                <button class="btn btn-dark rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#transferModal">
                    <i class="mdi mdi-swap-horizontal me-2"></i> Transfer to Kitchen
                </button>
            </x-page-header>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">PRODUCT</th>
                                    <th class="py-3 text-muted small fw-bold">QUANTITY IN KITCHEN</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">UNIT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kitchenStocks as $stock)
                                <tr>
                                    <td class="ps-4">{{ $stock->product->product_name ?? 'Unknown product' }}</td>
                                    <td>{{ rtrim(rtrim(number_format($stock->quantity, 2), '0'), '.') }}</td>
                                    <td class="pe-4 text-end text-muted">{{ $stock->unit ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        No stock has been transferred to the kitchen yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Transfer Modal --}}
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kitchen-inventory.transfer') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Transfer Stock to Kitchen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">-- Select product from store stock --</option>
                                @foreach($storeStocks as $stock)
                                    <option value="{{ $stock->product_id }}">
                                        {{ $stock->product->product_name ?? 'Unknown' }} (on shelf: {{ rtrim(rtrim(number_format($stock->quantity, 2), '0'), '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Quantity</label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                        </div>
                        <p class="text-muted small mb-0">This moves stock out of the store shelf count and into the kitchen's own inventory.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
