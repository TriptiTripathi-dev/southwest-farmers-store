<x-app-layout title="Product Analytics">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">{{ $product->product_name }}</h3>
                        <p class="text-muted mb-0">
                            <i class="mdi mdi-chart-line me-1"></i>Performance Analytics & Insights
                        </p>
                    </div>
                    <a href="{{ route('store.products.index') }}" class="btn btn-light border">
                        <i class="mdi mdi-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Current Stock</p>
                                <h3 class="mb-0 fw-bold">{{ $stock->quantity }}</h3>
                                <span class="badge bg-light text-dark mt-2">{{ $product->unit }}</span>
                            </div>
                            <div class="icon-wrapper bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-package-variant text-success" style="font-size: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Selling Price</p>
                                <h3 class="mb-0 fw-bold">₹{{ number_format($stock->selling_price, 2) }}</h3>
                                <span class="badge bg-light text-dark mt-2">per {{ $product->unit }}</span>
                            </div>
                            <div class="icon-wrapper bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-currency-inr text-primary" style="font-size: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Stock Value</p>
                                <h3 class="mb-0 fw-bold">₹{{ number_format($stock->quantity * $stock->selling_price, 2) }}</h3>
                                <span class="badge bg-light text-dark mt-2">Total Worth</span>
                            </div>
                            <div class="icon-wrapper bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-cash-multiple text-warning" style="font-size: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Avg. Daily Usage</p>
                                <h3 class="mb-0 fw-bold" id="avgUsage">--</h3>
                                <span class="badge bg-light text-dark mt-2">Last 30 days</span>
                            </div>
                            <div class="icon-wrapper bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-chart-timeline-variant text-info" style="font-size: 24px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1 fw-bold">Consumption Trend</h5>
                                <p class="text-muted mb-0 small">Daily usage pattern over the last 30 days</p>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="updateChartType('line')">
                                    <i class="mdi mdi-chart-line"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="updateChartType('bar')">
                                    <i class="mdi mdi-chart-bar"></i>
                                </button>
                            </div>
                        </div>
                        <div style="position: relative; height: 300px;">
                            <canvas id="productChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Section -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-lightbulb-on text-warning me-2"></i>Quick Insights
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex align-items-start">
                                <i class="mdi mdi-check-circle text-success me-2 mt-1"></i>
                                <span id="insight1" class="text-muted">Analyzing consumption patterns...</span>
                            </li>
                            <li class="mb-2 d-flex align-items-start">
                                <i class="mdi mdi-check-circle text-success me-2 mt-1"></i>
                                <span id="insight2" class="text-muted">Calculating reorder points...</span>
                            </li>
                            <li class="mb-0 d-flex align-items-start">
                                <i class="mdi mdi-check-circle text-success me-2 mt-1"></i>
                                <span id="insight3" class="text-muted">Evaluating stock levels...</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-information text-info me-2"></i>Stock Status
                        </h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Stock Level</span>
                                <span class="small fw-bold" id="stockPercent">--</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div id="stockBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="alert alert-light border mb-0" id="stockAlert">
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
    <script>
        let productChart;
        const chartData = {
            labels: {!! json_encode($dates) !!},
            usage: {!! json_encode($usage) !!}
        };

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
                        label: 'Quantity Sold/Used',
                        data: chartData.usage,
                        borderColor: '#4B49AC',
                        backgroundColor: type === 'bar' ? 'rgba(75, 73, 172, 0.8)' : 'rgba(75, 73, 172, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#4B49AC',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
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
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 },
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { size: 11 },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });

            calculateInsights();
        }

        function updateChartType(type) {
            document.querySelectorAll('.btn-group button').forEach(btn => {
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
    const currentStock = {{ $stock->quantity }};
            
            // Update average usage
            document.getElementById('avgUsage').textContent = avgUsage + ' {{ $product->unit }}';

            // Calculate days until stockout
            const daysRemaining = avgUsage > 0 ? Math.floor(currentStock / avgUsage) : '∞';
            
            // Update insights
            document.getElementById('insight1').textContent = 
                `Average daily consumption: ${avgUsage} {{ $product->unit }}`;
            document.getElementById('insight2').textContent = 
                `Peak usage recorded: ${maxUsage} {{ $product->unit }} in a single day`;
            document.getElementById('insight3').textContent = 
                daysRemaining !== '∞' ? `Estimated stock will last ${daysRemaining} days at current rate` : 
                'No consumption recorded in the last 30 days';

            // Stock status calculation
            const reorderPoint = avgUsage * 7; // 7 days buffer
            const stockPercent = Math.min(100, (currentStock / (reorderPoint * 2)) * 100);
            
            document.getElementById('stockPercent').textContent = stockPercent.toFixed(0) + '%';
            document.getElementById('stockBar').style.width = stockPercent + '%';
            
            if (stockPercent > 50) {
                document.getElementById('stockBar').className = 'progress-bar bg-success';
                document.getElementById('stockMessage').textContent = 'Stock level is healthy';
            } else if (stockPercent > 25) {
                document.getElementById('stockBar').className = 'progress-bar bg-warning';
                document.getElementById('stockMessage').textContent = 'Consider reordering soon';
            } else {
                document.getElementById('stockBar').className = 'progress-bar bg-danger';
                document.getElementById('stockMessage').textContent = 'Stock level is low - reorder recommended';
            }
        }

        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
        });
    </script>
    @endpush

    <style>
        .icon-wrapper {
            min-width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }

        .progress {
            border-radius: 10px;
            background-color: #f0f0f0;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }
    </style>
</x-app-layout>