<x-app-layout title="Stock Audits">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Stock Audits (Physical Count)</h2>
        <a href="{{ route('store.audits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Start New Audit
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Audit #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Initiated By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                    <tr>
                        <td>{{ $audit->audit_number }}</td>
                        <td>{{ $audit->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $audit->status == 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($audit->status) }}
                            </span>
                        </td>
                        <td>{{ $audit->initiated_by }}</td> <td>
                            <a href="{{ route('store.audits.show', $audit->id) }}" class="btn btn-sm btn-outline-primary">
                                {{ $audit->status == 'completed' ? 'View Report' : 'Continue Counting' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center p-4">No audits found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $audits->links() }}
        </div>
    </div>
</div>
</x-app-layout>