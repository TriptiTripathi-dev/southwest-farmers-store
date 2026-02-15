<x-app-layout title="Subcategories">
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Manrope', sans-serif; }
        
        .hover-lift { transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .letter-spacing-1 { letter-spacing: 0.5px; }

        /* Custom Select2 fixes for Bootstrap 5 Input Group */
        .input-group > .select2-container--bootstrap-5 {
            flex: 1 1 auto;
            width: 1% !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single {
            background-color: #f8f9fa !important; /* Matches bg-light */
            border: none !important;
            min-height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection--single {
            box-shadow: none !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
            color: #6c757d;
        }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">
            
            {{-- HEADER SECTION --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-sitemap fs-5"></i>
                        </div>
                        Subcategories
                    </h4>
                    <p class="text-muted small mb-0 mt-1 ms-5">Manage and organize product subcategories</p>
                </div>
                
                {{-- ACTIONS --}}
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-success text-white shadow-sm fw-bold d-flex align-items-center hover-lift" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="mdi mdi-upload fs-5 me-1"></i> Import
                    </button>
                    <a href="{{ route('store.subcategories.export') }}" class="btn btn-info text-white shadow-sm fw-bold d-flex align-items-center hover-lift">
                        <i class="mdi mdi-download fs-5 me-1"></i> Export
                    </a>
                    <a href="{{ route('store.subcategories.create') }}" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center hover-lift">
                        <i class="mdi mdi-plus fs-5 me-1"></i> Add New
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
                                <input type="text" name="search" class="form-control bg-light border-start-0 py-2" placeholder="Search subcategories..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-group shadow-sm flex-nowrap">
                                <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="mdi mdi-filter-variant"></i></span>
                                {{-- Added ID for Select2 --}}
                                <select name="category_id" id="categorySelect" class="form-select bg-light">
                                    <option value="">Filter by Parent Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow-sm">Filter Results</button>
                        </div>
                    </form>
                </div>

                {{-- DATA TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive custom-scrollbar">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Subcategory</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Parent Category</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1">Code</th>
                                    <th class="py-3 text-muted small fw-bold text-uppercase letter-spacing-1 text-center">Type</th>
                                    <th class="pe-4 py-3 text-end text-muted small fw-bold text-uppercase letter-spacing-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subcategories as $sub)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark fs-6">{{ $sub->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                            {{ $sub->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted font-monospace small"><i class="mdi mdi-barcode me-1"></i>{{ $sub->code }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(is_null($sub->store_id))
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                                <i class="mdi mdi-earth me-1"></i> Warehouse
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                                <i class="mdi mdi-storefront me-1"></i> Store
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('store.subcategories.analytics', $sub->id) }}" class="btn btn-sm btn-light border shadow-sm text-info" title="Analytics">
                                                <i class="mdi mdi-chart-line fs-6"></i>
                                            </a>
                                            
                                            @if(is_null($sub->store_id))
                                                <div class="btn btn-sm btn-light border text-muted opacity-75 pe-none" title="Global Subcategory (Locked)">
                                                    <i class="mdi mdi-lock fs-6"></i>
                                                </div>
                                            @else
                                                <a href="{{ route('store.subcategories.edit', $sub->id) }}" class="btn btn-sm btn-light border shadow-sm text-primary" title="Edit">
                                                    <i class="mdi mdi-pencil fs-6"></i>
                                                </a>
                                                <form action="{{ route('store.subcategories.destroy', $sub->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" title="Delete">
                                                        <i class="mdi mdi-trash-can fs-6"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3">
                                            <i class="mdi mdi-file-tree" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Subcategories Found</h6>
                                        <p class="text-muted small mb-0">Try adjusting your search or add a new subcategory.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- PAGINATION --}}
                @if($subcategories->hasPages())
                <div class="card-footer bg-white border-top p-3 rounded-bottom-4">
                    {{ $subcategories->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('store.subcategories.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-primary"><i class="mdi mdi-upload me-2"></i>Import Subcategories</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info small rounded-3 mb-3">
                            <i class="mdi mdi-information me-1"></i> Required Columns: <strong>category_code</strong>, <strong>name</strong>, <strong>code</strong>.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Excel File (.xlsx, .csv) <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control bg-light border-0 shadow-sm py-2" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success text-white rounded-pill px-4 fw-bold shadow-sm">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the Filter dropdown
            $('#categorySelect').select2({
                theme: 'bootstrap-5',
                placeholder: "Search Parent Category...",
                allowClear: true
            }).on('select2:select select2:unselect', function (e) {
                // Auto-submit the form when a category is selected or cleared
                $(this).closest('form').submit();
            });

            // Delete Confirmation
            $('.delete-btn').click(function() {
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the subcategory permanently! You won't be able to revert this.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger rounded-pill px-4',
                        cancelButton: 'btn btn-secondary rounded-pill px-4 ms-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>