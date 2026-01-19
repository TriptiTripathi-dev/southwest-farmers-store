<x-app-layout title="Category Analytics">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">{{ $category->name }}</h3>
                        <p class="text-muted mb-0">
                            <i class="mdi mdi-shape me-1"></i>Category Performance & Inventory Overview
                        </p>
                    </div>
                    <a href="{{ route('store.categories.index') }}" class="btn btn-light border">
                        <i class="mdi mdi-arrow-left me-2"></i>Back to Categories
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Total Products</p>
                                <h3 class="mb-0 fw-bold">{{ $stats->total_products }}</h3>
                                <span class="badge bg-light text-primary mt-2">
                                    <i class="mdi mdi-package-variant-closed"></i> SKUs
                                </span>
                            </div>
                            <div class="icon-wrapper bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-cube-outline text-info" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Total Stock Quantity</p>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats->total_qty ?? 0) }}</h3>
                                <span class="badge bg-light text-warning mt-2">
                                    <i class="mdi mdi-package-variant"></i> Units
                                </span>
                            </div>
                            <div class="icon-wrapper bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-warehouse text-warning" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Inventory Value</p>
                                <h3 class="mb-0 fw-bold">₹{{ number_format($stats->total_value, 2) }}</h3>
                                <span class="badge bg-light text-success mt-2">
                                    <i class="mdi mdi-cash-multiple"></i> Worth
                                </span>
                            </div>
                            <div class="icon-wrapper bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-currency-inr text-success" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">Avg. Product Value</p>
                                <h3 class="mb-0 fw-bold" id="avgValue">₹0.00</h3>
                                <span class="badge bg-light text-danger mt-2">
                                    <i class="mdi mdi-chart-line"></i> Per SKU
                                </span>
                            </div>
                            <div class="icon-wrapper bg-danger bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-calculator text-danger" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Top Products Chart -->
            <div class="col-lg-8 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1 fw-bold">Top Products by Stock</h5>
                                <p class="text-muted mb-0 small">Highest inventory levels in this category</p>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="updateChartType('bar')">
                                    <i class="mdi mdi-chart-bar"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="updateChartType('horizontalBar')">
                                    <i class="mdi mdi-chart-bar-stacked"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="updateChartType('doughnut')">
                                    <i class="mdi mdi-chart-donut"></i>
                                </button>
                            </div>
                        </div>
                        <div style="position: relative; height: 320px;">
                            <canvas id="catChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Distribution -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-chart-pie text-primary me-2"></i>Stock Distribution
                        </h5>
                        <div id="productList" class="mb-3">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <div class="mt-auto">
                            <div class="alert alert-light border mb-0">
                                <small class="text-muted">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    Showing top 5 products by quantity
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights Row -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-lightbulb-on text-warning me-2"></i>Category Insights
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex align-items-start">
                                <i class="mdi mdi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1">Inventory Coverage</strong>
                                    <span class="text-muted small" id="insight1">Calculating...</span>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <i class="mdi mdi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1">Stock Concentration</strong>
                                    <span class="text-muted small" id="insight2">Calculating...</span>
                                </div>
                            </li>
                            <li class="mb-0 d-flex align-items-start">
                                <i class="mdi mdi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1">Value Distribution</strong>
                                    <span class="text-muted small" id="insight3">Calculating...</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-clipboard-list text-info me-2"></i>Quick Stats
                        </h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small mb-1">Products in Stock</div>
                                    <h4 class="mb-0 fw-bold text-success" id="inStockCount">{{ $stats->total_products }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small mb-1">Avg Stock/Product</div>
                                    <h4 class="mb-0 fw-bold text-primary" id="avgStock">--</h4>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small">Category Health</span>
                                        <span class="badge bg-success" id="healthBadge">Excellent</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div id="healthBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let categoryChart;
        const chartData = {
            labels: {!! json_encode($labels) !!},
            data: {!! json_encode($data) !!},
            stats: {
                total_products: {{ $stats->total_products }},
                total_qty: {{ $stats->total_qty ?? 0 }},
                total_value: {{ $stats->total_value }}
            }
        };

        const colors = {
            primary: ['#4B49AC', '#7978E9', '#9998E9', '#B5B4E9', '#D1D0F0'],
            vibrant: ['#FFC100', '#4B49AC', '#248AFD', '#FF4747', '#57B657'],
            gradient: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe']
        };

        function initChart(type = 'bar') {
            const ctx = document.getElementById('catChart').getContext('2d');
            
            if (categoryChart) {
                categoryChart.destroy();
            }

            const isHorizontal = type === 'horizontalBar';
            const isDoughnut = type === 'doughnut';

            categoryChart = new Chart(ctx, {
                type: isDoughnut ? 'doughnut' : (isHorizontal ? 'bar' : 'bar'),
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Stock Quantity',
                        data: chartData.data,
                        backgroundColor: isDoughnut ? colors.vibrant : colors.vibrant.map(c => c + 'CC'),
                        borderColor: isDoughnut ? '#fff' : colors.vibrant,
                        borderWidth: isDoughnut ? 3 : 2,
                        borderRadius: isDoughnut ? 0 : 8,
                        hoverOffset: isDoughnut ? 15 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: isHorizontal ? 'y' : 'x',
                    plugins: {
                        legend: {
                            display: isDoughnut,
                            position: 'right',
                            labels: {
                                padding: 15,
                                font: { size: 11 },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => ({
                                        text: label + ' (' + data.datasets[0].data[i] + ')',
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    }));
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed.y || context.parsed) / total * 100).toFixed(1);
                                    return ` ${context.parsed.y || context.parsed} units (${percentage}%)`;
                                }
                            }
                        }
                    },
                    scales: isDoughnut ? {} : {
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
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });

            updateProductList();
        }

        function updateChartType(type) {
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('button').classList.add('active');
            initChart(type);
        }

        function updateProductList() {
            const listHTML = chartData.labels.map((label, index) => {
                const qty = chartData.data[index];
                const total = chartData.data.reduce((a, b) => a + b, 0);
                const percentage = ((qty / total) * 100).toFixed(1);
                
                return `
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2" style="width: 12px; height: 12px; background: ${colors.vibrant[index]}; border-radius: 3px;"></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold">${label}</span>
                                <span class="small text-muted">${qty} units</span>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" style="width: ${percentage}%; background: ${colors.vibrant[index]}"></div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('productList').innerHTML = listHTML;
        }

        function calculateInsights() {
            // Calculate average value per product
            const avgValue = chartData.stats.total_products > 0 
                ? chartData.stats.total_value / chartData.stats.total_products 
                : 0;
            document.getElementById('avgValue').textContent = '₹' + avgValue.toFixed(2);

            // Calculate average stock per product
            const avgStock = chartData.stats.total_products > 0 
                ? (chartData.stats.total_qty / chartData.stats.total_products).toFixed(0) 
                : 0;
            document.getElementById('avgStock').textContent = avgStock;

            // Insights
            if (chartData.stats.total_products > 0) {
                document.getElementById('insight1').textContent = 
                    `This category has ${chartData.stats.total_products} different products in inventory`;
                
                const topProduct = chartData.data[0] || 0;
                const topPercentage = ((topProduct / chartData.stats.total_qty) * 100).toFixed(1);
                document.getElementById('insight2').textContent = 
                    `Top product holds ${topPercentage}% of total category stock`;
                
                document.getElementById('insight3').textContent = 
                    `Average inventory value per product is ₹${avgValue.toFixed(2)}`;
            }

            // Health indicator
            const healthScore = Math.min(100, (chartData.stats.total_products * 10));
            document.getElementById('healthBar').style.width = healthScore + '%';
            
            if (healthScore >= 70) {
                document.getElementById('healthBar').className = 'progress-bar bg-success';
                document.getElementById('healthBadge').className = 'badge bg-success';
                document.getElementById('healthBadge').textContent = 'Excellent';
            } else if (healthScore >= 40) {
                document.getElementById('healthBar').className = 'progress-bar bg-warning';
                document.getElementById('healthBadge').className = 'badge bg-warning';
                document.getElementById('healthBadge').textContent = 'Good';
            } else {
                document.getElementById('healthBar').className = 'progress-bar bg-danger';
                document.getElementById('healthBadge').className = 'badge bg-danger';
                document.getElementById('healthBadge').textContent = 'Needs Attention';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
            calculateInsights();
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

        #productList {
            max-height: 280px;
            overflow-y: auto;
        }

        #productList::-webkit-scrollbar {
            width: 6px;
        }

        #productList::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #productList::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        #productList::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</x-app-layout>