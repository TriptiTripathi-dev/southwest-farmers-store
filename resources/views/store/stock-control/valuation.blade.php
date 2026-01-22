<x-app-layout title="Store Valuation">

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5 bg-white p-4 rounded shadow">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="mdi mdi-cash-multiple text-success me-2 fs-4"></i> Store Valuation
            </h4>
            <small class="text-muted">Current monetary value of inventory in your store</small>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-outline-success btn-sm">
                <i class="mdi mdi-refresh me-1"></i> Refresh
            </button>
            <button class="btn btn-outline-primary btn-sm">
                <i class="mdi mdi-download me-1"></i> Export Report
            </button>
        </div>
    </div>

    <!-- KPI Stats Cards -->
    <div class="row g-4 mb-5">
        <!-- Total Value -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg overflow-hidden h-100">
                <div class="card-body bg-gradient-primary  p-4 position-relative">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class=" mb-2">Total Store Value</h6>
                            <h2 class="fw-bold mb-0">₹ {{ number_format($storeValue, 2) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-4">
                            <i class="mdi mdi-currency-inr fs-3"></i>
                        </div>
                    </div>
                    <small class=" mt-3 d-block">Calculated at cost price • Real-time</small>
                </div>
            </div>
        </div>

        <!-- Total Units -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg overflow-hidden h-100">
                <div class="card-body bg-gradient-success  p-4 position-relative">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class=" mb-2">Total Units</h6>
                            <h2 class="fw-bold mb-0">{{ $topProducts->sum('quantity') }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-4">
                            <i class="mdi mdi-package-variant-closed fs-3"></i>
                        </div>
                    </div>
                    <small class=" mt-3 d-block">Across all products in stock</small>
                </div>
            </div>
        </div>

        <!-- Top Product -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg overflow-hidden h-100">
                <div class="card-body bg-gradient-info  p-4 position-relative">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class=" mb-2">Top Product Value</h6>
                            <h2 class="fw-bold mb-0">₹ {{ number_format($topProducts->first()?->value ?? 0, 2) }}</h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-4">
                            <i class="mdi mdi-star-circle fs-3"></i>
                        </div>
                    </div>
                    <small class=" mt-3 d-block">
                        {{ $topProducts->first()?->product_name ?? 'No products yet' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- 30-Day Trend Chart -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">30-Day Valuation Trend</h5>
            <small class="text-muted">Daily value changes at cost price</small>
        </div>
        <div class="card-body p-0">
            <div id="valuationTrend" style="height: 400px;"></div>
        </div>
    </div>

    <!-- Top 10 Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Top 10 Products by Value</h5>
            <small class="text-muted">Sorted by total cost value</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Value (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $index => $product)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $product->product_name }}</td>
                                <td>{{ $product->sku ?? 'N/A' }}</td>
                                <td class="text-center">{{ number_format($product->quantity, 0) }}</td>
                                <td class="text-end fw-bold text-success">₹ {{ number_format($product->value, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-information-outline fs-3 d-block mb-2"></i>
                                    No products in stock yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
var options = {
    series: [{
        name: 'Store Value',
        data: @json($trendData)
    }],
    chart: {
        height: 400,
        type: 'area',
        zoom: { enabled: false },
        toolbar: { show: true, tools: { download: true } }
    },
    dataLabels: { enabled: false },
    stroke: {
        curve: 'smooth',
        width: 3
    },
    xaxis: {
        type: 'datetime',
        categories: @json($dates),
        labels: { format: 'dd MMM' }
    },
    yaxis: {
        title: { text: 'Value (₹)' },
        labels: {
            formatter: val => "₹ " + val.toLocaleString('en-IN', { minimumFractionDigits: 0 })
        }
    },
    tooltip: {
        x: { format: 'dd MMM yyyy' },
        y: { formatter: val => "₹ " + val.toLocaleString('en-IN') }
    },
    colors: ['#198754'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.1,
            stops: [0, 90, 100]
        }
    },
    grid: {
        borderColor: '#e0e0e0'
    }
};

var chart = new ApexCharts(document.querySelector("#valuationTrend"), options);
chart.render();
</script>
@endpush

</x-app-layout>