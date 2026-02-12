<x-app-layout title="Notifications">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="h4 mb-0">All Notifications</h4>
        <form action="{{ route('store.notifications.clear') }}" method="POST" onsubmit="return confirm('Clear all notifications?');">
            @csrf
            <button class="btn btn-outline-danger btn-sm">Clear All</button>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notif)
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start {{ $notif->read_at ? '' : 'bg-light' }}">
                <div class="d-flex gap-3">
                    <div class="mt-1">
                        <span class="avatar-title rounded-circle bg-{{ $notif->type == 'danger' ? 'danger' : ($notif->type == 'warning' ? 'warning' : 'primary') }} text-white" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                            <i class="mdi mdi-bell"></i>
                        </span>
                    </div>
                    <div>
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 {{ $notif->read_at ? 'text-muted' : 'text-dark fw-bold' }}">{{ $notif->title }}</h6>
                        </div>
                        <p class="mb-1 text-muted small">{{ $notif->message }}</p>
                        <small class="text-muted">{{ $notif->created_at->format('M d, Y h:i A') }}</small>
                        
                        @if($notif->url)
                            <div class="mt-2">
                                <a href="{{ route('store.notifications.read', $notif->id) }}" class="btn btn-sm btn-link p-0">View Details &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    @if(!$notif->read_at)
                    <span class="badge bg-primary rounded-pill">New</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center p-5 text-muted">
                <i class="mdi mdi-bell-off-outline fs-1 opacity-25"></i>
                <p class="mt-2">You have no notifications.</p>
            </div>
            @endforelse
        </div>
        <div class="card-footer">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
</x-app-layout>