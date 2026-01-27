<x-app-layout title="SubCategory Analytics">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">{{ $subcategory->name }}</h3>
                        <p class="text-muted mb-0">
                            <i class="mdi mdi-folder-outline me-1"></i>SubCategory Analytics & Performance Metrics
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
                                <span class="badge bg-light text-info mt-2">
                                    <i class="mdi mdi-tag-multiple"></i> Items
                                </span>
                            </div>
                            <div class="icon-wrapper bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-package-variant-closed text-info" style="font-size: 28px;"></i>
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
                                    <i class="mdi mdi-layers-triple"></i> Units
                                </span>
                            </div>
                            <div class="icon-wrapper bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-archive text-warning" style="font-size: 28px;"></i>
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
                                <h3 class="mb-0 fw-bold">${{ number_format($stats->total_value, 2) }}</h3>
                                <span class="badge bg-light text-success mt-2">
                                    <i class="mdi mdi-wallet"></i> Total Worth
                                </span>
                            </div>
                            <div class="icon-wrapper bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-cash text-success" style="font-size: 28px;"></i>
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
                                <p class="text-muted mb-1 small">Avg. Unit Value</p>
                                <h3 class="mb-0 fw-bold" id="avgUnitValue">$0.00</h3>
                                <span class="badge bg-light text-primary mt-2">
                                    <i class="mdi mdi-trending-up"></i> Per Unit
                                </span>
                            </div>
                            <div class="icon-wrapper bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="mdi mdi-chart-areaspline text-primary" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analysis Row -->
        <div class="row mb-4">
            <!-- Main Chart -->
            <div class="col-lg-8 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title mb-1 fw-bold">Stock Distribution Analysis</h5>
                                <p class="text-muted mb-0 small">Top performing products in this subcategory</p>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="updateChartType('bar')">
                                    <i class="mdi mdi-chart-bar"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="updateChartType('line')">
                                    <i class="mdi mdi-chart-line"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="updateChartType('polarArea')">
                                    <i class="mdi mdi-chart-donut-variant"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="updateChartType('radar')">
                                    <i class="mdi mdi-radar"></i>
                                </button>
                            </div>
                        </div>
                        <div style="position: relative; height: 320px;">
                            <canvas id="catChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Breakdown -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>Product Breakdown
                        </h5>
                        <div id="productBreakdown" class="mb-3">
                            <!-- Populated by JavaScript -->
                        </div>
                        <div class="mt-auto pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Coverage:</span>
                                <span class="fw-bold small" id="coveragePercent">0%</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div id="coverageBar" class="progress-bar bg-gradient" style="width: 0%; background: linear-gradient(90deg, #4B49AC, #7978E9);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights and Metrics Row -->
        <div class="row">
            <!-- Performance Metrics -->
            <div class="col-lg-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-speedometer text-success me-2"></i>Performance Metrics
                        </h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="metric-box border rounded p-3 text-center h-100">
                                    <i class="mdi mdi-star-circle text-warning" style="font-size: 24px;"></i>
                                    <div class="mt-2">
                                        <div class="text-muted small mb-1">Top Product Share</div>
                                        <h4 class="mb-0 fw-bold" id="topProductShare">0%</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-box border rounded p-3 text-center h-100">
                                    <i class="mdi mdi-chart-box text-info" style="font-size: 24px;"></i>
                                    <div class="mt-2">
                                        <div class="text-muted small mb-1">Avg Stock/Product</div>
                                        <h4 class="mb-0 fw-bold" id="avgStockPerProduct">0</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="metric-box border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small fw-bold">
                                            <i class="mdi mdi-shield-check text-success me-1"></i>
                                            SubCategory Health
                                        </span>
                                        <span class="badge" id="healthBadge">Calculating...</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div id="healthBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Insights -->
            <div class="col-lg-6 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="mdi mdi-lightbulb-on text-warning me-2"></i>Key Insights
                        </h5>
                        <div class="insights-container">
                            <div class="insight-item mb-3 p-3 border-start border-4 border-info bg-light rounded">
                                <div class="d-flex align-items-start">
                                    <i class="mdi mdi-information text-info me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1 small">Inventory Diversity</strong>
                                        <span class="text-muted small" id="insight1">Analyzing product range...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="insight-item mb-3 p-3 border-start border-4 border-warning bg-light rounded">
                                <div class="d-flex align-items-start">
                                    <i class="mdi mdi-alert-circle text-warning me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1 small">Stock Concentration</strong>
                                        <span class="text-muted small" id="insight2">Evaluating distribution...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="insight-item p-3 border-start border-4 border-success bg-light rounded">
                                <div class="d-flex align-items-start">
                                    <i class="mdi mdi-check-circle text-success me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1 small">Value Assessment</strong>
                                        <span class="text-muted small" id="insight3">Computing metrics...</span>
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
        let subCategoryChart;
        const chartData = {
            labels: {!! json_encode($labels) !!},
            data: {!! json_encode($data) !!},
            stats: {
                total_products: {{ $stats->total_products }},
                total_qty: {{ $stats->total_qty ?? 0 }},
                total_value: {{ $stats->total_value }}
            }
        };

        const colorSchemes = {
            vibrant: ['#FFC100', '#4B49AC', '#248AFD', '#FF4747', '#57B657'],
            gradient: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe'],
            ocean: ['#2E3192', '#1BFFFF', '#00C9FF', '#0099FF', '#0066FF'],
            sunset: ['#FF6B6B', '#FFA07A', '#FFD700', '#FF8C00', '#FF4500']
        };

        function initChart(type = 'bar') {
            const ctx = document.getElementById('catChart').getContext('2d');
            
            if (subCategoryChart) {
                subCategoryChart.destroy();
            }

            const isPolar = type === 'polarArea';
            const isRadar = type === 'radar';
            const isLine = type === 'line';

            subCategoryChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Stock Quantity',
                        data: chartData.data,
                        backgroundColor: isPolar || isRadar ? colorSchemes.vibrant.map(c => c + '80') : colorSchemes.vibrant.map(c => c + 'CC'),
                        borderColor: colorSchemes.vibrant,
                        borderWidth: isLine ? 3 : 2,
                        borderRadius: (!isPolar && !isRadar && !isLine) ? 8 : 0,
                        fill: isLine || isRadar,
                        tension: 0.4,
                        pointRadius: isLine ? 5 : 0,
                        pointHoverRadius: isLine ? 8 : 0,
                        pointBackgroundColor: isLine ? colorSchemes.vibrant : []
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: isPolar,
                            position: 'right',
                            labels: {
                                padding: 12,
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
                            backgroundColor: 'rgba(0, 0, 0, 0.85)',
                            padding: 14,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            borderColor: 'rgba(255,255,255,0.2)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.parsed.y !== undefined ? context.parsed.y : context.parsed.r || context.parsed;
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return ` ${value} units (${percentage}% of total)`;
                                }
                            }
                        }
                    },
                    scales: (isPolar || isRadar) ? (isRadar ? {
                        r: {
                            beginAtZero: true,
                            ticks: { font: { size: 10 } }
                        }
                    } : {}) : {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: { size: 11 },
                                padding: 8
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { size: 11 },
                                padding: 8
                            }
                        }
                    }
                }
            });

            updateProductBreakdown();
        }

        function updateChartType(type) {
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('button').classList.add('active');
            initChart(type);
        }

        function updateProductBreakdown() {
            const total = chartData.data.reduce((a, b) => a + b, 0);
            
            const breakdownHTML = chartData.labels.map((label, index) => {
                const qty = chartData.data[index];
                const percentage = total > 0 ? ((qty / total) * 100).toFixed(1) : 0;
                
                return `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 10px; height: 10px; background: ${colorSchemes.vibrant[index]}; border-radius: 2px;"></div>
                                <span class="small fw-bold text-truncate" style="max-width: 140px;" title="${label}">${label}</span>
                            </div>
                            <span class="badge bg-light text-dark">${percentage}%</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar" style="width: ${percentage}%; background: ${colorSchemes.vibrant[index]}"></div>
                            </div>
                            <span class="small text-muted" style="min-width: 40px;">${qty}</span>
                        </div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('productBreakdown').innerHTML = breakdownHTML;
            
            // Update coverage
            const coverage = Math.min(100, (chartData.labels.length / chartData.stats.total_products) * 100);
            document.getElementById('coveragePercent').textContent = coverage.toFixed(0) + '%';
            document.getElementById('coverageBar').style.width = coverage + '%';
        }

        function calculateMetrics() {
            const total = chartData.data.reduce((a, b) => a + b, 0);
            
            // Average unit value
            const avgUnitValue = total > 0 ? (chartData.stats.total_value / total).toFixed(2) : 0;
            document.getElementById('avgUnitValue').textContent = '$' + avgUnitValue;

            // Top product share
            const topShare = total > 0 ? ((chartData.data[0] / total) * 100).toFixed(1) : 0;
            document.getElementById('topProductShare').textContent = topShare + '%';

            // Average stock per product
            const avgStock = chartData.stats.total_products > 0 
                ? (chartData.stats.total_qty / chartData.stats.total_products).toFixed(0) 
                : 0;
            document.getElementById('avgStockPerProduct').textContent = avgStock;

            // Health calculation
            let healthScore = 0;
            if (chartData.stats.total_products > 0) {
                healthScore += Math.min(40, chartData.stats.total_products * 4); // Product diversity (max 40)
                healthScore += Math.min(30, (chartData.stats.total_qty / 100)); // Stock quantity (max 30)
                healthScore += Math.min(30, (chartData.stats.total_value / 10000)); // Value (max 30)
            }
            healthScore = Math.min(100, healthScore);

            document.getElementById('healthBar').style.width = healthScore + '%';
            
            if (healthScore >= 75) {
                document.getElementById('healthBar').className = 'progress-bar bg-success progress-bar-striped progress-bar-animated';
                document.getElementById('healthBadge').className = 'badge bg-success';
                document.getElementById('healthBadge').textContent = 'Excellent';
            } else if (healthScore >= 50) {
                document.getElementById('healthBar').className = 'progress-bar bg-info progress-bar-striped progress-bar-animated';
                document.getElementById('healthBadge').className = 'badge bg-info';
                document.getElementById('healthBadge').textContent = 'Good';
            } else if (healthScore >= 25) {
                document.getElementById('healthBar').className = 'progress-bar bg-warning progress-bar-striped progress-bar-animated';
                document.getElementById('healthBadge').className = 'badge bg-warning';
                document.getElementById('healthBadge').textContent = 'Fair';
            } else {
                document.getElementById('healthBar').className = 'progress-bar bg-danger progress-bar-striped progress-bar-animated';
                document.getElementById('healthBadge').className = 'badge bg-danger';
                document.getElementById('healthBadge').textContent = 'Needs Attention';
            }

            // Insights
            document.getElementById('insight1').textContent = 
                `This subcategory contains ${chartData.stats.total_products} unique products with varied stock levels`;
            
            document.getElementById('insight2').textContent = 
                `Top product accounts for ${topShare}% of total inventory, ${topShare > 50 ? 'indicating high concentration' : 'showing balanced distribution'}`;
            
            const valuePerProduct = chartData.stats.total_products > 0 
                ? (chartData.stats.total_value / chartData.stats.total_products).toFixed(2) 
                : 0;
            document.getElementById('insight3').textContent = 
                `Average value per product is $${valuePerProduct}, total subcategory worth $${chartData.stats.total_value.toFixed(2)}`;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
            calculateMetrics();
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.12) !important;
        }

        .progress {
            border-radius: 10px;
            background-color: #f0f0f0;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        #productBreakdown {
            max-height: 260px;
            overflow-y: auto;
            padding-right: 8px;
        }

        #productBreakdown::-webkit-scrollbar {
            width: 5px;
        }

        #productBreakdown::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 10px;
        }

        #productBreakdown::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        #productBreakdown::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .metric-box {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .metric-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .insight-item {
            transition: all 0.2s ease;
        }

        .insight-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .insights-container {
            max-height: 300px;
            overflow-y: auto;
        }

        .insights-container::-webkit-scrollbar {
            width: 4px;
        }

        .insights-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .insights-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
    </style>
</x-app-layout>