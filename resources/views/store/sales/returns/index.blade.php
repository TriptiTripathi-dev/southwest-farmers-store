<x-app-layout title="Sales Returns">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="mdi mdi-keyboard-return text-danger me-2"></i>Sales Returns
                </h2>
                <p class="text-muted mb-0">Manage and track all customer returns</p>
            </div>
            <a href="{{ route('store.sales.returns.create') }}" class="btn btn-danger btn-lg">
                <i class="mdi mdi-plus-circle me-2"></i>Process New Return
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Total Returns</p>
                                <h4 class="fw-bold text-dark mb-0">{{ $returns->total() }}</h4>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-2 rounded">
                                <i class="mdi mdi-keyboard-return text-danger fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Total Refunded</p>
                                <h4 class="fw-bold text-danger mb-0">${{ number_format($returns->sum('total_refund'), 2) }}</h4>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-2 rounded">
                                <i class="mdi mdi-cash-multiple text-danger fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">This Month</p>
                                <h4 class="fw-bold text-primary mb-0">{{ $returns->filter(fn($r) => $r->created_at->isCurrentMonth())->count() }}</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-2 rounded">
                                <i class="mdi mdi-calendar text-primary fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Pending Review</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $returns->where('status', 'pending')->count() ?? 0 }}</h4>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-2 rounded">
                                <i class="mdi mdi-clock-outline text-warning fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter/Search Section -->
        <div class="row mb-4 g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="mdi mdi-magnify text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Search by Return #, Invoice, or Customer...">
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select">
                    <option value="">All Reasons</option>
                    <option value="damaged">Damaged</option>
                    <option value="defective">Defective</option>
                    <option value="wrong-item">Wrong Item</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <!-- Returns Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Returns History</h5>
                    <small class="text-muted">{{ $returns->total() }} total returns</small>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 fw-bold text-uppercase small">Return #</th>
                            <th class="fw-bold text-uppercase small">Invoice</th>
                            <th class="fw-bold text-uppercase small">Customer</th>
                            <th class="fw-bold text-uppercase small">Date</th>
                            <th class="fw-bold text-uppercase small">Refund Amount</th>
                            <th class="fw-bold text-uppercase small">Reason</th>
                            <th class="text-center fw-bold text-uppercase small">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                        <tr class="border-bottom">
                            <td class="ps-4">
                                <span class="badge bg-danger bg-opacity-20  fw-bold">
                                    <i class="mdi mdi-keyboard-return me-1"></i>{{ $return->return_no }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('store.sales.orders.show', $return->sale_id) }}" 
                                   class="text-primary text-decoration-none fw-500">
                                    <i class="mdi mdi-file-document-outline me-1"></i>{{ $return->sale->invoice_number ?? '-' }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar bg-primary bg-opacity-20 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <small class="fw-bold">{{ substr($return->customer->name ?? 'W', 0, 1) }}</small>
                                    </div>
                                    <span>{{ $return->customer->name ?? 'Walk-in Customer' }}</span>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="mdi mdi-calendar-outline me-1"></i>{{ $return->created_at->format('M d, Y') }}
                                </small>
                            </td>
                            <td>
                                <span class="fw-bold text-danger">
                                    <i class="mdi mdi-cash me-1"></i>${{ number_format($return->total_refund, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ Str::limit($return->reason ?? 'N/A', 25) }}
                                </span>
                            </td>
                            <td class="text-center">
                               
                                        <span class="badge bg-success bg-opacity-20 ">
                                            <i class="mdi mdi-check-circle me-1"></i>Completed
                                        </span>
                                  
                              
                            </td>
                           
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="mdi mdi-inbox-outline display-4 text-muted"></i>
                                </div>
                                <h5 class="text-muted">No returns found</h5>
                                <p class="text-muted small mb-0">Start by processing a new return</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-light border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Showing {{ $returns->firstItem() ?? 0 }} to {{ $returns->lastItem() ?? 0 }} of {{ $returns->total() }} returns
                    </small>
                    {{ $returns->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        
    </script>
    @endpush
</x-app-layout>