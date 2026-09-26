<x-app-layout title="Leftover Food Report">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <x-page-header>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('store.kitchen.production.leftovers.create') }}" class="btn btn-primary shadow-sm fw-bold">
                        <i class="mdi mdi-plus me-1"></i> Log Leftover
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
                                    <th class="ps-4 py-3 text-muted small fw-bold">DATE</th>
                                    <th class="py-3 text-muted small fw-bold">MENU ITEM</th>
                                    <th class="py-3 text-muted small fw-bold">PRODUCED</th>
                                    <th class="py-3 text-muted small fw-bold">SOLD</th>
                                    <th class="pe-4 py-3 text-muted small fw-bold">LEFTOVER</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4">{{ \Carbon\Carbon::parse($log->log_date)->format('d M Y') }}</td>
                                        <td class="fw-semibold">{{ $log->menuItem->name ?? 'N/A' }}</td>
                                        <td>{{ $log->quantity_produced }}</td>
                                        <td>{{ $log->quantity_sold }}</td>
                                        <td class="pe-4">
                                            <span class="badge {{ $log->quantity_leftover > 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $log->quantity_leftover }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No leftover logs yet.</td>
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
