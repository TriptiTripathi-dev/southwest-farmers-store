<x-app-layout title="Request Details">
    <div class="content-wrapper">
        <div class="container-fluid">
            
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('inventory.requests') }}">Requests</a></li>
                            <li class="breadcrumb-item active">#REQ-{{ $stockRequest->id }}</li>
                        </ol>
                    </nav>
                    <h4 class="mb-0 fw-bold">Request Details</h4>
                </div>
                <a href="{{ route('inventory.requests') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
            </div>

            <div class="row">
                {{-- LEFT COLUMN: INFO --}}
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="card-title mb-0 fw-bold">Item Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-light rounded p-3 me-3">
                                    <i class="mdi mdi-package-variant-closed fs-2 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $stockRequest->product->product_name }}</h5>
                                    <p class="text-muted mb-0 small">SKU: {{ $stockRequest->product->sku }} | Unit: {{ $stockRequest->product->unit }}</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light text-center">
                                        <small class="text-muted fw-bold d-block mb-1">REQUESTED</small>
                                        <h4 class="fw-bold text-dark mb-0">{{ $stockRequest->requested_quantity }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light text-center">
                                        <small class="text-muted fw-bold d-block mb-1">DISPATCHED</small>
                                        <h4 class="fw-bold text-info mb-0">{{ $stockRequest->fulfilled_quantity ?? 0 }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light text-center">
                                        <small class="text-muted fw-bold d-block mb-1">CURRENT STATUS</small>
                                        @php
                                            $badges = [
                                                'pending' => 'bg-warning text-dark',
                                                'dispatched' => 'bg-info text-white',
                                                'rejected' => 'bg-danger text-white',
                                                'completed' => 'bg-success text-white'
                                            ];
                                            $label = $stockRequest->status == 'dispatched' ? 'In Transit' : ucfirst($stockRequest->status);
                                        @endphp
                                        <span class="badge {{ $badges[$stockRequest->status] ?? 'bg-secondary' }} px-3 py-2">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PAYMENT PROOF SECTION (If In Transit or Completed) --}}
                    @if(in_array($stockRequest->status, ['dispatched', 'completed']))
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">Payment & Verification</h6>
                            @if($stockRequest->status == 'dispatched')
                                <button class="btn btn-sm btn-success text-white" onclick="openPaymentModal({{ $stockRequest->id }})">
                                    <i class="mdi mdi-upload me-1"></i> Upload/Update Proof
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small text-muted fw-bold text-uppercase">Your Payment Proof</label>
                                    @if($stockRequest->store_payment_proof)
                                        <div class="mt-2 p-3 border rounded">
                                            <a href="{{ asset('storage/'.$stockRequest->store_payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="mdi mdi-file-document me-1"></i> View Uploaded File
                                            </a>
                                            <div class="mt-2 small text-muted"><strong>Remarks:</strong> {{ $stockRequest->store_remarks }}</div>
                                        </div>
                                    @else
                                        <div class="mt-2 p-3 border rounded bg-light text-center text-muted small">
                                            Not uploaded yet.
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small text-muted fw-bold text-uppercase">Warehouse Verification</label>
                                    @if($stockRequest->warehouse_payment_proof)
                                        <div class="mt-2 p-3 border rounded">
                                            <a href="{{ asset('storage/'.$stockRequest->warehouse_payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-success w-100">
                                                <i class="mdi mdi-check-circle me-1"></i> View Warehouse Receipt
                                            </a>
                                            <div class="mt-2 small text-muted"><strong>Notes:</strong> {{ $stockRequest->warehouse_remarks }}</div>
                                        </div>
                                    @else
                                        <div class="mt-2 p-3 border rounded bg-light text-center text-muted small">
                                            Pending Verification.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- RIGHT COLUMN: TIMELINE --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="card-title mb-0 fw-bold">Timeline</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0 position-relative" style="border-left: 2px solid #e9ecef; margin-left: 10px; padding-left: 20px;">
                                <li class="mb-4 position-relative">
                                    <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -26px; top: 5px;"></div>
                                    <h6 class="fw-bold mb-1">Request Created</h6>
                                    <small class="text-muted">{{ $stockRequest->created_at->format('M d, Y h:i A') }}</small>
                                </li>

                                @if($stockRequest->status != 'pending')
                                    <li class="mb-4 position-relative">
                                        <div class="position-absolute {{ $stockRequest->status == 'rejected' ? 'bg-danger' : 'bg-info' }} rounded-circle" style="width: 12px; height: 12px; left: -26px; top: 5px;"></div>
                                        <h6 class="fw-bold mb-1">{{ $stockRequest->status == 'rejected' ? 'Rejected' : 'Dispatched' }}</h6>
                                        <small class="text-muted">
                                            {{ $stockRequest->updated_at->format('M d, Y h:i A') }}
                                            @if($stockRequest->admin_note)
                                                <br><span class="text-dark fst-italic">"{{ $stockRequest->admin_note }}"</span>
                                            @endif
                                        </small>
                                    </li>
                                @endif

                                @if($stockRequest->status == 'completed')
                                    <li class="position-relative">
                                        <div class="position-absolute bg-success rounded-circle" style="width: 12px; height: 12px; left: -26px; top: 5px;"></div>
                                        <h6 class="fw-bold mb-1">Completed</h6>
                                        <small class="text-muted">{{ $stockRequest->verified_at ? $stockRequest->verified_at->format('M d, Y h:i A') : 'Verified' }}</small>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT PROOF MODAL --}}
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
                        Upload proof of payment/receipt for the warehouse to verify.
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
                    <button type="submit" class="btn btn-success text-white">Upload</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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