<x-app-layout title="Ticket #{{ $ticket->ticket_number }}">
    <div class="container-fluid py-4">
        
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('store.support.index') }}" class="text-muted text-decoration-none small">
                    <i class="mdi mdi-arrow-left"></i> Back to List
                </a>
                <h4 class="fw-bold mt-1">
                    {{ $ticket->subject }} 
                    <span class="badge bg-{{ $ticket->status == 'open' ? 'success' : 'secondary' }} fs-6 ms-2">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </h4>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">SLA Due</small>
                <span class="fw-bold {{ $ticket->isOverdue() ? 'text-danger' : 'text-dark' }}">
                    {{ $ticket->sla_due_at->format('d M, h:i A') }}
                </span>
            </div>
        </div>

        <div class="row">
            {{-- Chat Area --}}
            <div class="col-lg-8">
                {{-- Original Ticket Description --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body bg-light">
                        <small class="text-muted fw-bold">Original Request:</small>
                        <p class="mt-2 mb-0">{{ $ticket->description }}</p>
                    </div>
                </div>

                {{-- Messages Timeline --}}
                <div class="d-flex flex-column gap-3 mb-4">
                    @foreach($ticket->messages as $msg)
                        <div class="d-flex {{ $msg->sender_type == 'App\Models\StoreUser' ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="card border-0 shadow-sm" style="max-width: 80%; {{ $msg->sender_type == 'App\Models\StoreUser' ? 'background: #e3f2fd;' : 'background: #fff;' }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-bold {{ $msg->sender_type == 'App\Models\StoreUser' ? 'text-primary' : 'text-dark' }}">
                                            {{ $msg->sender_type == 'App\Models\StoreUser' ? 'You' : 'Support Team' }}
                                        </small>
                                        <small class="text-muted ms-3" style="font-size: 0.75rem;">
                                            {{ $msg->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="mb-0 text-dark">{{ $msg->message }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Reply Box --}}
                @if($ticket->isOpen())
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('store.support.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <label class="form-label fw-bold">Reply</label>
                            <textarea name="message" class="form-control mb-3" rows="3" placeholder="Type your message here..." required></textarea>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="mdi mdi-send me-1"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <div class="alert alert-secondary text-center">
                    <i class="mdi mdi-lock me-1"></i> This ticket is <strong>{{ $ticket->status }}</strong>. You cannot reply.
                </div>
                @endif
            </div>

            {{-- Sidebar Info --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Ticket Info</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Ticket ID:</span>
                                <span class="fw-bold">{{ $ticket->ticket_number }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Category:</span>
                                <span>{{ $ticket->category }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Priority:</span>
                                <span class="badge bg-{{ $ticket->priority == 'critical' ? 'danger' : 'info' }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">Created:</span>
                                <span>{{ $ticket->created_at->format('d M Y') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>