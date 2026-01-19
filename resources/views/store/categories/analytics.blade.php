<x-app-layout title="Category Analytics">
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Analytics: {{ $category->name }}</h4>
                <a href="{{ route('store.categories.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Total Products</h6>
                        <h3>{{ $stats->total_products }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h6>Total Stock Qty</h6>
                        <h3>{{ $stats->total_qty ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Inventory Value</h6>
                        <h3>₹{{ number_format($stats->total_value, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Top 5 Products by Stock</h5>
                <canvas id="catChart" height="100"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('catChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Stock Quantity',
                    data: {!! json_encode($data) !!},
                    backgroundColor: ['#FFC100', '#4B49AC', '#248AFD', '#FF4747', '#57B657']
                }]
            }
        });
    </script>
    @endpush
</x-app-layout>