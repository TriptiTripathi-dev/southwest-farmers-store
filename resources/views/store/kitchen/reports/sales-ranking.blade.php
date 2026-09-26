<x-app-layout title="Sales Ranking Report">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <x-page-header />

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">RANK</th>
                                    <th class="py-3 text-muted small fw-bold">MENU ITEM</th>
                                    <th class="py-3 text-muted small fw-bold">CATEGORY</th>
                                    <th class="py-3 text-muted small fw-bold">TOTAL QTY SOLD</th>
                                    <th class="pe-4 py-3 text-muted small fw-bold">TOTAL REVENUE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1; @endphp
                                @forelse($rankedItems as $item)
                                    <tr>
                                        <td class="ps-4">#{{ $rank++ }}</td>
                                        <td class="fw-semibold">{{ $item->name }}</td>
                                        <td>{{ $item->categoryName }}</td>
                                        <td><strong>{{ number_format($item->total_quantity_sold) }}</strong></td>
                                        <td class="pe-4">${{ number_format($item->total_revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No sales data available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
