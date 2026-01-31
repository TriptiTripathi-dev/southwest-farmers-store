<x-app-layout title="Store Dashboard">

    <div class="container-fluid px-4 py-4">

        {{-- 1. HEADER & DATE FILTER --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    Welcome back, {{ auth()->user()->name }} 👋
                </h4>
                <p class="text-muted mb-0 small">
                    Store Performance: <span class="fw-semibold text-primary">{{ $start->format('M d') }} - {{ $end->format('M d, Y') }}</span>
                </p>
            </div>

            <form method="GET" class="d-flex align-items-center gap-2 bg-white p-2 rounded-3 shadow-sm border">
                <div class="input-group input-group-sm">
                    <input type="date" name="start_date" class="form-control border-0 bg-light" value="{{ $start->format('Y-m-d') }}">
                    <span class="input-group-text border-0 bg-white text-muted">to</span>
                    <input type="date" name="end_date" class="form-control border-0 bg-light" value="{{ $end->format('Y-m-d') }}">
                </div>
                <button class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="mdi mdi-filter-variant me-1"></i> Filter
                </button>
            </form>
        </div>

        {{-- 2. KPI CARDS ROW --}}
        <div class="row g-3 mb-4">

            {{-- Metric: Total Revenue --}}
            @if(auth()->user()->hasPermission('view_daily_sales'))
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Total Revenue</p>
                                <h4 class="fw-bold text-dark mb-0">${{ number_format($data['total_revenue'] ?? 0, 2) }}</h4>
                            </div>
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2">
                                <i class="mdi mdi-currency-usd fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3 d-flex align-items-center small">
                            @if(($data['revenue_growth'] ?? 0) >= 0)
                                <span class="text-success fw-bold me-2"><i class="mdi mdi-arrow-up"></i> {{ $data['revenue_growth'] ?? 0 }}%</span>
                            @else
                                <span class="text-danger fw-bold me-2"><i class="mdi mdi-arrow-down"></i> {{ abs($data['revenue_growth'] ?? 0) }}%</span>
                            @endif
                            <span class="text-muted">vs previous period</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Metric: Orders Count --}}
            @if(auth()->user()->hasPermission('view_orders'))
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Total Orders</p>
                                <h4 class="fw-bold text-dark mb-0">{{ number_format($data['total_orders'] ?? 0) }}</h4>
                            </div>
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                                <i class="mdi mdi-shopping fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted">
                            Processed in selected range
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Metric: Low Stock --}}
            @if(auth()->user()->hasPermission('check_stock_levels'))
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Low Stock Alerts</p>
                                <h4 class="fw-bold text-danger mb-0">{{ $data['low_stock_count'] ?? 0 }}</h4>
                            </div>
                            <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-3 p-2">
                                <i class="mdi mdi-alert-circle-outline fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('store.stock-control.low-stock') }}" class="btn btn-xs btn-outline-danger w-100">Restock Now</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Metric: Pending Requests --}}
            @if(auth()->user()->hasPermission('request_stock'))
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.7rem;">Pending Requests</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $data['pending_requests'] ?? 0 }}</h4>
                            </div>
                            <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                                <i class="mdi mdi-truck-delivery fs-4"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('inventory.requests') }}" class="text-decoration-none small text-warning fw-bold">View Status <i class="mdi mdi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- 3. ANALYTICS & CHARTS ROW --}}
        <div class="row g-4 mb-4">
            
            {{-- Sales Trend Chart --}}
            @if(auth()->user()->hasPermission('view_daily_sales') && isset($data['chart_sales']))
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="mdi mdi-chart-line me-2 text-primary"></i> Sales Overview</h6>
                    </div>
                    <div class="card-body">
                        <div id="salesTrendChart" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Top Products --}}
            @if((auth()->user()->hasPermission('view_daily_sales') || auth()->user()->hasPermission('view_reports')) && isset($data['top_products']))
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="mdi mdi-star-circle-outline me-2 text-warning"></i> Top Selling Items</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($data['top_products'] as $index => $product)
                            <div class="list-group-item border-0 px-3 py-2 d-flex align-items-center">
                                <div class="badge bg-light text-dark me-3 rounded-circle" style="width: 25px; height: 25px; line-height: 20px;">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small fw-bold">{{ Str::limit($product->product_name, 25) }}</h6>
                                </div>
                                <div class="fw-bold text-primary small">
                                    {{ $product->qty }} Sold
                                </div>
                            </div>
                            @endforeach
                            @if(count($data['top_products']) == 0)
                                <div class="text-center py-4 text-muted small">No sales data found for this period.</div>
                            @endif
                        </div>
                        <div class="p-3 border-top mt-auto">
                            <div id="topProductsPie"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- 4. RECENT ORDERS TABLE --}}
        @if(auth()->user()->hasPermission('view_orders') && isset($data['recent_orders']))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="mdi mdi-receipt me-2"></i> Recent Orders</h6>
                <a href="{{ route('store.sales.orders') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3">Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['recent_orders'] as $order)
                        <tr>
                            <td class="ps-3 fw-bold">#{{ $order->invoice_number }}</td>
                            <td>{{ $order->customer->name ?? 'Walk-in Customer' }}</td>
                            <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success">Completed</span>
                            </td>
                            <td class="text-muted small">{{ $order->created_at->format('d M, h:i A') }}</td>
                            <td class="text-end pe-3">
                                <a href="#" class="btn btn-sm btn-icon btn-light rounded-circle"><i class="mdi mdi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        @if(auth()->user()->hasPermission('view_daily_sales') && isset($data['chart_sales']))
        // Sales Trend Chart
        var salesOptions = {
            series: [{
                name: 'Revenue',
                data: @json($data['chart_sales'])
            }],
            chart: {
                type: 'area',
                height: 320,
                fontFamily: 'inherit',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#0d6efd'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: @json($data['chart_dates']),
                type: 'datetime',
                labels: { format: 'dd MMM' }
            },
            yaxis: {
                labels: { formatter: function (val) { return "$" + val.toFixed(0); } }
            },
            grid: { borderColor: '#f1f1f1' },
            tooltip: { x: { format: 'dd MMM yyyy' } }
        };
        new ApexCharts(document.querySelector("#salesTrendChart"), salesOptions).render();
        @endif

        @if((auth()->user()->hasPermission('view_daily_sales') || auth()->user()->hasPermission('view_reports')) && isset($data['top_products']) && count($data['top_products']) > 0)
        // Top Products Pie
        var pieOptions = {
            series: @json($data['top_products']->pluck('qty')),
            labels: @json($data['top_products']->pluck('product_name')),
            chart: {
                type: 'donut',
                height: 200,
                fontFamily: 'inherit'
            },
            colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Sold',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#topProductsPie"), pieOptions).render();
        @endif
    </script>
    @endpush

</x-app-layout>