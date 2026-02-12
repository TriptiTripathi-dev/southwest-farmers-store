<x-app-layout title="Promotions & Marketing">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Promotions & Marketing</h2>
        @if(Auth::user()->hasPermission('create_promotion'))
        <a href="{{ route('store.promotions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Campaign
        </a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Scope</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotions as $promo)
                        <tr>
                            <td>
                                <strong>{{ $promo->name }}</strong>
                            </td>
                            <td>
                                @if($promo->code)
                                    <span class="badge bg-dark">{{ $promo->code }}</span>
                                @else
                                    <span class="text-muted small">Auto-Apply</span>
                                @endif
                            </td>
                            <td>
                                @if($promo->type == 'percentage')
                                    <span class="badge bg-info text-dark">{{ $promo->value }}% OFF</span>
                                @elseif($promo->type == 'fixed_amount')
                                    <span class="badge bg-success">${{ number_format($promo->value, 2) }} OFF</span>
                                @else
                                    <span class="badge bg-warning text-dark">Buy 1 Get 1</span>
                                @endif
                            </td>
                            <td>
                                @if($promo->product)
                                    Item: {{ Str::limit($promo->product->product_name, 20) }}
                                @elseif($promo->category)
                                    Category: {{ $promo->category->name }}
                                @else
                                    <span class="badge bg-secondary">Global (All Items)</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ $promo->start_date->format('d M') }} - {{ $promo->end_date->format('d M Y') }}
                                </small>
                                @if(!$promo->isValid())
                                    <br><span class="badge bg-danger" style="font-size: 0.65rem;">Expired</span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->hasPermission('edit_promotion'))
                                <form action="{{ route('store.promotions.status', $promo->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-{{ $promo->is_active ? 'success' : 'secondary' }}" style="min-width: 80px;">
                                        {{ $promo->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                                @else
                                    <span class="badge bg-{{ $promo->is_active ? 'success' : 'secondary' }}">{{ $promo->is_active ? 'Active' : 'Inactive' }}</span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->hasPermission('delete_promotion'))
                                <form action="{{ route('store.promotions.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Delete this promotion?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-trash-can"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center p-4">No active promotions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $promotions->links() }}
        </div>
    </div>
</div>
</x-app-layout>