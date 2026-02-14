<x-app-layout title="Stock Audits">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Stock Audits</h4>
                    <p class="text-muted small mb-0 mt-1">Manage and track physical inventory counts</p>
                </div>
                
                @if(Auth::user()->hasPermission('create_audit'))
                <div class="d-flex">
                    <a href="{{ route('store.audits.create') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold d-flex align-items-center transition-all hover-lift">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Start New Audit
                    </a>
                </div>
                @endif
            </div>

            {{-- MAIN CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center rounded-top-4">
                    <h6 class="mb-0 fw-bold text-dark">Audit History</h6>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Audit #</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Date</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Status</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Initiated By</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase letter-spacing-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($audits as $audit)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark">#{{ $audit->audit_number }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $audit->created_at->format('d M Y') }}</div>
                                        <div class="small text-muted">{{ $audit->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        @if($audit->status == 'completed')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                <i class="mdi mdi-check-circle-outline me-1"></i> Completed
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                                <i class="mdi mdi-progress-clock me-1"></i> {{ ucfirst($audit->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted fw-medium">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;">
                                                <i class="mdi mdi-account text-secondary"></i>
                                            </div>
                                            {{ $audit->initiated_by }}
                                        </div>
                                    </td> 
                                    <td class="pe-4 text-end">
                                        @if($audit->status == 'completed')
                                            <a href="{{ route('store.audits.show', $audit->id) }}" class="btn btn-sm btn-light border shadow-sm fw-bold text-primary rounded-pill px-3 transition-all hover-bg-primary">
                                                <i class="mdi mdi-eye-outline me-1"></i> View Report
                                            </a>
                                        @else
                                            @if(Auth::user()->hasPermission('perform_audit'))
                                            <a href="{{ route('store.audits.show', $audit->id) }}" class="btn btn-sm btn-primary shadow-sm fw-bold rounded-pill px-3 transition-all hover-lift">
                                                <i class="mdi mdi-clipboard-edit-outline me-1"></i> Continue
                                            </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-clipboard-text-off-outline" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Audits Found</h6>
                                        <p class="text-muted small mb-0">Start a new physical count to see it here.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Dynamic Pagination Footer --}}
                @if($audits->hasPages())
                <div class="card-footer bg-white border-top py-3 rounded-bottom-4">
                    {{ $audits->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>