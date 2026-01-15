<x-app-layout title="Edit Role">
    <div class="content">
        <div class="container-fluid">
            <div class="py-3">
               <div class="flex-grow-1"><h4 class="fs-18 fw-semibold m-0">Edit Role</h4></div>
                <div class="text-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-light border">Back</a>
                </div>
            </div>

            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        
                        {{-- Role Name --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                        </div>

                        <h5 class="mb-3">Update Permissions</h5>
                        <hr>

                        @foreach ($permissions as $group => $groupPermissions)
                            @php
                                // Check if all permissions in this group are already assigned
                                $groupId = Str::slug($group);
                                $allChecked = true;
                                foreach($groupPermissions as $perm) {
                                    if(!in_array($perm->id, $assignedPermissions)) {
                                        $allChecked = false;
                                        break;
                                    }
                                }
                            @endphp

                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input group-checkbox" 
                                               type="checkbox" 
                                               id="group-{{ $groupId }}"
                                               data-group="group-{{ $groupId }}"
                                               {{ $allChecked ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-uppercase text-primary" for="group-{{ $groupId }}">
                                            {{ ucfirst($group ?? 'General') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        @foreach ($groupPermissions as $permission)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox group-{{ $groupId }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}" 
                                                           id="perm-{{ $permission->id }}"
                                                           {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">Update Role</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle "Select All" Group Checkbox
            const groupCheckboxes = document.querySelectorAll('.group-checkbox');

            groupCheckboxes.forEach(groupCheckbox => {
                groupCheckbox.addEventListener('change', function () {
                    const groupClass = this.getAttribute('data-group');
                    const permissions = document.querySelectorAll('.' + groupClass);
                    
                    permissions.forEach(permission => {
                        permission.checked = this.checked;
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>