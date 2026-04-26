<x-app-layout title="Order Details">
    <div class="content-wrapper">
        <div class="container-fluid">
            
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('inventory.requests') }}">Order Inventory</a></li>
                            <li class="breadcrumb-item active">{{ $stockRequest->request_number }}</li>
                        </ol>
                    </nav>
                    <h4 class="mb-0 fw-bold text-dark">Order Details: <span class="text-primary">{{ $stockRequest->request_number }}</span></h4>
                </div>
                <div class="d-flex gap-2">
                    @if(request('action') == 'receive' && $stockRequest->status == 'dispatched')
                        <button type="submit" form="receiveForm" class="btn btn-success fw-bold text-white px-4 rounded-pill shadow-sm">
                            <i class="mdi mdi-check-circle me-1"></i> Confirm Receiving
                        </button>
                    @endif
                    <button class="btn btn-outline-info" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> Print
                    </button>
                    <a href="{{ route('inventory.requests') }}" class="btn btn-outline-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            @if(request('action') == 'receive' && $stockRequest->status == 'dispatched')
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                <i class="mdi mdi-information-outline fs-3 me-3"></i>
                <div>
                    <h6 class="mb-0 fw-bold">Receiving Inventory</h6>
                    <small>Please verify the items physically and enter the quantities received. Any discrepancies should be noted in the remarks.</small>
                </div>
            </div>
            @endif

            <div class="row">
                {{-- LEFT COLUMN: INFO --}}
                <div class="col-md-8">
                    <!-- Order Items -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">Order Items</h6>
                            <span class="badge bg-primary rounded-pill">{{ $stockRequest->items->count() }} Items</span>
                        </div>
                        <div class="card-body p-0">
                            <form action="{{ route('inventory.requests.receive', $stockRequest->id) }}" method="POST" id="receiveForm">
                                @csrf
                                @if(request('action') == 'receive')
                                <div class="p-3 bg-light border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Received By (Your Name) <span class="text-danger">*</span></label>
                                            <input type="text" name="received_by_name" class="form-control" required placeholder="Enter full name..." value="{{ Auth::user()->name }}">
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light small text-uppercase text-muted">
                                            <tr>
                                                <th class="ps-4">Product</th>
                                                <th class="text-center">Requested</th>
                                                <th class="text-center">Dispatched</th>
                                                <th class="text-center">Received</th>
                                                @if(request('action') == 'receive')
                                                <th class="text-center" style="width: 150px;">Record Now</th>
                                                @endif
                                                <th class="text-end pe-4">Unit Cost</th>
                                                <th class="text-end pe-4">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stockRequest->items as $item)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark">{{ $item->product->product_name }}</div>
                                                    <small class="text-muted font-monospace">{{ $item->product->upc }}</small>
                                                </td>
                                                <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                                <td class="text-center text-info fw-bold">{{ $item->dispatched_quantity ?? 0 }}</td>
                                                <td class="text-center text-success fw-bold">{{ $item->received_quantity ?? 0 }}</td>
                                                @if(request('action') == 'receive')
                                                <td class="text-center">
                                                    <input type="number" name="items[{{ $item->id }}][received_qty]" 
                                                           class="form-control form-control-sm text-center fw-bold border-primary" 
                                                           value="{{ ($item->dispatched_quantity ?? $item->quantity) - ($item->received_quantity ?? 0) }}" 
                                                           max="{{ ($item->dispatched_quantity ?? $item->quantity) - ($item->received_quantity ?? 0) }}" 
                                                           min="0">
                                                </td>
                                                @endif
                                                <td class="text-end pe-4">₹{{ number_format($item->unit_cost, 2) }}</td>
                                                <td class="text-end pe-4 fw-bold">₹{{ number_format($item->total_cost, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                            </form>
                                    <tfoot class="bg-light border-top">
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold py-3">Total Amount:</td>
                                            <td class="text-end pe-4 fw-bold py-3 text-primary fs-5">₹{{ number_format($stockRequest->total_amount, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Approval & Contacts -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-account-check me-2 text-success"></i>Approval Info</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="small text-muted fw-bold text-uppercase d-block">Reviewed By</label>
                                        @if($stockRequest->reviewed)
                                            <div class="d-flex align-items-center mt-1">
                                                <i class="mdi mdi-check-decagram text-success me-2 fs-4"></i>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $stockRequest->reviewedBy->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $stockRequest->reviewed_at->format('d M Y, h:i A') }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-warning small fst-italic">Pending Review</span>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="small text-muted fw-bold text-uppercase d-block">Approved By</label>
                                        @if($stockRequest->approved_at)
                                            <div class="d-flex align-items-center mt-1">
                                                <i class="mdi mdi-shield-check text-primary me-2 fs-4"></i>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $stockRequest->approvedBy->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $stockRequest->approved_at->format('d M Y, h:i A') }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">Waiting for Warehouse Approval</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-phone-outline me-2 text-info"></i>Store Contacts</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="small text-muted fw-bold text-uppercase d-block">General Manager</label>
                                        <div class="text-dark fw-bold small">{{ $stockRequest->gm_email ?? 'No email' }}</div>
                                        <div class="text-muted small">{{ $stockRequest->gm_phone ?? 'No phone' }}</div>
                                    </div>
                                    <div>
                                        <label class="small text-muted fw-bold text-uppercase d-block">Vice President</label>
                                        <div class="text-dark fw-bold small">{{ $stockRequest->vp_email ?? 'No email' }}</div>
                                        <div class="text-muted small">{{ $stockRequest->vp_phone ?? 'No phone' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: STATUS & TIMELINE --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="card-title mb-0 fw-bold">Order Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 text-center p-3 bg-light rounded">
                                <small class="text-muted fw-bold d-block mb-2 text-uppercase">Current Status</small>
                                @php
                                    $badges = [
                                        'pending' => 'bg-warning text-dark',
                                        'awaiting_approval' => 'bg-danger text-white',
                                        'dispatched' => 'bg-success text-white',
                                        'rejected' => 'bg-secondary text-white',
                                        'completed' => 'bg-success text-white'
                                    ];
                                    $label = $stockRequest->status == 'dispatched' ? 'In Transit' : ($stockRequest->status == 'awaiting_approval' ? 'Awaiting Approval' : ucfirst($stockRequest->status));
                                @endphp
                                <h4 class="badge {{ $badges[$stockRequest->status] ?? 'bg-secondary' }} px-4 py-2 rounded-pill shadow-sm">
                                    {{ $label }}
                                </h4>
                            </div>

                            <ul class="list-unstyled mb-0 position-relative" style="border-left: 2px solid #e9ecef; margin-left: 10px; padding-left: 20px;">
                                <li class="mb-4 position-relative">
                                    <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -26px; top: 5px;"></div>
                                    <h6 class="fw-bold mb-1">Order Placed</h6>
                                    <small class="text-muted">{{ $stockRequest->created_at->format('M d, Y h:i A') }}</small>
                                </li>

                                @if($stockRequest->reviewed)
                                <li class="mb-4 position-relative">
                                    <div class="position-absolute bg-success rounded-circle" style="width: 12px; height: 12px; left: -26px; top: 5px;"></div>
                                    <h6 class="fw-bold mb-1">Reviewed</h6>
                                    <small class="text-muted">{{ $stockRequest->reviewed_at->format('M d, Y h:i A') }}</small>
                                </li>
                                @endif

                                @if($stockRequest->approved_at)
                                <li class="mb-4 position-relative">
                                    <div class="position-absolute bg-info rounded-circle" style="width: 12px; height: 12px; left: -26px; top: 5px;"></div>
                                    <h6 class="fw-bold mb-1">Approved & Dispatched</h6>
                                    <small class="text-muted">{{ $stockRequest->approved_at->format('M d, Y h:i A') }}</small>
                                </li>
                                @endif

                                @if($stockRequest->status == 'completed')
                                <li class="position-relative">
                                    <div class="position-absolute bg-success rounded-circle" style="width: 12px; height: 12px; left: -26px; top: 5px;"></div>
                                    <h6 class="fw-bold mb-1">Completed</h6>
                                    <small class="text-muted">{{ $stockRequest->received_at ? $stockRequest->received_at->format('M d, Y h:i A') : 'Verified' }}</small>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    @if($stockRequest->store_remarks)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="card-title mb-0 fw-bold">Remarks</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-muted fst-italic">"{{ $stockRequest->store_remarks }}"</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .btn, .breadcrumb, .navbar, .sidebar-wrapper, .footer, .alert, .mdi-checkbox-marked-circle {
                display: none !important;
            }
            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            .bg-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(request('print'))
            window.onload = function() { window.print(); }
        @endif
    </script>
    @endpush
</x-app-layout>