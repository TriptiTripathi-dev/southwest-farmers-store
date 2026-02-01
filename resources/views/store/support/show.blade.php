<x-app-layout title="Ticket #{{ $ticket->ticket_number }}">
    <div class="container-fluid">
        
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0 bg-white p-3 rounded shadow-sm">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-dark">
                        <i class="mdi mdi-home-outline me-1"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('store.support.index') }}" class="text-decoration-none text-dark">
                        Support Tickets
                    </a>
                </li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">
                    {{ $ticket->ticket_number }}
                </li>
            </ol>
        </nav>

        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('store.support.index') }}" class="btn btn-white border shadow-sm text-dark fw-medium">
                <i class="mdi mdi-arrow-left me-1"></i> Back to My Tickets
            </a>
        </div>

        <div class="row g-4">
            {{-- Left Column: Chat & Info --}}
            <div class="col-lg-8">
                
                {{-- Ticket Header Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="fw-bold text-dark mb-1">{{ $ticket->subject }}</h4>
                                <div class="d-flex align-items-center gap-3 text-muted small mt-2">
                                    <span><i class="mdi mdi-calendar me-1 text-primary"></i> {{ $ticket->created_at->format('d M Y, h:i A') }}</span>
                                    <span><i class="mdi mdi-tag me-1 text-primary"></i> {{ $ticket->category }}</span>
                                </div>
                            </div>
                            @php
                                $statusColors = [
                                    'open' => 'success',
                                    'in_progress' => 'info',
                                    'waiting' => 'warning',
                                    'resolved' => 'primary',
                                    'closed' => 'secondary'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$ticket->status] ?? 'secondary' }} fs-6 px-3 py-2 shadow-sm">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                        <div class="bg-light p-3 rounded-3 border-start border-4 border-primary">
                            <h6 class="fw-bold text-muted small text-uppercase mb-2">Original Request</h6>
                            <p class="mb-0 text-dark">{{ $ticket->description }}</p>
                        </div>
                    </div>
                </div>

                {{-- Chat Timeline --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                        <i class="mdi mdi-forum-outline text-primary fs-5"></i>
                        <h5 class="card-title mb-0 fw-bold text-dark">Discussion</h5>
                    </div>
                    
                    <div class="card-body bg-light" style="max-height: 550px; overflow-y: auto;">
                        @forelse($ticket->messages as $msg)
                            @php
                                // Check if sender is StoreUser (Me) or WareUser (Support)
                                $isMe = $msg->sender_type == 'App\Models\StoreUser'; 
                            @endphp

                            <div class="d-flex mb-4 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}" style="max-width: 85%;">
                                    
                                    <div class="d-flex align-items-center mb-1">
                                        @if(!$isMe)
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-white text-primary border shadow-sm" style="font-size: 0.8rem;">
                                                    <i class="mdi mdi-headset"></i>
                                                </span>
                                            </div>
                                        @endif
                                        <span class="fw-bold small {{ $isMe ? 'text-dark' : 'text-primary' }}">
                                            {{ $isMe ? 'You' : 'Support Team' }}
                                        </span>
                                        <span class="text-muted ms-2 small" style="font-size: 0.7rem;">
                                            {{ $msg->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <div class="card border-0 shadow-sm p-3 {{ $isMe ? 'bg-primary bg-opacity-10 text-dark' : 'bg-white' }}" 
                                         style="border-radius: {{ $isMe ? '15px 0 15px 15px' : '0 15px 15px 15px' }};">
                                        
                                        <p class="mb-0" style="font-size: 0.95rem;">{{ $msg->message }}</p>
                                        
                                        @if($msg->attachments->count() > 0)
                                            <div class="mt-2 pt-2 border-top border-secondary border-opacity-10">
                                                @foreach($msg->attachments as $att)
                                                    <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="d-inline-flex align-items-center badge bg-white text-dark border p-2 me-1 text-decoration-none shadow-sm mt-1">
                                                        <i class="mdi mdi-file-document-outline me-1 text-primary"></i> {{ $att->file_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="mdi mdi-chat-outline fs-1 opacity-50"></i>
                                <p class="mt-2">No messages yet.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Reply Form --}}
                    @if($ticket->status !== 'closed')
                    <div class="card-footer bg-white p-4 border-top">
                        <form action="{{ route('store.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            <label class="form-label fw-bold text-dark mb-2">Reply to Support</label>
                            <textarea name="message" class="form-control mb-3 bg-light border-0" rows="3" placeholder="Type your message here..." style="resize: none;" required></textarea>
                            <div class="invalid-feedback">Please write a message.</div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                {{-- File Upload --}}
                                <div class="position-relative">
                                    <button type="button" class="btn btn-sm btn-light border text-muted" onclick="document.getElementById('attInput').click()">
                                        <i class="mdi mdi-paperclip me-1"></i> Attach Files
                                    </button>
                                    <input type="file" id="attInput" name="attachments[]" multiple class="d-none" onchange="document.getElementById('fileCount').innerText = this.files.length + ' files'">
                                    <span id="fileCount" class="small text-primary fw-bold ms-1"></span>
                                </div>

                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="mdi mdi-send me-1"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="card-footer bg-light text-center py-4">
                        <span class="text-muted fw-medium"><i class="mdi mdi-lock me-1"></i> This ticket is closed. You cannot reply.</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Info --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark">Ticket Info</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small">Ticket ID</span>
                                <span class="fw-bold text-primary">{{ $ticket->ticket_number }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small">Priority</span>
                                <span class="badge bg-{{ $ticket->priority == 'critical' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : 'info') }} bg-opacity-10 text-{{ $ticket->priority == 'critical' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : 'info') }} px-2 py-1">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small">SLA Deadline</span>
                                <div class="text-end">
                                    <div class="fw-bold {{ $ticket->isOverdue() ? 'text-danger' : 'text-dark' }}">
                                        {{ $ticket->sla_due_at->format('d M, h:i A') }}
                                    </div>
                                    @if($ticket->isOverdue())
                                        <small class="badge bg-danger text-white mt-1">OVERDUE</small>
                                    @endif
                                </div>
                            </li>
                        </ul>
                    </div>
                    @if($ticket->status !== 'closed')
                        <div class="card-footer bg-light p-3">
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0" role="alert">
                                <i class="mdi mdi-information-outline fs-4 me-2"></i>
                                <div class="small lh-sm">
                                    Our support team usually replies within <strong>24 hours</strong>.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>