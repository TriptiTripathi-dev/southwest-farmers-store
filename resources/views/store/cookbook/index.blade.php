<x-app-layout title="Cookbook Builder">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <x-page-header>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('store.cookbook.create') }}" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center">
                        <i class="mdi mdi-plus fs-5 me-1"></i> New Recipe
                    </a>
                </div>
            </x-page-header>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold">RECIPE</th>
                                    <th class="py-3 text-muted small fw-bold">MENU ITEM</th>
                                    <th class="py-3 text-muted small fw-bold">PREP / COOK TIME</th>
                                    <th class="py-3 text-muted small fw-bold">YIELD</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recipes as $recipe)
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $recipe->name }}</td>
                                        <td>{{ $recipe->menuItem->name ?? 'N/A' }}</td>
                                        <td>{{ $recipe->prep_time_minutes ?? '—' }}m / {{ $recipe->cook_time_minutes ?? '—' }}m</td>
                                        <td>{{ $recipe->yield_quantity ? $recipe->yield_quantity . ' ' . $recipe->yield_unit : '—' }}</td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('store.cookbook.show', $recipe->id) }}" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-eye"></i></a>
                                            <a href="{{ route('store.cookbook.edit', $recipe->id) }}" class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-pencil"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No recipes yet. Create one to get started.</td>
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
