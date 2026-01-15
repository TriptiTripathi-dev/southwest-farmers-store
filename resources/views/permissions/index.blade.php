<x-app-layout title="Permissions List | Store Panel">
    <div class="content">
        <div class="container-fluid">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1"><h4 class="fs-18 fw-semibold m-0">Store Permissions</h4></div>
                <div class="text-end">
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i> Add Permission
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered dt-responsive nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>Group</th>
                                        <th>Name</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $group => $groupPermissions)
                                        @foreach ($groupPermissions as $permission)
                                            <tr>
                                                <td><span class="badge bg-light text-dark">{{ $group }}</span></td>
                                                <td>{{ $permission->name }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-light border">
                                                        <i class="mdi mdi-pencil text-primary"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light border">
                                                            <i class="mdi mdi-delete text-danger"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>