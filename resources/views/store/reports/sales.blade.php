<x-app-layout title="Sales Report">
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold text-dark">Sales Report</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-printer me-1"></i> Print
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('store.reports.sales') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="all">All Methods</option>
                            <option value="cash" {{ $paymentMethod == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ $paymentMethod == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="upi" {{ $paymentMethod == 'upi' ? 'selected' : '' }}>UPI / Mobile Money</option>
                            <option value="check" {{ $paymentMethod == 'check' ? 'selected' : '' }}>Check</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                <i class="mdi mdi-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('store.reports.sales') }}" class="btn btn-light border" title="Reset">
                                <i class="mdi mdi-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-white-50 text-uppercase small fw-bold">Total Revenue</h6>
                        <h3 class="mb-0 fw-bold">${{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-white-50 text-uppercase small fw-bold">Total Orders</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalOrders) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-white border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small fw-bold">Tax Collected</h6>
                        <h3 class="mb-0 fw-bold text-dark">${{ number_format($totalTax, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-white border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase small fw-bold">Discounts Given</h6>
                        <h3 class="mb-0 fw-bold text-danger">-${{ number_format($totalDiscount, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Sales History</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Payment</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end pe-4">Total</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td class="ps-4 text-muted small">
                                {{ $sale->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="fw-bold text-primary">{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $sale->items->count() }}</span></td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success text-uppercase border border-success border-opacity-25">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="text-end">${{ number_format($sale->subtotal, 2) }}</td>
                            <td class="text-end text-muted small">${{ number_format($sale->tax_amount, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-dark">${{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('store.sales.orders.show', $sale->id) }}" class="btn btn-sm btn-light border">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="mdi mdi-receipt fs-1 opacity-25"></i>
                                <p class="mb-0">No sales records found for this period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $sales->links() }}
            </div>
        </div>

    </div>
</x-app-layout>