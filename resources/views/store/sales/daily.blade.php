<x-app-layout title="Daily Sales Report">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold text-dark">Daily Sales</h4>
            <form method="GET" action="{{ route('store.sales.daily') }}" class="d-flex gap-2">
                <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="mdi mdi-printer"></i>
                </button>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-white-50 text-uppercase fw-bold">Total Revenue</small>
                        <h3 class="mb-0 fw-bold mt-1">${{ number_format($totalRevenue, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-white border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold">Total Orders</small>
                        <h3 class="mb-0 fw-bold mt-1 text-dark">{{ $totalOrders }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success bg-opacity-10 border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-success text-uppercase fw-bold">Cash Sales</small>
                        <h3 class="mb-0 fw-bold mt-1 text-success">${{ number_format($cashSales, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info bg-opacity-10 border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-info text-uppercase fw-bold">Digital/Card</small>
                        <h3 class="mb-0 fw-bold mt-1 text-info">${{ number_format($digitalSales, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Transactions for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Invoice #</th>
                            <th>Time</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Method</th>
                            <th class="text-end pe-4">Amount</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $sale->invoice_number }}</td>
                            <td class="text-muted">{{ $sale->created_at->format('h:i A') }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $sale->items->sum('quantity') }}</span></td>
                            <td class="text-uppercase small fw-bold">{{ $sale->payment_method }}</td>
                            <td class="text-end pe-4 fw-bold text-dark">${{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('store.sales.orders.show', $sale->id) }}" class="btn btn-sm btn-light border">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No sales found for this date.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $sales->appends(['date' => $date])->links() }}
            </div>
        </div>
    </div>
</x-app-layout>