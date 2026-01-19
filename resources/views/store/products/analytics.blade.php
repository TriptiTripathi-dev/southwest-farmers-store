<x-app-layout title="Product Analytics">
    <div class="content-wrapper">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Analytics: {{ $product->product_name }}</h4>
                <a href="{{ route('store.products.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Current Stock</h5>
                        <h2>{{ $stock->quantity }} {{ $product->unit }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Selling Price</h5>
                        <h2>₹{{ number_format($stock->selling_price, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white">
                    <div class="card-body text-center">
                        <h5>Total Value</h5>
                        <h2>₹{{ number_format($stock->quantity * $stock->selling_price, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Consumption Trend (Last 30 Days)</h5>
                <canvas id="productChart" height="100"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('productChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [{
                    label: 'Quantity Sold/Used',
                    data: {!! json_encode($usage) !!},
                    borderColor: '#4B49AC',
                    backgroundColor: 'rgba(75, 73, 172, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });
    </script>
    @endpush
</x-app-layout>