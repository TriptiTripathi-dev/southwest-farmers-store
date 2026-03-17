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
                <div class="card-body p-0">
                    <ul class="nav nav-pills bg-light rounded-2 p-1">
                        <li class="nav-item flex-fill">
                            <a class="nav-link {{ !request('type') ? 'active fw-bold' : 'text-muted' }}" href="{{ route('inventory.index') }}">
                                <i class="mdi mdi-view-list me-1"></i> All Stock
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link {{ request('type') == 'weight' ? 'active fw-bold' : 'text-muted' }}" href="{{ route('inventory.index', ['type' => 'weight']) }}">
                                <i class="mdi mdi-scale me-1"></i> Free Weight (Bulk)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-lg-6 col-md-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-2 p-md-3">
                            <form action="{{ route('inventory.index') }}" method="GET">
                                @if(request('type'))
                                    <input type="hidden" name="type" value="{{ request('type') }}">
                                @endif
                                <div class="input-group input-group-lg border rounded-pill overflow-hidden bg-white shadow-sm">
                                    <span class="input-group-text bg-white border-0 ps-4">
                                        <i class="mdi mdi-magnify text-success fs-4"></i>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                           class="form-control border-0 bg-white fs-6 py-3" 
                                           placeholder="Search products or UPC...">
                                    @if(request('search'))
                                        <a href="{{ route('inventory.index') }}" class="btn btn-link text-muted border-0 py-3 px-3">
                                            <i class="mdi mdi-close fs-4"></i>
                                        </a>
                                    @endif
                                    <button type="submit" class="btn btn-success px-4 px-md-5 fw-bold text-uppercase small">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold" style="min-width: 250px;">UPC / PRODUCT</th>
                                    <th class="py-3 text-muted small fw-bold">CATEGORY</th>
                                    <th class="py-3 text-muted small fw-bold">SUBCATEGORY</th>
                                    <th class="py-3 text-muted small fw-bold">QUANTITY</th>
                                    <th class="py-3 text-muted small fw-bold">IN TRANSIT</th>
                                    <th class="py-3 text-muted small fw-bold">STATUS</th>
                                    <th class="py-3 text-center text-muted small fw-bold">ACTIONS</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">LAST UPDATED</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $stock)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded bg-success-subtle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px;">
                                                <i class="mdi mdi-barcode text-success fs-4"></i>
                                            </div>
                                            <div>
                                                <small class="d-block text-muted font-monospace">UPC: {{ $stock->product->upc ?? '-' }}</small>
                                                <h6 class="mb-0 fw-semibold text-dark">{{ $stock->product->product_name }}</h6>
                                                <small class="text-muted">{{ $stock->product->unit }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium">
                                            {{ $stock->product->category->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            {{ $stock->product->subcategory->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="h6 mb-0 fw-bold">{{ $stock->quantity }}</span>
                                    </td>
                                    <td>
                                        <span class="h6 mb-0 fw-bold text-success">{{ (int) ($inTransitByProduct[$stock->product_id] ?? 0) }}</span>
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
                                    <td class="text-center">
                                        @if($stock->product->unit_type == 'weight')
                                        <button class="btn btn-sm btn-outline-success fw-bold px-3 rounded-pill convert-weight-btn" 
                                                data-id="{{ $stock->id }}"
                                                data-name="{{ $stock->product->product_name }}"
                                                data-qty="{{ $stock->quantity }}"
                                                data-unit="{{ $stock->product->unit }}"
                                                data-options="{{ json_encode($stock->product->weight_options) }}">
                                            <i class="mdi mdi-swap-horizontal me-1"></i> Convert
                                        </button>
                                        @else
                                        <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end text-muted small">
                                        {{ $stock->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.convert-weight-btn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let qty = $(this).data('qty');
                let unit = $(this).data('unit');
                
                $('#conv_stock_id').val(id);
                $('#convName').text(name);
                $('#convCurrQty').text(qty);
                $('#convUnit').text(unit);
                $('#sourceUnitLabel').text(unit);
                $('#sourceQty').val('').attr('max', qty);
                $('#resultingQty').val('');
                
                $('#conversionModal').modal('show');
            });

            $('#targetUnit, #sourceQty').on('change input', function() {
                let sourceVal = parseFloat($('#sourceQty').val()) || 0;
                let targetType = $('#targetUnit').val();
                let resulting = 0;
                let ratio = 1;

                if (targetType === 'Bags (50kg)') {
                    ratio = 50;
                    resulting = sourceVal / 50;
                    $('#resultingQty').parent().find('.input-group-text').text('Bags');
                } else if (targetType === 'Units (1kg)') {
                    ratio = 1;
                    resulting = sourceVal;
                    $('#resultingQty').parent().find('.input-group-text').text('Units');
                }
                
                $('#resultingQty').val(resulting.toFixed(2));
                if (targetType) {
                    $('#convRatioHint').text('Conversion Ratio: 1 Unit per ' + ratio + ' ' + $('#convUnit').text());
                } else {
                    $('#convRatioHint').text('');
                }
            });
        });
    </script>
    @endpush

    {{-- CONVERSION MODAL --}}
    <div class="modal fade" id="conversionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold" id="conversionModalLabel">Weight to Unit Conversion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="conversionForm" method="POST" action="{{ route('inventory.convert') }}">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="stock_id" id="conv_stock_id">
                        
                        <div class="alert alert-info border-0 shadow-none rounded-3 mb-4">
                            <div class="d-flex">
                                <i class="mdi mdi-information-outline fs-4 me-3"></i>
                                <div>
                                    <p class="mb-1 fw-bold" id="convName"></p>
                                    <p class="mb-0 small">Current Stock: <span class="fw-bold" id="convCurrQty"></span> <span id="convUnit"></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Conversion Type</label>
                            <select name="target_unit" id="targetUnit" class="form-select bg-light border-0 py-2 rounded-3" required>
                                <option value="">Select Packaging --</option>
                                <option value="Bags (50kg)">Bags (50kg)</option>
                                <option value="Units (1kg)">Units (1kg)</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Source Qty to Convert</label>
                                <div class="input-group">
                                    <input type="number" name="source_qty" id="sourceQty" class="form-control border-0 bg-light py-2 rounded-start-3" placeholder="0.00" step="0.01" required>
                                    <span class="input-group-text border-0 bg-light rounded-end-3" id="sourceUnitLabel">kg</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Resulting Units</label>
                                <div class="input-group">
                                    <input type="number" name="resulting_qty" id="resultingQty" class="form-control border-0 bg-light py-2 rounded-start-3" placeholder="0" readonly>
                                    <span class="input-group-text border-0 bg-light rounded-end-3">Bags</span>
                                </div>
                                <small class="text-info mt-1 d-block" id="convRatioHint"></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3">
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">Confirm Conversion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
