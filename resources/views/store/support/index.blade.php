<x-app-layout title="My Support Tickets">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Support Tickets</h4>
            <a href="{{ route('store.support.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus"></i> Raise New Ticket
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Ticket #</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="fw-bold text-primary">{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->category }}</td>
                            <td>
                                <span class="badge bg-{{ $ticket->status == 'open' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('store.support.show', $ticket->id) }}" class="btn btn-sm btn-light">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No support tickets found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>