<x-app-layout title="Store Valuation">

<style>
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.animate-slide-up {
    animation: slideInUp 0.6s ease-out forwards;
}

.stat-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.stat-icon {
    transition: transform 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: rotate(10deg) scale(1.1);
}

.gradient-warning {
    background: linear-gradient(135deg, #f7b731 0%, #f39c12 100%);
}

.gradient-success {
    background: linear-gradient(135deg, #26de81 0%, #20bf6b 100%);
}

.gradient-info {
    background: linear-gradient(135deg, #45aaf2 0%, #2d98da 100%);
}

.glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(75, 73, 172, 0.05);
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-weight: 700;
    font-size: 14px;
}

.rank-1 { background: linear-gradient(135deg, #ffd700, #ffed4e); color: #856404; }
.rank-2 { background: linear-gradient(135deg, #c0c0c0, #e8e8e8); color: #5a6268; }
.rank-3 { background: linear-gradient(135deg, #cd7f32, #e8a87c); color: #fff; }
.rank-default { background: linear-gradient(135deg, #f8f9fa, #e9ecef); color: #6c757d; }

.filter-card {
    transition: all 0.3s ease;
}

.filter-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
}

.btn-primary {
    background: linear-gradient(135deg, #4B49AC 0%, #7978E9 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(75, 73, 172, 0.3);
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 8s ease-in-out infinite;
}

.chart-card {
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.chart-card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.12) !important;
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
}

.empty-state i {
    font-size: 64px;
    opacity: 0.3;
    margin-bottom: 20px;
}

.badge-value {
    background: linear-gradient(135deg, #26de81 0%, #20bf6b 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.form-control:focus, .form-select:focus {
    border-color: #4B49AC;
    box-shadow: 0 0 0 0.2rem rgba(75, 73, 172, 0.15);
}

.card-header {
    border-bottom: 2px solid rgba(75, 73, 172, 0.1);
}
</style>

<div class="container-fluid">

    <!-- Enhanced Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4 p-4 shadow-lg position-relative">
        <div class="position-relative z-1">
            <h3 class="fw-bold mb-2">
                <i class="mdi mdi-cash-multiple me-2"></i> Store Valuation
            </h3>
            <p class="mb-0 opacity-90">Track and analyze your inventory's monetary value</p>
        </div>
        <div class="d-flex gap-2 position-relative z-1">
            <a href="{{ route('store.stock-control.valuation') }}" class="btn btn-light border-0 shadow-sm">
                <i class="mdi mdi-refresh me-1"></i> Reset
            </a>
            
        </div>
    </div>

    <!-- Enhanced Filter Card -->
    <div class="card border-0 shadow-sm mb-4 filter-card glass-effect" style="animation-delay: 0.1s">
        <div class="card-body p-4">
            <form action="{{ route('store.stock-control.valuation') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-2">
                        <i class="mdi mdi-calendar-range text-primary me-1"></i> Date Range
                    </label>
                    <div class="input-group shadow-sm">
                        <input type="date" name="start_date" class="form-control border-end-0" 
                               value="{{ request('start_date', \Carbon\Carbon::today()->subDays(29)->format('Y-m-d')) }}">
                        <span class="input-group-text bg-white">
                            <i class="mdi mdi-arrow-right text-muted"></i>
                        </span>
                        <input type="date" name="end_date" class="form-control border-start-0" 
                               value="{{ request('end_date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-2">
                        <i class="mdi mdi-tag-multiple text-primary me-1"></i> Category
                    </label>
                    <select name="category_id" class="form-select shadow-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-2">
                        <i class="mdi mdi-package-variant text-primary me-1"></i> Product
                    </label>
                    <select name="product_id" class="form-select shadow-sm">
                        <option value="">All Products</option>
                        @foreach($productsList as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->product_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="mdi mdi-filter-variant me-1"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg stat-card animate-slide-up h-100" style="animation-delay: 0.2s">
                <div class="card-body gradient-warning p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-white bg-opacity-25 text-white me-2">Total</span>
                                <h6 class="mb-0 text-white-50">Store Value</h6>
                            </div>
                            <h1 class="fw-bold mb-0 text-white display-6">₹{{ number_format($storeValue, 2) }}</h1>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-3 p-3 stat-icon">
                            <i class="mdi mdi-currency-inr display-4 "></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                        <small class="text-white-50">
                            <i class="mdi mdi-information-outline me-1"></i>
                            @if(request('category_id') || request('product_id'))
                                Filtered inventory value
                            @else
                                Complete inventory value
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg stat-card animate-slide-up h-100" style="animation-delay: 0.3s">
                <div class="card-body gradient-success p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-white bg-opacity-25 text-white me-2">Stock</span>
                                <h6 class="mb-0 text-white-50">Total Units</h6>
                            </div>
                            <h1 class="fw-bold mb-0 text-white display-6">{{ number_format($topProducts->sum('quantity')) }}</h1>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-3 p-3 stat-icon">
                            <i class="mdi mdi-package-variant-closed display-4 "></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                        <small class="text-white-50">
                            <i class="mdi mdi-cube-outline me-1"></i>
                            Units in current selection
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-lg stat-card animate-slide-up h-100" style="animation-delay: 0.4s">
                <div class="card-body gradient-info p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-white bg-opacity-25 text-white me-2">Top</span>
                                <h6 class="mb-0 text-white-50">Highest Value</h6>
                            </div>
                            <h1 class="fw-bold mb-0 text-white display-6">₹{{ number_format($topProducts->first()?->value ?? 0, 2) }}</h1>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-3 p-3 stat-icon">
                            <i class="mdi mdi-star-circle display-4 "></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                        <small class="text-white text-truncate d-block fw-medium">
                            <i class="mdi mdi-tag me-1"></i>
                            {{ $topProducts->first()?->product_name ?? 'No data available' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Chart Card -->
    <div class="card border-0 shadow-sm mb-5 chart-card animate-slide-up" style="animation-delay: 0.5s">
        <div class="card-header bg-gradient d-flex justify-content-between align-items-center py-3" 
             style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="mdi mdi-chart-line text-primary me-2"></i>
                    Valuation Trend
                </h5>
                <small class="text-muted">Track inventory value over time</small>
            </div>
            <div class="badge badge-value">
                <i class="mdi mdi-calendar me-1"></i>
                {{ \Carbon\Carbon::parse(request('start_date', now()->subDays(29)))->format('d M') }} - 
                {{ \Carbon\Carbon::parse(request('end_date', now()))->format('d M') }}
            </div>
        </div>
        <div class="card-body p-0">
            <div id="valuationTrend" style="height: 400px;"></div>
        </div>
    </div>

    <!-- Enhanced Table Card -->
    <div class="card border-0 shadow-sm animate-slide-up" style="animation-delay: 0.6s">
        <div class="card-header bg-gradient d-flex justify-content-between align-items-center py-3"
             style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="mdi mdi-trophy text-warning me-2"></i>
                    Top Products by Value
                </h5>
                <small class="text-muted">Products ranked by total inventory cost</small>
            </div>
            <span class="badge bg-primary">{{ $topProducts->count() }} Products</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <tr>
                            <th class="text-center" style="width: 80px;">Rank</th>
                            <th>Product Name</th>
                            <th style="width: 150px;">SKU</th>
                            <th class="text-center" style="width: 120px;">
                                <i class="mdi mdi-package-variant me-1"></i>Quantity
                            </th>
                            <th class="text-end" style="width: 180px;">
                                <i class="mdi mdi-currency-inr me-1"></i>Total Value
                            </th>
                             <th class="text-end" style="width: 180px;">
                                <i class="mdi mdi-currency-inr me-1"></i>Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $index => $product)
                            <tr>
                                <td class="text-center">
                                    <span class="rank-badge {{ $index < 3 ? 'rank-'.($index+1) : 'rank-default' }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                            <i class="mdi mdi-package-variant text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $product->product_name }}</div>
                                            @if($index < 3)
                                                <small class="text-muted">
                                                    <i class="mdi mdi-medal me-1"></i>Top performer
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $product->sku ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-medium">{{ number_format($product->quantity, 0) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-success fs-6">
                                        ₹ {{ number_format($product->value, 2) }}
                                    </span>
                                </td>
                                 <td class="text-end">
                                    <a href="{{ route('store.products.analytics', $product->id) }}" class="btn btn-sm btn-info py-1 me-1" title="Analytics">
    <i class="mdi mdi-chart-bar"></i>
</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="mdi mdi-package-variant-closed-remove d-block"></i>
                                    <h6 class="text-muted mb-2">No Products Found</h6>
                                    <p class="text-muted small mb-0">Try adjusting your filters to see results</p>
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
        toolbar: { 
            show: true,
            tools: {
                download: true,
                zoom: true,
                pan: true,
                reset: true
            }
        },
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800,
            animateGradually: {
                enabled: true,
                delay: 150
            },
            dynamicAnimation: {
                enabled: true,
                speed: 350
            }
        }
    },
    dataLabels: { enabled: false },
    stroke: {
        curve: 'smooth',
        width: 3
    },
    xaxis: {
        type: 'datetime',
        categories: @json($dates),
        labels: { 
            format: 'dd MMM',
            style: {
                fontSize: '12px',
                fontWeight: 500
            }
        }
    },
    yaxis: {
        title: { 
            text: 'Value (₹)',
            style: {
                fontSize: '13px',
                fontWeight: 600
            }
        },
        labels: {
            formatter: val => "₹ " + val.toLocaleString('en-IN', { maximumFractionDigits: 0 }),
            style: {
                fontSize: '12px'
            }
        }
    },
    tooltip: {
        x: { format: 'dd MMM yyyy' },
        y: { 
            formatter: val => "₹ " + val.toLocaleString('en-IN'),
            title: {
                formatter: () => 'Total Value'
            }
        },
        theme: 'light'
    },
    colors: ['#4B49AC'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.1,
            stops: [0, 90, 100]
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4,
        padding: {
            top: 0,
            right: 20,
            bottom: 0,
            left: 10
        }
    },
    markers: {
        size: 0,
        hover: {
            size: 6
        }
    }
};

var chart = new ApexCharts(document.querySelector("#valuationTrend"), options);
chart.render();
</script>
@endpush

</x-app-layout>