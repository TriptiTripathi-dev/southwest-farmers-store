<x-app-layout title="All Orders">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="mb-0 text-dark fw-bold">Sales Orders</h4>
                    <p class="text-muted small mb-0 mt-1">Manage and view all customer transactions</p>
                </div>
                
                <div class="d-flex">
                    @if(Auth::user()->hasPermission('create_order') || Auth::user()->hasPermission('access_pos'))
                    <a href="{{ route('sales.pos') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold d-flex align-items-center transition-all">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Create New Order
                    </a>
                    @endif
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                
                {{-- FILTER BAR (Integrated into Card Header) --}}
                <div class="card-header bg-white border-bottom p-3 p-md-4 rounded-top-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-receipt-text-outline text-primary me-2"></i>Order History</h6>
                    
                    <form method="GET" action="{{ route('store.sales.orders') }}" class="m-0 w-100" style="max-width: 400px;">
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-magnify fs-5"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0 py-2 fs-6"
                                placeholder="Search Invoice or Customer..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-dark fw-bold px-4">Filter</button>
                        </div>
                    </form>
                </div>

                {{-- DATA TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Invoice No</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Customer</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Date & Time</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase text-center" style="letter-spacing: 0.5px;">Items</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Amount</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Payment</th>
                                    <th class="text-end pe-4 py-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-primary bg-primary bg-opacity-10 border border-primary border-opacity-25 px-2 py-1 rounded-2">
                                            {{ $order->invoice_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold fs-5 border border-success border-opacity-25" style="width: 42px; height: 42px;">
                                                {{ substr($order->customer->name ?? 'W', 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="d-block fw-bold text-dark">{{ $order->customer->name ?? 'Walk-in Customer' }}</span>
                                                <small class="text-muted"><i class="mdi mdi-phone-outline me-1"></i>{{ $order->customer->phone ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $order->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 fw-bold">
                                            {{ $order->items->sum('quantity') }} Items
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bolder text-success fs-6">${{ number_format($order->total_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill text-uppercase fw-bold">
                                            <i class="mdi mdi-credit-card-outline me-1"></i>{{ $order->payment_method }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if(Auth::user()->hasPermission('view_orders'))
                                            <a href="{{ route('store.sales.orders.show', $order->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" data-bs-toggle="tooltip" title="View Order">
                                                <i class="mdi mdi-eye fs-6"></i>
                                            </a>
                                            @endif
                                            
                                            @if(Auth::user()->hasPermission('process_return'))
                                            <a href="{{ route('store.sales.returns.create', ['invoice' => $order->invoice_number]) }}"
                                                class="btn btn-sm btn-light border shadow-sm text-danger"
                                                data-bs-toggle="tooltip" title="Process Return">
                                                <i class="mdi mdi-keyboard-return fs-6"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-cart-off" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Orders Found</h6>
                                        <p class="text-muted small mb-0">There are no sales orders matching your criteria.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if($orders->hasPages())
                <div class="card-footer bg-white border-top p-3 rounded-bottom-4">
                    {{ $orders->links() }}
                </div>
                @endif
                
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    @endpush
</x-app-layout>