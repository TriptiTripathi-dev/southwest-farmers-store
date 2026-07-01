<x-app-layout title="Menu Items">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Menu Items</h4>
                    <p class="text-muted small mb-0 mt-1">Manage dishes and prepared food items sold in the store</p>
                </div>
                
                {{-- ACTIONS --}}
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('menu-items.create') }}" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Add New Menu Item
                    </a>
                </div>
            </div>

            {{-- MAIN CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                
                {{-- FILTER BAR --}}
                <div class="card-header bg-white border-bottom p-3 p-md-4 rounded-top-4">
                    <form method="GET" class="row g-2 align-items-center m-0">
                        <div class="col-12 col-md-5">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-magnify fs-5"></i></span>
                                <input type="text" name="search" class="form-control bg-light border-start-0 py-2" placeholder="Search item name..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-muted px-3"><i class="mdi mdi-filter-variant"></i></span>
                                <select name="category_id" class="form-select bg-light py-2" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow-sm">Search</button>
                        </div>
                    </form>
                </div>

                {{-- DATA TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Item Info</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Category</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Price</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-center">Status</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase letter-spacing-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->image)
                                                <img src="{{ Storage::disk('r2')->url($item->image) }}" alt="{{ $item->name }}" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 45px; height: 45px;">
                                                    <i class="mdi mdi-food fs-4"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                                <div class="text-muted small text-truncate" style="max-width: 300px;">{{ $item->description }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                            {{ $item->category->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">${{ number_format($item->price, 2) }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $item->is_active ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-danger bg-opacity-10 text-danger border border-danger' }} border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('menu-items.edit', $item->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit">
                                                <i class="mdi mdi-pencil fs-6"></i>
                                            </a>
                                            <form action="{{ route('menu-items.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" title="Delete Menu Item">
                                                    <i class="mdi mdi-trash-can fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-food-fork-drink" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Menu Items Found</h6>
                                        <p class="text-muted small mb-0">Add a new prepared dish to the menu catalog.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINATION --}}
                @if($items->hasPages())
                <div class="card-footer bg-white border-top p-3 rounded-bottom-4">
                    {{ $items->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
