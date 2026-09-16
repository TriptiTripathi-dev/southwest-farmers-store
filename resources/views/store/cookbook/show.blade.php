<x-app-layout title="{{ $recipe->name }}">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <nav class="mb-1">
                        <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="page-breadcrumb-link">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('store.cookbook.index') }}" class="page-breadcrumb-link">Cookbook Builder</a></li>
                            <li class="breadcrumb-item text-muted">{{ $recipe->name }}</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold mb-0 text-dark">{{ $recipe->name }}</h4>
                    <p class="text-muted small mb-0 mt-1">For: {{ $recipe->menuItem->name ?? 'N/A' }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('store.cookbook.edit', $recipe->id) }}" class="btn btn-outline-secondary fw-bold">
                        <i class="mdi mdi-pencil me-1"></i> Edit
                    </a>
                    <form action="{{ route('store.cookbook.destroy', $recipe->id) }}" method="POST" onsubmit="return confirm('Delete this recipe? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger fw-bold">
                            <i class="mdi mdi-delete me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Details</h6>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Prep Time</span>
                                <span class="fw-semibold">{{ $recipe->prep_time_minutes ?? '—' }} min</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">Cook Time</span>
                                <span class="fw-semibold">{{ $recipe->cook_time_minutes ?? '—' }} min</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted small">Yield</span>
                                <span class="fw-semibold">{{ $recipe->yield_quantity ? $recipe->yield_quantity . ' ' . $recipe->yield_unit : '—' }}</span>
                            </div>
                            @if($recipe->description)
                                <hr>
                                <p class="small text-muted mb-0">{{ $recipe->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Steps</h6>
                            @forelse($recipe->steps as $step)
                                <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="fw-bold text-primary" style="min-width: 28px;">{{ $step->step_number }}.</div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1">{{ $step->instruction }}</p>
                                        @if($step->timer_minutes)
                                            <span class="badge bg-light text-dark border"><i class="mdi mdi-timer-outline"></i> {{ $step->timer_minutes }} min</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No steps added for this recipe.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
