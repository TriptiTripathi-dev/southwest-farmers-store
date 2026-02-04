<x-app-layout title="Weekly Sales Summary">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="mb-0 fw-bold text-dark">Weekly Sales Overview</h4>
            <form method="GET" action="{{ route('store.sales.weekly') }}" class="d-flex gap-2 align-items-center bg-white p-2 rounded shadow-sm">
                <span class="small fw-bold text-muted ps-2">Range:</span>
                <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-light" value="{{ $startOfWeek }}">
                <span class="text-muted">-</span>
                <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-light" value="{{ $endOfWeek }}">
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-white-50">Total Revenue (Selected Period)</h5>
                            <h2 class="mb-0 fw-bold">${{ number_format($totalRevenue, 2) }}</h2>
                        </div>
                        <i class="mdi mdi-chart-line fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-white border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-muted">Total Orders</h5>
                            <h2 class="mb-0 fw-bold text-dark">{{ $totalOrders }}</h2>
                        </div>
                        <i class="mdi mdi-cart-check fs-1 text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold">Breakdown by Day</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th class="text-center">Orders</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Tax Collected</th>
                            <th class="text-end pe-4">Total Sales</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyStats as $stat)
                        <tr>
                            <td class="ps-4 fw-bold">
                                {{ \Carbon\Carbon::parse($stat->date)->format('l, M d, Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $stat->total_orders }}</span>
                            </td>
                            <td class="text-end text-muted">${{ number_format($stat->total_subtotal, 2) }}</td>
                            <td class="text-end text-muted">${{ number_format($stat->total_tax, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-success fs-6">${{ number_format($stat->total_sales, 2) }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('store.sales.daily', ['date' => $stat->date]) }}" class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No sales data for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>

    </script>
    @endpush
</x-app-layout>