<x-app-layout title="Product Visibility">
    <div class="content">
        <div class="container-fluid px-3 px-md-4 py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Product -> Location Inventory Visibility</h4>
                    <p class="text-muted small mb-0 mt-1">Real-time stock levels across Warehouse and all Store locations</p>
                </div>
            </div>

            <div class="row">
                {{-- SELECTION COLUMN --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom p-3">
                            <h6 class="fw-bold m-0"><i class="mdi mdi-filter-variant me-2"></i>Select Product</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Search Product</label>
                                <select id="productSelector" class="form-select select2">
                                    <option value="">-- Choose a Product --</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}"
                                            {{ $selectedProduct && $selectedProduct->id == $p->id ? 'selected' : '' }}>
                                            [{{ $p->upc ?? 'N/A' }}] {{ $p->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="productSummary" class="mt-4 d-none">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <div id="productImg"
                                        class="mb-2 mx-auto rounded-circle border bg-white d-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 80px; overflow: hidden;">
                                        <i class="mdi mdi-package-variant fs-1 text-success"></i>
                                    </div>
                                    <h6 id="summaryName" class="fw-bold mb-1"></h6>
                                    <p id="summaryUPC" class="text-muted small font-monospace mb-0"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RESULTS COLUMN --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div
                            class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold m-0"><i class="mdi mdi-map-marker-radius me-2 text-success"></i>Inventory Locations</h6>
                            <span id="loadingSpinner" class="spinner-border spinner-border-sm text-success d-none"></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 text-muted small fw-bold">LOCATION</th>
                                            <th class="py-3 text-muted small fw-bold">TYPE</th>
                                            <th class="py-3 text-center text-muted small fw-bold">QUANTITY</th>
                                            <th class="pe-4 py-3 text-center text-muted small fw-bold">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="visibilityBody">
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted opacity-50">
                                                    <i class="mdi mdi-arrow-left-bold-outline fs-1 d-block mb-2 text-success"></i>
                                                    <p class="mb-0">Please select a product from the left to see inventory
                                                        distribution.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#productSelector').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });

                $('#productSelector').on('change', function() {
                    let productId = $(this).val();
                    if (!productId) return;

                    loadVisibility(productId);
                });

                // Trigger if pre-selected
                if ($('#productSelector').val()) {
                    loadVisibility($('#productSelector').val());
                }

                function loadVisibility(id) {
                    $('#loadingSpinner').removeClass('d-none');
                    $('#visibilityBody').addClass('opacity-50');

                    $.ajax({
                        url: "{{ route('store.inventory.visibility') }}",
                        data: {
                            product_id: id
                        },
                        success: function(data) {
                            $('#loadingSpinner').addClass('d-none');
                            $('#visibilityBody').removeClass('opacity-50');

                            // Update Summary
                            $('#productSummary').removeClass('d-none');
                            $('#summaryName').text(data.product.product_name);
                            $('#summaryUPC').text('UPC: ' + (data.product.upc || 'N/A'));

                            // Build Table
                            let html = '';
                            data.locations.forEach(loc => {
                                let badgeClass = loc.quantity > 0 ? 'bg-success' : 'bg-danger';
                                html += `<tr>
                            <td class="ps-4 py-3 fw-medium text-dark">${loc.name}</td>
                            <td><span class="badge bg-light text-muted border">${loc.type}</span></td>
                            <td class="text-center fw-bold fs-5">${loc.quantity}</td>
                            <td class="pe-4 text-center">
                                <span class="badge ${badgeClass} bg-opacity-10 text-${badgeClass.split('-')[1]} border border-${badgeClass.split('-')[1]} px-3 rounded-pill">${loc.status}</span>
                            </td>
                        </tr>`;
                            });
                            $('#visibilityBody').html(html);
                        },
                        error: function() {
                            $('#loadingSpinner').addClass('d-none');
                            $('#visibilityBody').removeClass('opacity-50');
                            Swal.fire('Error', 'Failed to load inventory data', 'error');
                        }
                    });
                }
            });
        </script>
        <style>
            .select2-container--bootstrap-5 .select2-selection {
                border-radius: 10px;
                padding: 5px;
                border: 1px solid #dee2e6;
            }
        </style>
    @endpush
</x-app-layout>
