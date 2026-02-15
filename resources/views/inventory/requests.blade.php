<x-app-layout title="Stock Requests">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid">

            {{-- HEADER --}}
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="fw-bold m-0 text-dark">Stock Requests</h4>
                    <p class="text-muted small mb-0 mt-1">Manage inventory replenishment from warehouse</p>
                </div>
                {{-- FIX: Added flex-wrap so buttons don't overflow on small screens --}}
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success btn-sm text-white" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="mdi mdi-file-excel"></i> Import
                    </button>
                    <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newRequestModal">
                        <i class="mdi mdi-plus me-1"></i> New Request
                    </button>
                </div>
            </div>

            {{-- TABS --}}
            {{-- FIX: Added flex-nowrap and overflow-auto so tabs can be swiped horizontally on mobile --}}
            <ul class="nav nav-tabs mb-4 border-bottom-0 flex-nowrap overflow-auto pb-1" style="white-space: nowrap; -webkit-overflow-scrolling: touch;">
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' || !request('status') ? 'active fw-bold border-bottom-0' : '' }}"
                       href="{{ route('inventory.requests', ['status' => 'pending']) }}">
                       <i class="mdi mdi-clock-outline me-1"></i> Pending 
                       <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'in_transit' ? 'active fw-bold border-bottom-0' : '' }}"
                       href="{{ route('inventory.requests', ['status' => 'in_transit']) }}">
                       <i class="mdi mdi-truck-fast me-1"></i> In Transit
                       <span class="badge bg-info ms-1">{{ $inTransitCount }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'history' ? 'active fw-bold border-bottom-0' : '' }}"
                       href="{{ route('inventory.requests', ['status' => 'history']) }}">
                       <i class="mdi mdi-history me-1"></i> History
                    </a>
                </li>
            </ul>

            {{-- MAIN CONTENT CARD --}}
            <div class="card border-0 shadow-sm rounded-3">
                {{-- FIX: Made the header responsive. Stacks on mobile, inline on desktop --}}
                <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <h6 class="mb-0 fw-bold">Request List</h6>
                    <form method="GET" action="{{ route('inventory.requests') }}" class="d-flex w-100 w-md-auto justify-content-md-end">
                        <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                        <div class="input-group input-group-sm w-100" style="max-width: 100%; width: 250px;">
                            <input type="text" name="search" class="form-control" placeholder="Search ID or Product..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="mdi mdi-magnify"></i></button>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        {{-- FIX: Added text-nowrap to prevent columns from squishing on mobile --}}
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">REQ ID</th>
                                    <th class="py-3 text-muted small fw-bold">PRODUCT</th>
                                    <th class="py-3 text-muted small fw-bold text-center">QTY</th>
                                    <th class="py-3 text-muted small fw-bold text-center">STATUS</th>
                                    <th class="py-3 text-muted small fw-bold">DATE</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $req)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $req->id }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $req->product->product_name }}</div>
                                        <div class="small text-muted font-monospace">{{ $req->product->sku }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold">{{ $req->requested_quantity }}</span> 
                                        <span class="small text-muted">{{ $req->product->unit }}</span>
                                        @if($req->fulfilled_quantity && $req->fulfilled_quantity != $req->requested_quantity)
                                            <div class="text-xs text-success">Sent: {{ $req->fulfilled_quantity }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badges = [
                                                'pending' => 'bg-warning text-dark',
                                                'dispatched' => 'bg-info text-white',
                                                'rejected' => 'bg-danger text-white',
                                                'completed' => 'bg-success text-white'
                                            ];
                                            $label = $req->status == 'dispatched' ? 'In Transit' : ucfirst($req->status);
                                        @endphp
                                        <span class="badge {{ $badges[$req->status] ?? 'bg-secondary' }} px-3 rounded-pill">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $req->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('inventory.requests.show', $req->id) }}" class="btn btn-sm btn-outline-dark me-1" title="Manage">
                                            Manage
                                        </a>

                                        @if($req->status == 'dispatched')
                                            <button onclick="openPaymentModal({{ $req->id }})" class="btn btn-sm btn-success text-white">
                                                <i class="mdi mdi-upload me-1"></i> Pay
                                            </button>
                                        @endif

                                        @if($req->status == 'pending')
                                            <form action="{{ route('inventory.requests.destroy', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this request?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="mdi mdi-clipboard-text-off fs-1 opacity-50 mb-2"></i>
                                        <p>No requests found in this category.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($requests->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $requests->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- NEW REQUEST MODAL --}}
    <div class="modal fade" id="newRequestModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('inventory.request') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">New Stock Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        {{-- Added ID for Select2 --}}
                        <select name="product_id" id="productSearchSelect" class="form-select" required style="width: 100%;">
                            <option value="">Select Product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('inventory.requests.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">Required columns: sku, quantity</div>
                    <div class="text-end mb-2">
                        <a href="{{ route('inventory.requests.sample') }}" class="small">Download Sample</a>
                    </div>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success text-white">Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- UPLOAD PAYMENT PROOF MODAL --}}
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="paymentForm" class="modal-content" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Payment Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="payment_req_id">
                    
                    <div class="alert alert-warning small">
                        <i class="mdi mdi-alert-circle me-1"></i> 
                        Your stock is in transit. Upload payment proof so the warehouse can verify and complete the order.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Proof (Image/PDF) <span class="text-danger">*</span></label>
                        <input type="file" name="store_payment_proof" class="form-control" required accept="image/*,.pdf">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks <span class="text-danger">*</span></label>
                        <textarea name="store_remarks" class="form-control" required rows="2" placeholder="Transaction ID, Bank Name, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success text-white">Upload & Notify</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the Product dropdown
            $('#productSearchSelect').select2({
                theme: 'bootstrap-5',
                placeholder: "Search product name or SKU...",
                allowClear: true,
                dropdownParent: $('#newRequestModal') // Crucial for Bootstrap modals
            });
        });

        function openPaymentModal(id) {
            document.getElementById('payment_req_id').value = id;
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch("{{ route('inventory.requests.upload_proof') }}", {
                method: "POST",
                body: formData,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire('Success', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Upload failed', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Server error', 'error'));
        });
    </script>
    @endpush
</x-app-layout>