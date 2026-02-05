<x-app-layout title="All Orders">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 text-dark fw-bold">Sales Orders</h4>
                <small class="text-muted">Manage and view all customer orders</small>
            </div>
            <div>
                <a href="{{ route('sales.pos') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus me-1"></i> Create New Order
                </a>
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('store.sales.orders') }}" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Search by Invoice # or Customer..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Invoice No</th>
                                <th>Customer</th>
                                <th>Date & Time</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Payment</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">
                                    {{ $order->invoice_number }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2 text-success fw-bold">
                                            {{ substr($order->customer->name ?? 'W', 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold text-dark">{{ $order->customer->name ?? 'Walk-in Customer' }}</span>
                                            <small class="text-muted">{{ $order->customer->phone ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-dark">{{ $order->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        {{ $order->items->sum('quantity') }} Items
                                    </span>
                                </td>
                                <td class="fw-bold text-success">
                                    ${{ number_format($order->total_amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge border border-secondary text-secondary text-uppercase">
                                        {{ $order->payment_method }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('store.sales.orders.show', $order->id) }}" class="btn btn-sm btn-light border me-1" title="View">
                                        <i class="mdi mdi-eye"></i>
                                    </a>

                                    <a href="{{ route('store.sales.returns.create', ['invoice' => $order->invoice_number]) }}"
                                        class="btn btn-sm btn-soft-danger"
                                        title="Process Return">
                                        <i class="mdi mdi-keyboard-return"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="mdi mdi-cart-off" style="font-size: 3rem;"></i>
                                        <p class="mt-2">No orders found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white py-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>