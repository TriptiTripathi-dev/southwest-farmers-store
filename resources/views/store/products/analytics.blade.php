<x-app-layout title="Product Analytics">
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Minimal Custom CSS for Micro-Animations and Scrollbars */
        body { font-family: 'Manrope', sans-serif; }
        
        .hover-lift { transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s; }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
        
        .hover-bg-soft:hover { background-color: rgba(75, 73, 172, 0.04); }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .hero-bg {
            background: linear-gradient(135deg, #4B49AC 0%, #667eea 100%);
        }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            <div class="hero-bg rounded-4 shadow-sm mb-4 p-4 p-md-5 position-relative overflow-hidden text-white">
                <div class="position-absolute top-0 end-0 opacity-25" style="transform: translate(20%, -20%);">
                    <i class="mdi mdi-chart-donut" style="font-size: 15rem;"></i>
                </div>
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center position-relative z-1 gap-3">
                    <div>
                        <a href="{{ route('store.products.index') }}" class="btn btn-light btn-sm rounded-pill fw-bold text-primary mb-3 shadow-sm px-3">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to Products
                        </a>
                        <h2 class="mb-2 fw-bold display-6 d-flex align-items-center flex-wrap gap-2">
                            {{ $product->product_name }}
                            @if(isset($product->department))
                                <span class="badge bg-white bg-opacity-25 border border-white border-opacity-25 rounded-pill fs-6 px-3 py-2 mt-2 mt-md-0">
                                    <i class="mdi mdi-layers-outline me-1"></i> {{ $product->department->name }}
                                </span>
                            @endif
                        </h2>
                        <p class="mb-0 fs-6 opacity-75">
                            <i class="mdi mdi-chart-line me-1"></i> Comprehensive Performance Analytics & Market Insights
                        </p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('store.products.analytics', $product->id) }}" method="GET" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1 mb-2">
                                    <i class="mdi mdi-calendar-range text-primary me-1"></i> Date Range
                                </label>
                                <input type="text" name="date_range" id="dateRangePicker"
                                    class="form-control bg-light border-0 shadow-sm py-2 px-3"
                                    placeholder="Select date range"
                                    value="{{ request('date_range') }}">
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1 mb-2">
                                    <i class="mdi mdi-map-marker text-primary me-1"></i> Location Filter
                                </label>
                                <select name="location" class="form-select bg-light border-0 shadow-sm py-2 px-3" id="locationFilter">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-3">
                                    <i class="mdi mdi-filter-variant me-2"></i>Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 g-md-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                                <i class="mdi mdi-package-variant fs-2"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">Current Stock</p>
                                <h3 class="mb-0 fw-black text-dark">{{ number_format($stock->quantity ?? 0) }}</h3>
                                <span class="badge bg-light text-muted border mt-2">{{ $product->unit }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                                <i class="mdi mdi-currency-usd fs-2"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">Selling Price</p>
                                <h3 class="mb-0 fw-black text-dark">${{ number_format($stock->selling_price ?? 0, 2) }}</h3>
                                <span class="badge bg-light text-muted border mt-2">Per {{ $product->unit }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                                <i class="mdi mdi-cash-multiple fs-2"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">Stock Worth</p>
                                <h3 class="mb-0 fw-black text-dark">${{ number_format(($stock->quantity ?? 0) * ($stock->selling_price ?? 0), 2) }}</h3>
                                <span class="badge bg-light text-muted border mt-2">Total Value</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 text-info rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                                <i class="mdi mdi-chart-timeline-variant fs-2"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-muted small fw-bold text-uppercase mb-1">Daily Sales</p>
                                <h3 class="mb-0 fw-black text-dark" id="avgUsage">{{ $avgDaily ?? 0 }}</h3>
                                <span class="badge bg-light text-muted border mt-2">{{ $product->unit }}/day</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-md-4 mb-4">
                
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center rounded-top-4">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-chart-line text-primary me-2"></i>Sales Trend</h6>
                                <small class="text-muted">Daily sales pattern analysis</small>
                            </div>
                            <div class="btn-group shadow-sm rounded-3">
                                <button type="button" class="btn btn-outline-primary btn-sm fw-bold active chart-toggle-btn" onclick="updateChartType('line', this)">Line</button>
                                <button type="button" class="btn btn-outline-primary btn-sm fw-bold chart-toggle-btn" onclick="updateChartType('bar', this)">Bar</button>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="position-relative w-100" style="height: 350px;">
                                <canvas id="productChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                            <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-map-marker-radius text-danger me-2"></i>Sales by Location</h6>
                        </div>
                        <div class="card-body p-3 custom-scrollbar" style="max-height: 400px; overflow-y: auto;" id="locationStats">
                            <div class="text-center py-5 text-muted">
                                <div class="spinner-border text-primary border-2 mb-2" role="status"></div>
                                <p class="mb-0 small fw-bold">Loading location data...</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row g-3 g-md-4">
                
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                            <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-lightbulb-on text-warning me-2"></i>Smart Insights</h6>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush gap-2">
                                <li class="list-group-item d-flex align-items-center border-0 bg-light rounded-3 p-3 hover-bg-soft transition-all">
                                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle me-3"><i class="mdi mdi-trending-up"></i></div>
                                    <span id="insight1" class="fw-medium text-dark small">Analyzing consumption patterns...</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center border-0 bg-light rounded-3 p-3 hover-bg-soft transition-all">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-3"><i class="mdi mdi-chart-bell-curve"></i></div>
                                    <span id="insight2" class="fw-medium text-dark small">Calculating peak demand...</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center border-0 bg-light rounded-3 p-3 hover-bg-soft transition-all">
                                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-circle me-3"><i class="mdi mdi-calendar-clock"></i></div>
                                    <span id="insight3" class="fw-medium text-dark small">Evaluating stock duration...</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                            <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-shield-check text-success me-2"></i>Stock Health</h6>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small fw-bold text-uppercase"><i class="mdi mdi-gauge me-1"></i>Stock Level</span>
                                <span class="small fw-black text-dark" id="stockPercent">--</span>
                            </div>
                            <div class="progress shadow-none bg-light mb-4" style="height: 12px; border-radius: 10px;">
                                <div id="stockBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; border-radius: 10px;"></div>
                            </div>
                            <div class="alert border-0 shadow-sm mb-0 rounded-3 d-flex align-items-center" id="stockAlert">
                                <i class="mdi mdi-information-outline fs-4 me-3"></i>
                                <span id="stockMessage" class="fw-medium small">Calculating stock status...</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // FIXED BLADE ECHOS: Removed broken spacing that caused JS syntax errors
        const chartData = {
            labels: {!! json_encode($dates ?? []) !!},
            usage: {!! json_encode($usage ?? []) !!}
        };

        // FIXED STOCK VARIABLE
        const currentStock = {{ $stock->quantity ?? 0 }};
        const productUnit = "{{ $product->unit ?? 'Units' }}";
        let productChart;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr
            flatpickr("#dateRangePicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: "{{ request('date_range', now()->subDays(29)->format('Y-m-d') . ' to ' . now()->format('Y-m-d')) }}".split(' to '),
                maxDate: "today"
            });

            initChart();
            loadLocationStats();
            calculateInsights();
        });

        function initChart(type = 'line') {
            const ctx = document.getElementById('productChart').getContext('2d');
            if (productChart) productChart.destroy();

            productChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Quantity Sold',
                        data: chartData.usage,
                        borderColor: '#4B49AC',
                        backgroundColor: type === 'bar' ? 'rgba(75, 73, 172, 0.8)' : function(context) {
                            const chartCtx = context.chart.ctx;
                            const gradient = chartCtx.createLinearGradient(0, 0, 0, 350);
                            gradient.addColorStop(0, 'rgba(75, 73, 172, 0.3)');
                            gradient.addColorStop(1, 'rgba(75, 73, 172, 0.01)');
                            return gradient;
                        },
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#4B49AC',
                        pointBorderWidth: 2,
                        borderRadius: type === 'bar' ? 4 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Sold: ' + context.parsed.y + ' ' + productUnit;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 45, minRotation: 45 }
                        }
                    },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        }

        window.updateChartType = function(type, element) {
            document.querySelectorAll('.chart-toggle-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            initChart(type);
        }

        function calculateInsights() {
            const usage = chartData.usage;
            const totalUsage = usage.reduce((a, b) => a + b, 0);
            const avgUsage = usage.length > 0 ? (totalUsage / usage.length).toFixed(2) : 0;
            const maxUsage = usage.length > 0 ? Math.max(...usage) : 0;

            document.getElementById('insight1').textContent = `Average daily sales: ${avgUsage} ${productUnit}`;
            document.getElementById('insight2').textContent = `Peak sales recorded: ${maxUsage} ${productUnit} in a single day`;

            const daysRemaining = avgUsage > 0 ? Math.floor(currentStock / avgUsage) : '∞';
            document.getElementById('insight3').textContent = daysRemaining !== '∞' 
                ? `Stock will last approximately ${daysRemaining} days` 
                : 'Stock duration cannot be calculated yet';

            const reorderPoint = avgUsage * 7;
            const stockPercent = reorderPoint > 0 ? Math.min(100, (currentStock / (reorderPoint * 2)) * 100) : 100;

            document.getElementById('stockPercent').textContent = stockPercent.toFixed(0) + '%';
            document.getElementById('stockBar').style.width = stockPercent + '%';

            const alertDiv = document.getElementById('stockAlert');
            if (stockPercent > 50 || currentStock > 0 && reorderPoint === 0) {
                document.getElementById('stockBar').className = 'progress-bar progress-bar-striped progress-bar-animated bg-success';
                alertDiv.className = 'alert border-0 shadow-sm mb-0 rounded-3 d-flex align-items-center alert-success';
                document.getElementById('stockMessage').textContent = 'Stock level is healthy and sufficient.';
            } else if (stockPercent > 25) {
                document.getElementById('stockBar').className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';
                alertDiv.className = 'alert border-0 shadow-sm mb-0 rounded-3 d-flex align-items-center alert-warning';
                document.getElementById('stockMessage').textContent = 'Consider reordering soon to avoid a stockout.';
            } else {
                document.getElementById('stockBar').className = 'progress-bar progress-bar-striped progress-bar-animated bg-danger';
                alertDiv.className = 'alert border-0 shadow-sm mb-0 rounded-3 d-flex align-items-center alert-danger text-danger';
                document.getElementById('stockMessage').textContent = 'Critical: Reorder immediately to prevent stockout.';
            }
        }

        function loadLocationStats() {
            fetch(`{{ route('store.products.analytics', $product->id) }}?ajax=1&location_stats=1&date_range={{ request('date_range') }}`)
                .then(response => response.json())
                .then(data => {
                    // FIXED DOM ID: Changed 'r' to 'locationStats'
                    const container = document.getElementById('locationStats');

                    if (data.locations && data.locations.length > 0) {
                        const maxSales = Math.max(...data.locations.map(l => l.total_sold));
                        let html = '';
                        
                        data.locations.forEach((loc, index) => {
                            const percentage = (loc.total_sold / maxSales * 100).toFixed(0);
                            const rankClass = index === 0 ? 'text-success' : index === 1 ? 'text-primary' : 'text-dark';

                            html += `
                            <div class="p-3 bg-light rounded-3 mb-2 transition-all hover-bg-soft border border-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-white text-dark border me-3 d-flex justify-content-center align-items-center" style="width:24px; height:24px;">${index + 1}</div>
                                        <div>
                                            <div class="fw-bold ${rankClass} small">${loc.location}</div>
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">${loc.total_sold} ${productUnit} sold</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">$${parseFloat(loc.revenue).toLocaleString('en-US')}</span>
                                </div>
                                <div class="progress shadow-none bg-white border" style="height: 6px; border-radius: 10px;">
                                    <div class="progress-bar" style="width: ${percentage}%; background: linear-gradient(90deg, #4B49AC, #7978E9);"></div>
                                </div>
                            </div>`;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <div class="text-center py-5 text-muted opacity-50">
                                <i class="mdi mdi-map-marker-off fs-1"></i>
                                <p class="mt-2 mb-0 fw-bold small">No location data available</p>
                            </div>`;
                    }
                })
                .catch(error => {
                    console.error('Error loading location stats:', error);
                    document.getElementById('locationStats').innerHTML = `
                        <div class="text-center py-5 text-danger opacity-75">
                            <i class="mdi mdi-alert-circle fs-1"></i>
                            <p class="mt-2 mb-0 fw-bold small">Error loading location data</p>
                        </div>`;
                });
        }
    </script>
    @endpush
</x-app-layout>