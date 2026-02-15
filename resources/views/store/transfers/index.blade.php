<x-app-layout title="Stock Requests">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Fix for Select2 height inside Bootstrap form-select-lg */
        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(3.5rem + 2px);
            padding: 0.75rem 1rem;
            font-size: 1.25rem;
            border-radius: 0.5rem;
            border: 2px solid #ced4da;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            color: #212529;
        }
    </style>
    @endpush

    <div class="container-fluid px-2 px-md-4 py-4">
        
        <div class="mb-4">
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fas fa-dolly text-primary me-2"></i>Inter-Store Transfers
            </h1>
            <p class="text-muted small mb-0">Manage inventory transfers between locations</p>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-gradient bg-primary text-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="fas fa-paper-plane me-2"></i>Request Stock Transfer
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('transfers.store') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-500 text-dark">
                                <i class="fas fa-warehouse text-primary me-2"></i>Request From Store
                            </label>
                            <select name="from_store_id" id="storeSelect" class="form-select form-select-lg border-2" required style="width: 100%;">
                                <option value="">-- Select Store --</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_name }} ({{ $store->city }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-500 text-dark">
                                <i class="fas fa-box text-primary me-2"></i>Product
                            </label>
                            <select name="product_id" id="productSelect" class="form-select form-select-lg border-2" required style="width: 100%;">
                                <option value="">-- Select Product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->sku }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-2">
                            <label class="form-label fw-500 text-dark">
                                <i class="fas fa-cubes text-primary me-2"></i>Quantity
                            </label>
                            <input type="number" name="quantity" class="form-control form-control-lg border-2" min="1" placeholder="0" required>
                        </div>

                        <div class="col-12 col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-arrow-right"></i> Send Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient bg-warning border-0 py-3">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-arrow-down text-dark"></i>
                            <span>Requests Received</span>
                            <span class="badge bg-danger-subtle text-danger rounded-pill ms-auto">
                                {{ $outgoing->where('status', 'pending')->count() }}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light fw-bold text-uppercase small">
                                    <tr>
                                        <th class="px-3 py-3">ID</th>
                                        <th class="px-3 py-3">To Store</th>
                                        <th class="px-3 py-3">Product</th>
                                        <th class="px-3 py-3 text-end">Qty</th>
                                        <th class="px-3 py-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($outgoing as $t)
                                    <tr class="align-middle border-bottom">
                                        <td class="px-3 py-3">
                                            <span class="badge bg-light text-dark fw-bold">#{{ $t->transfer_number }}</span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-map-marker-alt text-danger small"></i>
                                                <span class="fw-500">{{ $t->toStore->store_name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-truncate" title="{{ $t->product->product_name ?? 'N/A' }}">
                                                {{ $t->product->product_name ?? 'N/A' }}
                                            </div>
                                            <small class="text-muted">{{ $t->product->sku ?? '' }}</small>
                                        </td>
                                        <td class="px-3 py-3 text-end">
                                            <span class="badge bg-primary-subtle text-primary fw-bold">{{ $t->quantity_sent }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($t->status == 'pending')
                                                @if(Auth::user()->hasPermission('dispatch_transfer'))
                                                {{-- CHANGED: Removed native onsubmit confirm, added class for SweetAlert --}}
                                                <form action="{{ route('transfers.dispatch', $t->id) }}" method="POST" class="d-inline dispatch-form">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1 dispatch-btn">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span class="d-none d-md-inline">Dispatch</span>
                                                    </button>
                                                </form>
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="mb-2">
                                                <i class="fas fa-inbox fa-2x opacity-50"></i>
                                            </div>
                                            <p class="mb-0">No pending requests to fulfill.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient bg-info text-white border-0 py-3">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-arrow-up"></i>
                            <span>My Sent Requests</span>
                            <span class="badge bg-light text-info rounded-pill ms-auto">
                                {{ $incoming->whereIn('status', ['pending', 'dispatched'])->count() }}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light fw-bold text-uppercase small">
                                    <tr>
                                        <th class="px-3 py-3">ID</th>
                                        <th class="px-3 py-3">From Store</th>
                                        <th class="px-3 py-3">Product</th>
                                        <th class="px-3 py-3 text-end">Qty</th>
                                        <th class="px-3 py-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($incoming as $t)
                                    <tr class="align-middle border-bottom">
                                        <td class="px-3 py-3">
                                            <span class="badge bg-light text-dark fw-bold">#{{ $t->transfer_number }}</span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-map-marker-alt text-success small"></i>
                                                <span class="fw-500">{{ $t->fromStore->store_name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-truncate" title="{{ $t->product->product_name ?? 'N/A' }}">
                                                {{ $t->product->product_name ?? 'N/A' }}
                                            </div>
                                            <small class="text-muted">{{ $t->product->sku ?? '' }}</small>
                                        </td>
                                        <td class="px-3 py-3 text-end">
                                            <span class="badge bg-info-subtle text-info fw-bold">{{ $t->quantity_sent }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($t->status == 'dispatched')
                                                @if(Auth::user()->hasPermission('receive_transfer'))
                                                <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1" onclick="openReceiveModal('{{ $t->id }}', '{{ $t->product->product_name }}', {{ $t->quantity_sent }})">
                                                    <i class="fas fa-download"></i>
                                                    <span class="d-none d-md-inline">Receive</span>
                                                </button>
                                                @else
                                                    <span class="badge bg-info">Dispatched</span>
                                                @endif
                                            @elseif($t->status == 'pending')
                                                <span class="badge bg-warning text-dark d-flex align-items-center justify-content-center gap-1">
                                                    <i class="fas fa-clock fa-xs"></i>
                                                    <span class="d-none d-md-inline">Pending</span>
                                                </span>
                                            @else
                                                <span class="badge bg-success d-flex align-items-center justify-content-center gap-1">
                                                    <i class="fas fa-check fa-xs"></i>
                                                    <span class="d-none d-md-inline">Received</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="mb-2">
                                                <i class="fas fa-send fa-2x opacity-50"></i>
                                            </div>
                                            <p class="mb-0">No active requests.</p>
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
    </div>

    <div class="modal fade" id="receiveModal" tabindex="-1" aria-labelledby="receiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="receiveForm" method="POST" class="modal-content border-0 shadow">
                @csrf
                
                <div class="modal-header bg-gradient bg-primary text-white border-0 py-3">
                    <h5 class="modal-title" id="receiveModalLabel">
                        <i class="fas fa-inbox me-2"></i>Confirm Stock Receipt
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-box text-primary"></i>
                            <label class="fw-bold text-dark mb-0">Product Receiving</label>
                        </div>
                        <p class="bg-light p-3 rounded-2 mb-0">
                            <strong id="modalProductName" class="text-primary"></strong>
                        </p>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-cubes text-info"></i>
                            <label class="fw-bold text-dark mb-0">Expected Quantity</label>
                        </div>
                        <p class="bg-light p-3 rounded-2 mb-0">
                            <strong id="modalExpectedQty" class="text-info"></strong> <span class="text-muted">units</span>
                        </p>
                    </div>

                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <label for="modalReceivedQty" class="fw-bold text-dark mb-0">Actual Received Quantity</label>
                        </div>
                        <input type="number" name="received_qty" id="modalReceivedQty" class="form-control form-control-lg border-2" min="0" required>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>Enter less than expected if there is a shortage or damage.
                        </small>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="fas fa-arrow-right"></i> Confirm & Add to Stock
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $('#storeSelect').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Search or Select Store --",
                allowClear: true
            });

            $('#productSelect').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Search or Select Product --",
                allowClear: true
            });

            // Handle SweetAlert for Dispatch buttons
            $('.dispatch-btn').on('click', function(e) {
                e.preventDefault();
                let form = $(this).closest('.dispatch-form');
                
                Swal.fire({
                    title: 'Dispatch Stock?',
                    text: 'Are you sure you want to dispatch this stock? It will be deducted from your inventory immediately.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981', // success green
                    cancelButtonColor: '#6c757d', // secondary grey
                    confirmButtonText: 'Yes, Dispatch it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        function openReceiveModal(id, productName, qty) {
            document.getElementById('modalProductName').innerText = productName;
            document.getElementById('modalExpectedQty').innerText = qty;
            document.getElementById('modalReceivedQty').value = qty;
            
            const form = document.getElementById('receiveForm');
            form.action = `/store/transfers/${id}/receive`;
            
            const modal = new bootstrap.Modal(document.getElementById('receiveModal'));
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>