<x-app-layout title="Stock Adjustments">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    @endpush

    <div class="content">
        <div class="container-fluid">
            
            {{-- MOBILE FIX: Added flex-column flex-md-row and gap-3 for mobile stacking --}}
            <div class="py-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h4 class="h4 fw-bold m-0 text-dark">Stock Adjustments</h4>
                    <p class="text-muted small mb-0 mt-1">Manually correct stock levels (Damage, Theft, Returns)</p>
                </div>
                
                @if(Auth::user()->hasPermission('adjust_stock'))
                <button class="btn btn-dark rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newAdjustmentModal">
                    <i class="mdi mdi-scale-balance me-2"></i> Make Adjustment
                </button>
                @endif
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">PRODUCT</th>
                                    <th class="py-3 text-muted small fw-bold">TYPE</th>
                                    <th class="py-3 text-muted small fw-bold">QUANTITY</th>
                                    <th class="py-3 text-muted small fw-bold">REASON</th>
                                    <th class="py-3 text-muted small fw-bold">ADJUSTED BY</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjustments as $adj)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-semibold text-dark">{{ $adj->product->product_name }}</div>
                                        <div class="small text-muted font-monospace">{{ $adj->product->sku }}</div>
                                    </td>
                                    <td>
                                        @if($adj->operation === 'add')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2">
                                                <i class="mdi mdi-plus-circle me-1"></i> Addition
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2">
                                                <i class="mdi mdi-minus-circle me-1"></i> Deduction
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ $adj->operation === 'add' ? 'text-success' : 'text-danger' }}">
                                            {{ $adj->operation === 'add' ? '+' : '-' }}{{ $adj->quantity }}
                                        </span>
                                        <small class="text-muted">{{ $adj->product->unit }}</small>
                                    </td>
                                    <td>
                                        {{ $adj->reason ?? '-' }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                {{ substr($adj->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span class="small">{{ $adj->user->name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end text-muted small">
                                        {{ $adj->created_at->format('M d, Y h:i A') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="mdi mdi-history opacity-50 fs-1 mb-2 d-block"></i>
                                        No adjustments have been made yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    {{ $adjustments->links() }}
                </div>
            </div>

        </div>
    </div>

    @if(Auth::user()->hasPermission('adjust_stock'))
    <div class="modal fade" id="newAdjustmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('inventory.adjustments.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Make Stock Adjustment</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning small mb-3">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> 
                            This action will directly update the live stock quantity.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Product</label>
                            {{-- Select2 ID added here --}}
                            <select name="product_id" id="adjustmentProductSelect" class="form-select" required style="width: 100%;">
                                <option value="" disabled selected>Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->product_name }} ({{ $product->sku }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Operation</label>
                                <select name="operation" class="form-select" required>
                                    <option value="subtract">Subtract (-) Damage/Loss</option>
                                    <option value="add">Add (+) Correction/Return</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="1" required placeholder="Qty">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Reason / Note</label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="e.g., Damaged during shipping"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold">Save Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- SWEETALERT AND SELECT2 SCRIPTS --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the Product dropdown inside the modal
            $('#adjustmentProductSelect').select2({
                theme: 'bootstrap-5',
                placeholder: "Search by product name or SKU...",
                allowClear: true,
                dropdownParent: $('#newAdjustmentModal') // Prevents search box from being blocked by modal
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Adjustment Failed!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
    @endpush
</x-app-layout>