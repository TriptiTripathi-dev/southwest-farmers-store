<x-app-layout title="Store Stock">
    @push('styles')
    <style>
        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 1.25rem;
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.05) !important;
        }
        .action-bar {
            background: #fff;
            border-radius: 100px;
            padding: 0.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
        }
        .search-input-group {
            background: #f8fafc;
            border-radius: 100px;
            padding: 0.25rem 0.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .search-input-group:focus-within {
            background: #fff;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 4px rgba(1, 153, 52, 0.1);
        }
        .nav-pills-premium .nav-link {
            border-radius: 100px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.3s ease;
        }
        .nav-pills-premium .nav-link.active {
            background: var(--bs-primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(1, 153, 52, 0.2);
        }
        .stock-table thead th {
            background: #f8fafc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            border-top: none;
            padding: 1.25rem 1rem;
        }
        .product-icon {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bs-primary);
            font-size: 1.5rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.75rem;
        }
        @media (max-width: 991.98px) {
            .stock-table thead { display: none; }
            .stock-table tbody tr {
                display: block;
                margin-bottom: 1.5rem;
                background: white;
                border-radius: 1rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                padding: 1rem;
                border: 1px solid #f1f5f9;
            }
            .stock-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 0;
                border-bottom: 1px solid #f1f5f9;
                text-align: right;
            }
            .stock-table tbody td:last-child { border-bottom: none; }
            .stock-table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                font-size: 0.7rem;
            }
            .stock-table tbody td.product-cell {
                display: block;
                text-align: left;
                border-bottom: 1px solid #e2e8f0;
                padding-bottom: 1rem;
                margin-bottom: 0.5rem;
            }
            .stock-table tbody td.product-cell::before { display: none; }
        }
    </style>
    @endpush

    <div class="content">
        <div class="container-fluid px-lg-4">
            {{-- Header & Stats --}}
            <div class="row align-items-center py-4 g-3">
                <div class="col-md-4">
                    <h4 class="h3 fw-black m-0 text-dark">Store Stock</h4>
                    <p class="text-muted mb-0">Manage and monitor your local inventory</p>
                </div>
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="stats-card p-3 shadow-sm text-center">
                                <div class="text-primary fs-4 mb-1"><i class="mdi mdi-package-variant"></i></div>
                                <h4 class="fw-bold mb-0">{{ $totalSku }}</h4>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Total SKU</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card p-3 shadow-sm text-center">
                                <div class="text-danger fs-4 mb-1"><i class="mdi mdi-alert-circle-outline"></i></div>
                                <h4 class="fw-bold mb-0 text-danger">{{ $outOfStockCount }}</h4>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Out of Stock</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-card p-3 shadow-sm text-center">
                                <div class="text-info fs-4 mb-1"><i class="mdi mdi-truck-delivery-outline"></i></div>
                                <h4 class="fw-bold mb-0 text-info">{{ number_format($totalInTransit) }}</h4>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">In Transit</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Bar --}}
            <div class="action-bar mb-4 px-3 py-2">
                <div class="row align-items-center g-3">
                    <div class="col-lg-4">
                        <ul class="nav nav-pills nav-pills-premium bg-light p-1 rounded-pill w-100">
                            <li class="nav-item flex-fill">
                                <a class="nav-link text-center {{ !request('type') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                                    All Stock
                                </a>
                            </li>
                            <li class="nav-item flex-fill">
                                <a class="nav-link text-center {{ request('type') == 'weight' ? 'active' : '' }}" href="{{ route('inventory.index', ['type' => 'weight']) }}">
                                    Bulk Weight
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-8">
                        <form action="{{ route('inventory.index') }}" method="GET">
                            @if(request('type'))
                                <input type="hidden" name="type" value="{{ request('type') }}">
                            @endif
                            <div class="search-input-group d-flex align-items-center">
                                <i class="mdi mdi-magnify fs-4 text-muted ms-2"></i>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       class="form-control border-0 bg-transparent shadow-none py-2" 
                                       placeholder="Search by product name, SKU, or UPC...">
                                @if(request('search'))
                                    <a href="{{ route('inventory.index') }}" class="btn btn-link text-muted p-1 me-1">
                                        <i class="mdi mdi-close-circle fs-5"></i>
                                    </a>
                                @endif
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold text-uppercase ms-2 d-none d-md-block">
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 stock-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Product Details</th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Quantity</th>
                                    <th>In Transit</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                    <th class="pe-4 text-end">Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $stock)
                                <tr>
                                    <td class="ps-4 py-3 product-cell">
                                        <div class="d-flex align-items-center">
                                            <div class="product-icon me-3">
                                                <i class="mdi mdi-package-variant-closed"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $stock->product->product_name }}</h6>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="badge bg-light text-muted fw-medium font-monospace" style="font-size: 0.65rem;">
                                                        UPC: {{ $stock->product->upc ?? '-' }}
                                                    </span>
                                                    <span class="text-muted small">{{ $stock->product->unit }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Category">
                                        <span class="fw-semibold text-dark">
                                            {{ $stock->product->category->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td data-label="Subcategory">
                                        <span class="text-muted">
                                            {{ $stock->product->subcategory->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td data-label="Quantity">
                                        <span class="h5 mb-0 fw-black {{ $stock->quantity <= 0 ? 'text-danger' : 'text-dark' }}">
                                            {{ number_format($stock->quantity) }}
                                        </span>
                                    </td>
                                    <td data-label="In Transit">
                                        <span class="h6 mb-0 fw-bold text-info">
                                            <i class="mdi mdi-arrow-right-bold-circle-outline me-1"></i>
                                            {{ (int) ($inTransitByProduct[$stock->product_id] ?? 0) }}
                                        </span>
                                    </td>
                                    <td data-label="Status">
                                        @php $inTransitQty = (int) ($inTransitByProduct[$stock->product_id] ?? 0); @endphp
                                        @if($stock->quantity <= 0 && $inTransitQty > 0)
                                            <span class="status-badge bg-info bg-opacity-10 text-info">In Transit</span>
                                        @elseif($stock->quantity > 10)
                                            <span class="status-badge bg-success bg-opacity-10 text-success">In Stock</span>
                                        @elseif($stock->quantity > 0)
                                            <span class="status-badge bg-warning bg-opacity-10 text-warning">Low Stock</span>
                                        @else
                                            <span class="status-badge bg-danger bg-opacity-10 text-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-label="Actions">
                                        @if($stock->product->unit_type == 'weight')
                                        <button class="btn btn-sm btn-outline-primary fw-bold px-3 rounded-pill convert-weight-btn" 
                                                data-id="{{ $stock->id }}"
                                                data-name="{{ $stock->product->product_name }}"
                                                data-qty="{{ $stock->quantity }}"
                                                data-unit="{{ $stock->product->unit }}"
                                                data-options="{{ json_encode($stock->product->weight_options) }}">
                                            <i class="mdi mdi-swap-horizontal me-1"></i> Convert
                                        </button>
                                        @else
                                        <span class="text-muted small italic">Fixed Unit</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end text-muted small" data-label="Last Update">
                                        {{ $stock->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="product-icon mx-auto mb-3" style="width: 80px; height: 80px;">
                                                <i class="mdi mdi-package-variant-closed fs-1"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark">No products found</h5>
                                            <p class="text-muted">Your active inventory search returned no results.</p>
                                            <a href="{{ route('inventory.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold">Clear Filters</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($stocks->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-center">
                        {{ $stocks->links() }}
                    </div>
                </div>
                @endif
            </div>
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
            <div class="modal-content border-0 shadow-lg rounded-[2rem]">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-black text-dark fs-4" id="conversionModalLabel">Weight Conversion</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form id="conversionForm" method="POST" action="{{ route('inventory.convert') }}">
                    @csrf
                    <div class="modal-body p-4 pt-2">
                        <input type="hidden" name="stock_id" id="conv_stock_id">
                        
                        <div class="stats-card p-4 mb-4 border-0 shadow-sm" style="background: var(--bs-primary-bg-subtle);">
                            <div class="d-flex align-items-center">
                                <div class="product-icon bg-white text-primary rounded-circle me-3" style="width: 50px; height: 50px;">
                                    <i class="mdi mdi-scale"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-black text-dark" id="convName"></h6>
                                    <p class="mb-0 text-muted small">Available: <span class="fw-bold text-primary" id="convCurrQty"></span> <span id="convUnit" class="fw-bold text-primary"></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-black text-uppercase text-muted letter-spacing-1">Conversion Target</label>
                            <div class="input-group search-input-group border">
                                <span class="input-group-text bg-transparent border-0"><i class="mdi mdi-package-variant text-primary"></i></span>
                                <select name="target_unit" id="targetUnit" class="form-select border-0 bg-transparent shadow-none" required>
                                    <option value="">Select Packaging Type</option>
                                    <option value="Bags (50kg)">Bags (50kg)</option>
                                    <option value="Units (1kg)">Units (1kg)</option>
                                    <option value="Custom">Custom Packaging</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-black text-uppercase text-muted letter-spacing-1">Bulk Qty</label>
                                <div class="search-input-group border d-flex align-items-center">
                                    <input type="number" name="source_qty" id="sourceQty" class="form-control border-0 bg-transparent shadow-none" placeholder="0.00" step="0.01" required>
                                    <span class="badge bg-light text-dark me-2" id="sourceUnitLabel">kg</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-black text-uppercase text-muted letter-spacing-1">Resulting</label>
                                <div class="search-input-group border d-flex align-items-center bg-light">
                                    <input type="number" name="resulting_qty" id="resultingQty" class="form-control border-0 bg-transparent shadow-none" placeholder="0" readonly>
                                    <span class="badge bg-primary-subtle text-primary me-2">Packs</span>
                                </div>
                            </div>
                        </div>
                        <div id="convRatioHint" class="text-center mt-3 p-2 rounded-pill bg-light text-primary small fw-semibold d-none"></div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none me-auto" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-black shadow-lg">
                            Process Conversion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
