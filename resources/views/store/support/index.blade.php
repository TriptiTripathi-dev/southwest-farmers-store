<x-app-layout title="My Support Tickets">
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0 bg-white p-3 rounded shadow-sm">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-dark">
                        <i class="mdi mdi-home-outline me-1"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">
                    Support Tickets
                </li>
            </ol>
        </nav>

        {{-- Metrics Logic (Inline for immediate functionality) --}}
        @php
        $storeId = auth()->user()->store_id;
        $openCount = \App\Models\SupportTicket::where('store_id', $storeId)->where('status', 'open')->count();
        $resolvedCount = \App\Models\SupportTicket::where('store_id', $storeId)->where('status', 'resolved')->count();
        $totalCount = \App\Models\SupportTicket::where('store_id', $storeId)->count();
        @endphp

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-4 border-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase text-muted small fw-bold mb-1">Open Tickets</h6>
                                <h2 class="text-primary fw-bold mb-0">{{ $openCount }}</h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                <i class="mdi mdi-ticket-confirmation-outline fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase text-muted small fw-bold mb-1">Resolved</h6>
                                <h2 class="text-success fw-bold mb-0">{{ $resolvedCount }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="mdi mdi-check-circle-outline fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-4 border-secondary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase text-muted small fw-bold mb-1">Total Tickets</h6>
                                <h2 class="text-secondary fw-bold mb-0">{{ $totalCount }}</h2>
                            </div>
                            <div class="bg-secondary bg-opacity-10 p-3 rounded-circle text-secondary">
                                <i class="mdi mdi-history fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-lifebuoy me-2 text-primary"></i>My Tickets
                </h5>
                <div class="d-flex gap-2">
                    {{-- Filter Form --}}
                    <form method="GET" class="d-flex">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="mdi mdi-filter-variant"></i>
                            </span>
                            <select name="status" class="form-select border-start-0 ps-0" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </form>

                    {{-- Filter Form --}}

                    @if(Auth::user()->hasPermission('raise_ticket'))
                    <a href="{{ route('store.support.create') }}" class="btn btn-sm btn-primary shadow-sm px-3 d-flex align-items-center">
                        <i class="mdi mdi-plus-circle me-1"></i> Raise Ticket
                    </a>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Ticket ID</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Subject</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Category</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Status</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Created</th>
                            <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('store.support.show', $ticket->id) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td>{{ Str::limit($ticket->subject, 40) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $ticket->category }}
                                </span>
                            </td>
                            <td>
                                @php
                                $statusColors = [
                                'open' => 'success',
                                'in_progress' => 'info',
                                'waiting' => 'warning',
                                'resolved' => 'primary',
                                'closed' => 'secondary'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$ticket->status] ?? 'secondary' }} bg-opacity-10 text-{{ $statusColors[$ticket->status] ?? 'secondary' }} border border-{{ $statusColors[$ticket->status] ?? 'secondary' }} border-opacity-25 px-2 py-1">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                <i class="mdi mdi-calendar-blank me-1"></i>{{ $ticket->created_at->format('d M Y') }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('store.support.show', $ticket->id) }}" class="btn btn-sm btn-white border shadow-sm text-dark hover-primary">
                                    View <i class="mdi mdi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted opacity-75">
                                    <i class="mdi mdi-inbox-outline fs-1 d-block mb-2"></i>
                                    <span class="fw-medium">No support tickets found.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>