<x-app-layout title="Product Analytics">

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }

        .kpi-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 16px;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .kpi-card:hover::before {
            left: 100%;
        }

        .kpi-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .icon-wrapper {
            min-width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .kpi-card:hover .icon-wrapper {
            transform: rotate(10deg) scale(1.1);
        }

        .gradient-success {
            background: linear-gradient(135deg, #26de81 0%, #20bf6b 100%);
        }

        .gradient-primary {
            background: linear-gradient(135deg, #4B49AC 0%, #7978E9 100%);
        }

        .gradient-warning {
            background: linear-gradient(135deg, #f7b731 0%, #f39c12 100%);
        }

        .gradient-info {
            background: linear-gradient(135deg, #45aaf2 0%, #2d98da 100%);
        }

        .filter-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .filter-card:hover {
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1) !important;
        }

        .chart-card {
            border-radius: 16px;
            border: none;
            transition: all 0.3s ease;
        }

        .chart-card:hover {
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12) !important;
        }

        .insight-card {
            border-radius: 16px;
            border: none;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        }

        .insight-item {
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 8px;
            background: white;
            transition: all 0.2s ease;
        }

        .insight-item:hover {
            background: rgba(75, 73, 172, 0.05);
            transform: translateX(8px);
        }

        .progress-bar-animated {
            animation: shimmer 2s infinite;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.4) 50%, transparent 100%);
            background-size: 200% 100%;
        }

        .location-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 20px;
            background: white;
            border: 2px solid #e9ecef;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .location-chip:hover {
            border-color: #4B49AC;
            background: rgba(75, 73, 172, 0.05);
            transform: translateY(-2px);
        }

        .location-chip.active {
            background: linear-gradient(135deg, #4B49AC 0%, #7978E9 100%);
            color: white;
            border-color: #4B49AC;
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

        .badge-metric {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4B49AC;
            box-shadow: 0 0 0 0.2rem rgba(75, 73, 172, 0.15);
        }

        .chart-toggle-btn {
            transition: all 0.2s ease;
        }

        .chart-toggle-btn.active {
            background: linear-gradient(135deg, #4B49AC 0%, #7978E9 100%);
            color: white;
            border-color: #4B49AC;
        }

        .location-stats {
            max-height: 400px;
            overflow-y: auto;
        }

        .location-stats::-webkit-scrollbar {
            width: 6px;
        }

        .location-stats::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .location-stats::-webkit-scrollbar-thumb {
            background: #4B49AC;
            border-radius: 10px;
        }

        .location-row {
            transition: all 0.2s ease;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .location-row:hover {
            background: rgba(75, 73, 172, 0.05);
            transform: scale(1.02);
        }
    </style>

    <div class="content-wrapper">
        <!-- Hero Header -->
        <div class="hero-gradient rounded-4 shadow-lg mb-4 p-4 position-relative">
            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
                <div class="text-white">
                    <h2 class="mb-2 fw-bold d-flex align-items-center">
                        <i class="mdi mdi-chart-box-outline me-2"></i>{{ $product->product_name }}

                        {{-- NEW: Department Badge --}}
                        <span class="badge bg-white bg-opacity-25 border border-white border-opacity-25 ms-3 fs-6">
                            <i class="mdi mdi-layers-outline me-1"></i>
                            {{ $product->department->name ?? 'No Dept' }}
                        </span>
                    </h2>
                    <p class="mb-0 opacity-90">
                        <i class="mdi mdi-chart-line me-1"></i>Comprehensive Performance Analytics & Market Insights
                    </p>
                </div>
                <a href="{{ route('store.products.index') }}" class="btn btn-light shadow-sm">
                    <i class="mdi mdi-arrow-left me-2"></i>Back to Products
                </a>
            </div>
        </div>

        <!-- Enhanced Filter Card -->
        <div class="card filter-card shadow-sm mb-4 animate-fade-in" style="animation-delay: 0.1s">
            <div class="card-body p-4">
                <form action="{{ route('store.products.analytics', $product->id) }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted mb-2">
                                <i class="mdi mdi-calendar-range text-primary me-1"></i> Date Range
                            </label>
                            <input type="text" name="date_range" id="dateRangePicker"
                                class="form-control shadow-sm"
                                placeholder="Select date range"
                                value="{{ request('date_range') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted mb-2">
                                <i class="mdi mdi-map-marker text-primary me-1"></i> Location Filter
                            </label>
                            <select name="location" class="form-select shadow-sm" id="locationFilter">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="mdi mdi-filter-variant me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enhanced KPI Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card kpi-card shadow-lg animate-fade-in" style="animation-delay: 0.2s">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-metric bg-success bg-opacity-10 text-success me-2">Stock</span>
                                    <p class="text-muted mb-0 small">Current Quantity</p>
                                </div>
                                <h2 class="mb-0 fw-bold display-6">{{ number_format($stock->quantity) }}</h2>
                                <span class="badge bg-light text-dark mt-2">{{ $product->unit }}</span>
                            </div>
                            <div class="icon-wrapper gradient-success rounded-3">
                                <i class="mdi mdi-package-variant text-white" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card kpi-card shadow-lg animate-fade-in" style="animation-delay: 0.3s">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-metric bg-primary bg-opacity-10 text-primary me-2">Price</span>
                                    <p class="text-muted mb-0 small">Selling Price</p>
                                </div>
                                <h2 class="mb-0 fw-bold display-6">${{ number_format($stock->selling_price) }}</h2>
                                <span class="badge bg-light text-dark mt-2">per {{ $product->unit }}</span>
                            </div>
                            <div class="icon-wrapper gradient-primary rounded-3">
                                <i class="mdi mdi-currency-inr text-white" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card kpi-card shadow-lg animate-fade-in" style="animation-delay: 0.4s">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-metric bg-warning bg-opacity-10 text-warning me-2">Value</span>
                                    <p class="text-muted mb-0 small">Stock Worth</p>
                                </div>
                                <h2 class="mb-0 fw-bold display-6">${{ number_format($stock->quantity * $stock->selling_price) }}</h2>
                                <span class="badge bg-light text-dark mt-2">Total Value</span>
                            </div>
                            <div class="icon-wrapper gradient-warning rounded-3">
                                <i class="mdi mdi-cash-multiple text-white" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card kpi-card shadow-lg animate-fade-in" style="animation-delay: 0.5s">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-metric bg-info bg-opacity-10 text-info me-2">Avg</span>
                                    <p class="text-muted mb-0 small">Daily Sales</p>
                                </div>
                                <h2 class="mb-0 fw-bold display-6" id="avgUsage">{{ $avgDaily }}</h2>
                                <span class="badge bg-light text-dark mt-2">{{ $product->unit }}/day</span>
                            </div>
                            <div class="icon-wrapper gradient-info rounded-3">
                                <i class="mdi mdi-chart-timeline-variant text-white" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart and Location Stats Row -->
        <div class="row mb-4">
            <!-- Chart Section -->
            <div class="col-lg-8 mb-3">
                <div class="card chart-card shadow-sm animate-fade-in" style="animation-delay: 0.6s">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1 fw-bold">
                                    <i class="mdi mdi-chart-line text-primary me-2"></i>Sales Trend
                                </h5>
                                <p class="text-muted mb-0 small">Daily sales pattern analysis</p>
                            </div>
                            <div class="btn-group btn-group-sm shadow-sm" role="group">
                                <button type="button" class="btn btn-outline-primary chart-toggle-btn active" onclick="updateChartType('line')">
                                    <i class="mdi mdi-chart-line me-1"></i>Line
                                </button>
                                <button type="button" class="btn btn-outline-primary chart-toggle-btn" onclick="updateChartType('bar')">
                                    <i class="mdi mdi-chart-bar me-1"></i>Bar
                                </button>
                            </div>
                        </div>
                        <div style="position: relative; height: 350px;">
                            <canvas id="productChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location-wise Sales -->
            <div class="col-lg-4 mb-3">
                <div class="card insight-card shadow-sm animate-fade-in h-100" style="animation-delay: 0.7s">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-map-marker-radius text-danger me-2"></i>Sales by Location
                        </h5>
                        <div class="location-stats" id="locationStats">
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-loading mdi-spin fs-3"></i>
                                <p class="mt-2 mb-0">Loading location data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Row -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card insight-card shadow-sm animate-fade-in h-100" style="animation-delay: 0.8s">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-lightbulb-on text-warning me-2"></i>Smart Insights
                        </h5>
                        <div class="insight-item">
                            <i class="mdi mdi-trending-up text-success me-2"></i>
                            <span id="insight1" class="text-muted">Analyzing consumption patterns...</span>
                        </div>
                        <div class="insight-item">
                            <i class="mdi mdi-chart-bell-curve text-primary me-2"></i>
                            <span id="insight2" class="text-muted">Calculating peak demand...</span>
                        </div>
                        <div class="insight-item">
                            <i class="mdi mdi-calendar-clock text-info me-2"></i>
                            <span id="insight3" class="text-muted">Evaluating stock duration...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card insight-card shadow-sm animate-fade-in h-100" style="animation-delay: 0.9s">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-shield-check text-success me-2"></i>Stock Health
                        </h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small fw-medium">
                                    <i class="mdi mdi-gauge me-1"></i>Stock Level
                                </span>
                                <span class="small fw-bold" id="stockPercent">--</span>
                            </div>
                            <div class="progress shadow-sm" style="height: 12px; border-radius: 10px;">
                                <div id="stockBar" class="progress-bar" role="progressbar" style="width: 0%; border-radius: 10px;"></div>
                            </div>
                        </div>
                        <div class="alert border-0 shadow-sm mb-0" id="stockAlert" style="border-radius: 12px;">
                            <i class="mdi mdi-information-outline me-2"></i>
                            <span id="stockMessage">Calculating stock status...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let productChart;
        const chartData = {
            labels: {
                !!json_encode($dates) !!
            },
            usage: {
                !!json_encode($usage) !!
            }
        };

        // Initialize Flatpickr
        flatpickr("#dateRangePicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: "{{ request('date_range', now()->subDays(29)->format('Y-m-d') . ' to ' . now()->format('Y-m-d')) }}".split(' to '),
            maxDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    // Auto-submit on range selection (optional)
                    // document.getElementById('filterForm').submit();
                }
            }
        });

        function initChart(type = 'line') {
            const ctx = document.getElementById('productChart').getContext('2d');

            if (productChart) {
                productChart.destroy();
            }

            productChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Quantity Sold',
                        data: chartData.usage,
                        borderColor: '#4B49AC',
                        backgroundColor: type === 'bar' ?
                            'rgba(75, 73, 172, 0.8)' : function(context) {
                                const ctx = context.chart.ctx;
                                const gradient = ctx.createLinearGradient(0, 0, 0, 350);
                                gradient.addColorStop(0, 'rgba(75, 73, 172, 0.4)');
                                gradient.addColorStop(1, 'rgba(75, 73, 172, 0.02)');
                                return gradient;
                            },
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 8,
                        pointBackgroundColor: '#4B49AC',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#4B49AC',
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.9)',
                            padding: 16,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            displayColors: false,
                            borderColor: '#4B49AC',
                            borderWidth: 2,
                            callbacks: {
                                label: function(context) {
                                    return 'Sold: ' + context.parsed.y + ' {{ $product->unit }}';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                padding: 8
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            calculateInsights();
        }

        function updateChartType(type) {
            document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('button').classList.add('active');
            initChart(type);
        }

        function calculateInsights() {
            const usage = chartData.usage;
            const totalUsage = usage.reduce((a, b) => a + b, 0);
            const avgUsage = usage.length > 0 ? (totalUsage / usage.length).toFixed(2) : 0;
            const maxUsage = usage.length > 0 ? Math.max(...usage) : 0;
            const currentStock = {
                {
                    $stock - > quantity
                }
            };

            // Update insights
            document.getElementById('insight1').textContent =
                `Average daily sales: ${avgUsage} {{ $product->unit }}`;
            document.getElementById('insight2').textContent =
                `Peak sales recorded: ${maxUsage} {{ $product->unit }} in a single day`;

            const daysRemaining = avgUsage > 0 ? Math.floor(currentStock / avgUsage) : '∞';
            document.getElementById('insight3').textContent =
                daysRemaining !== '∞' ?
                `Stock will last approximately ${daysRemaining} days` :
                'Stock duration cannot be calculated';

            // Stock status
            const reorderPoint = avgUsage * 7;
            const stockPercent = Math.min(100, (currentStock / (reorderPoint * 2)) * 100);

            document.getElementById('stockPercent').textContent = stockPercent.toFixed(0) + '%';
            document.getElementById('stockBar').style.width = stockPercent + '%';

            const alertDiv = document.getElementById('stockAlert');
            if (stockPercent > 50) {
                document.getElementById('stockBar').className = 'progress-bar bg-success';
                alertDiv.className = 'alert border-0 shadow-sm mb-0 alert-success';
                document.getElementById('stockMessage').innerHTML =
                    '<i class="mdi mdi-check-circle me-1"></i>Stock level is healthy and sufficient';
            } else if (stockPercent > 25) {
                document.getElementById('stockBar').className = 'progress-bar bg-warning';
                alertDiv.className = 'alert border-0 shadow-sm mb-0 alert-warning';
                document.getElementById('stockMessage').innerHTML =
                    '<i class="mdi mdi-alert me-1"></i>Consider reordering soon to avoid stockout';
            } else {
                document.getElementById('stockBar').className = 'progress-bar bg-danger';
                alertDiv.className = 'alert border-0 shadow-sm mb-0 alert-danger';
                document.getElementById('stockMessage').innerHTML =
                    '<i class="mdi mdi-alert-circle me-1"></i>Critical: Reorder immediately to prevent stockout';
            }
        }

        // Load location-wise sales data
        function loadLocationStats() {
            fetch(`{{ route('store.products.analytics', $product->id) }}?ajax=1&location_stats=1&date_range={{ request('date_range') }}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('r');

                    if (data.locations && data.locations.length > 0) {
                        const maxSales = Math.max(...data.locations.map(l => l.total_sold));

                        let html = '';
                        data.locations.forEach((loc, index) => {
                            const percentage = (loc.total_sold / maxSales * 100).toFixed(0);
                            const rankClass = index === 0 ? 'text-success' : index === 1 ? 'text-primary' : 'text-muted';

                            html += `
                            <div class="location-row">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2">${index + 1}</span>
                                        <div>
                                            <div class="fw-bold ${rankClass}">${loc.location}</div>
                                            <small class="text-muted">${loc.total_sold} {{ $product->unit }} sold</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary">$${parseFloat(loc.revenue).toLocaleString('en-IN')}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                    <div class="progress-bar bg-gradient" style="width: ${percentage}%; background: linear-gradient(90deg, #4B49AC, #7978E9);"></div>
                                </div>
                            </div>
                        `;
                        });

                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="mdi mdi-map-marker-off fs-3"></i>
                            <p class="mt-2 mb-0">No location data available</p>
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error loading location stats:', error);
                    document.getElementById('locationStats').innerHTML = `
                    <div class="text-center py-4 text-danger">
                        <i class="mdi mdi-alert-circle fs-3"></i>
                        <p class="mt-2 mb-0">Error loading location data</p>
                    </div>
                `;
                });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
            loadLocationStats();
        });
    </script>
    @endpush
</x-app-layout>