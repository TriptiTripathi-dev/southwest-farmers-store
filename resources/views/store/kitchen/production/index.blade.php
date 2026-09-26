<x-app-layout title="Production Logs">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <x-page-header>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('store.kitchen.production.create') }}" class="btn btn-primary shadow-sm fw-bold">
                        <i class="mdi mdi-plus me-1"></i> Log New Production
                    </a>
                    <a href="{{ route('store.kitchen.production.leftovers') }}" class="btn btn-outline-warning fw-bold">
                        <i class="mdi mdi-food-off me-1"></i> Leftover Report
                    </a>
                </div>
            </x-page-header>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">MENU ITEM</th>
                                    <th class="py-3 text-muted small fw-bold">QTY MADE</th>
                                    <th class="py-3 text-muted small fw-bold">YIELD (PLATES)</th>
                                    <th class="py-3 text-muted small fw-bold">DAILY TARGET</th>
                                    <th class="pe-4 py-3 text-muted small fw-bold">PRODUCED AT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productions as $log)
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $log->menuItem->name ?? 'N/A' }}</td>
                                        <td>{{ $log->quantity_made }} {{ $log->quantity_unit }}</td>
                                        <td>{{ $log->yield_plates ?? '-' }}</td>
                                        <td>{{ $log->daily_target ?: '-' }}</td>
                                        <td class="pe-4">{{ \Carbon\Carbon::parse($log->produced_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No production logs yet.</td>
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
