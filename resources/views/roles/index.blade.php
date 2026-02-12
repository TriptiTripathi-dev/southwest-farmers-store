<x-app-layout title="Store Roles">
    <div class="content">
        <div class="container-fluid">
            <div class="py-3 d-flex align-items-center justify-content-between">
                <h4 class="fs-18 fw-semibold m-0">Store Roles</h4>
                @if(Auth::user()->hasPermission('manage_roles'))
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus me-1"></i> Create Role
                </a>
                @endif
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered mb-0 table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Role Name</th>
                                    <th>Permissions Count</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="badge bg-primary-subtle text-primary fs-12">{{ ucfirst($role->name) }}</span></td>
                                        <td><span class="badge bg-info text-white">{{ $role->permissions->count() }}</span></td>
                                        <td class="text-end">
                                            @if(Auth::user()->hasPermission('manage_roles'))
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-light border">
                                                <i class="mdi mdi-pencil text-primary"></i>
                                            </a>
                                            
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border">
                                                    <i class="mdi mdi-delete text-danger"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No roles found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>