<x-app-layout title="Stores | Inventory System">

    <div class="container-fluid">

        <div class="py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h4 class="fs-18 fw-semibold m-0">Store List</h4>

            <div class="d-flex align-items-center gap-2">
               

                <form method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search store name or code...">

                      

                        <button class="btn btn-secondary">
                            <i class="mdi mdi-magnify"></i>
                        </button>

                        @if (request('search') || request('status') !== null)
                            <a href="{{ route('store.index') }}" class="btn btn-danger">
                                <i class="mdi mdi-close"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Store Name</th>
                                <th>Store Code</th>
                                <th>City</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stores as $store)
                                <tr>
                                    <td>{{ $store->store_name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $store->store_code }}
                                        </span>
                                    </td>
                                    <td>{{ $store->city ?? 'N/A' }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                data-id="{{ $store->id }}"
                                                {{ $store->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{route('store.edit',$store->id)}}"
                                            class="btn btn-sm btn-light border">
                                            <i class="mdi mdi-pencil text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No stores found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

              

            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            $('.status-toggle').change(function() {

                let checkbox = $(this);
                let id = checkbox.data('id');
                let status = checkbox.is(':checked') ? 1 : 0;

                // Revert visually first (wait for confirmation)
                checkbox.prop('checked', !checkbox.is(':checked'));

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Change store status?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!',
                    confirmButtonColor: '#4CAF50', // Green theme
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Apply check visually
                        checkbox.prop('checked', !checkbox.prop('checked'));

                        $.post("{{ route('store.update-status') }}", {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            status: status
                        }, function(res) {
                            Swal.fire({
                                toast: true,
                                icon: 'success',
                                position: 'top-end',
                                timer: 3000,
                                showConfirmButton: false,
                                title: res.message
                            });
                        }).fail(function() {
                            // Revert if error
                            checkbox.prop('checked', !checkbox.prop('checked'));
                            Swal.fire({
                                toast: true,
                                icon: 'error',
                                title: 'Something went wrong!'
                            });
                        });
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>