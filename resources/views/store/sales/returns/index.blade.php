<x-app-layout title="Sales Returns">
    @push('styles')
    <style>
        body { font-family: 'Manrope', sans-serif; }
        
        /* Smooth hover effect for cards */
        .hover-lift { transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important; }
        
        /* Custom slim scrollbar for tables */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-keyboard-return fs-5"></i>
                        </div>
                        Sales Returns
                    </h4>
                    <p class="text-muted small mb-0 mt-1 ms-5">Manage and track all customer returns and refunds</p>
                </div>
                
                <div class="d-flex">
                    <a href="{{ route('store.sales.returns.create') }}" class="btn btn-danger rounded-pill px-4 py-2 shadow-sm fw-bold d-flex align-items-center transition-all hover-lift">
                        <i class="mdi mdi-plus-circle-outline fs-5 me-1"></i> Process New Return
                    </a>
                </div>
            </div>

            {{-- STATISTICS CARDS --}}
            <div class="row g-3 g-md-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                                <i class="mdi mdi-keyboard-return fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">Total Returns</p>
                                <h3 class="mb-0 fw-black text-dark">{{ $returns->total() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                                <i class="mdi mdi-cash-refund fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">Total Refunded</p>
                                <h3 class="mb-0 fw-black text-danger">${{ number_format($returns->sum('total_refund'), 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                                <i class="mdi mdi-calendar-month fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">This Month</p>
                                <h3 class="mb-0 fw-black text-primary">{{ $returns->filter(fn($r) => $r->created_at->isCurrentMonth())->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                                <i class="mdi mdi-clock-outline fs-3"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">Pending Review</p>
                                <h3 class="mb-0 fw-black text-warning">{{ $returns->where('status', 'pending')->count() ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                
                {{-- FILTER BAR --}}
                <div class="card-header bg-white border-bottom p-3 p-md-4 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Returns History</h6>
                            <small class="text-muted">Showing {{ $returns->total() }} records</small>
                        </div>
                    </div>
                    
                    <form method="GET" class="row g-2 align-items-center m-0">
                        <div class="col-12 col-md-6">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-magnify fs-5"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-start-0 py-2" placeholder="Search Return #, Invoice, or Customer..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-muted px-3"><i class="mdi mdi-filter-variant"></i></span>
                                <select name="reason" class="form-select bg-light py-2">
                                    <option value="">All Reasons</option>
                                    <option value="damaged" {{ request('reason') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                                    <option value="defective" {{ request('reason') == 'defective' ? 'selected' : '' }}>Defective</option>
                                    <option value="wrong-item" {{ request('reason') == 'wrong-item' ? 'selected' : '' }}>Wrong Item</option>
                                    <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow-sm">Filter</button>
                        </div>
                    </form>
                </div>

                {{-- DATA TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive custom-scrollbar">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Return #</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Invoice</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Customer</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Date</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Refund Amount</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Reason</th>
                                    <th class="pe-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-2 fw-bold">
                                            {{ $return->return_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('store.sales.orders.show', $return->sale_id) }}" class="fw-bold text-primary text-decoration-none d-flex align-items-center">
                                            <i class="mdi mdi-file-document-outline me-1"></i> {{ $return->sale->invoice_number ?? '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold fs-6 border border-primary border-opacity-25" style="width: 36px; height: 36px;">
                                                {{ substr($return->customer->name ?? 'W', 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="d-block fw-bold text-dark">{{ $return->customer->name ?? 'Walk-in Customer' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $return->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>{{ $return->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-black text-danger fs-6">
                                            ${{ number_format($return->total_refund, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill">
                                            {{ Str::limit($return->reason ?? 'N/A', 25) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                            <i class="mdi mdi-check-circle-outline me-1"></i> Completed
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-inbox-outline" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Returns Found</h6>
                                        <p class="text-muted small mb-0">Start by processing a new return.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if($returns->hasPages() || $returns->total() > 0)
                <div class="card-footer bg-white border-top p-3 rounded-bottom-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <small class="text-muted fw-medium">
                        Showing {{ $returns->firstItem() ?? 0 }} to {{ $returns->lastItem() ?? 0 }} of {{ $returns->total() }} returns
                    </small>
                    <div class="m-0">
                        {{ $returns->links() }}
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>