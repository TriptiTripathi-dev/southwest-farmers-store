<x-app-layout title="Staff Management">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="h3 fw-bold m-0 text-dark">Staff Management</h4>
                    <p class="text-muted mb-0 mt-2">Manage your team members and their roles</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light border" id="refreshBtn" data-bs-toggle="tooltip" title="Refresh">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                    @if(Auth::user()->hasPermission('manage_staff') || Auth::user()->hasPermission('create_staff'))
                    <a href="{{ route('staff.create') }}" class="btn btn-primary shadow-sm px-4">
                        <i class="mdi mdi-plus-circle me-2"></i> Add New Staff
                    </a>
                    @endif
                </div>
            </div>

            {{-- ... --}}

            <div class="card border-0 shadow-sm">
                {{-- Card Body --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light border-bottom">
                                {{-- Headers (Unchanged) --}}
                                <tr>
                                    <th>#</th>
                                    <th>Staff Member</th>
                                    <th>Contact Info</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($staffMembers as $staff)
                                <tr class="border-bottom">
                                    {{-- Data Columns (Unchanged) --}}
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td>{{ $staff->name }}</td>
                                    <td>{{ $staff->email }}</td>
                                    <td>
                                        @foreach($staff->roles as $role)
                                        <span class="badge bg-info bg-opacity-10 text-info">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="form-check form-switch d-flex align-items-center mb-0">
                                            <input class="form-check-input status-toggle fs-5 me-2"
                                                type="checkbox"
                                                role="switch"
                                                data-id="{{ $staff->id }}"
                                                {{ $staff->is_active ? 'checked' : '' }}
                                                {{ !Auth::user()->hasPermission('manage_staff') ? 'disabled' : '' }}>
                                            {{-- Status Labels --}}
                                        </div>
                                    </td>

                                    <td class="text-end pe-4">
                                        @if(Auth::user()->hasPermission('manage_staff'))
                                        <div class="btn-group shadow-sm" role="group">
                                            <a href="{{ route('staff.edit', $staff->id) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit Staff">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $staff->id }}"
                                                title="Delete Staff">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                {{-- Empty State --}}
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Scripts (Unchanged) --}}
</x-app-layout>