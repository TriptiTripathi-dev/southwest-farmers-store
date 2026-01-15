<x-app-layout title="Create Role">
    <div class="content">
        <div class="container-fluid">
            <div class="py-3">
              <div class="flex-grow-1"><h4 class="fs-18 fw-semibold m-0">Create Role</h4></div>
                <div class="text-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-light border">Back</a>
                </div>
            </div>

            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        
                        {{-- Role Name Input --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Shift Manager" required>
                        </div>

                        <h5 class="mb-3">Assign Permissions</h5>
                        <hr>

                        {{-- Loop through Grouped Permissions --}}
                        @foreach ($permissions as $group => $groupPermissions)
                            <div class="row mb-4 border-bottom pb-3">
                                <div class="col-md-3">
                                    {{-- Group Header with Select All Checkbox --}}
                                    <div class="form-check">
                                        <input class="form-check-input group-checkbox" 
                                               type="checkbox" 
                                               id="group-{{ Str::slug($group) }}"
                                               data-group="group-{{ Str::slug($group) }}">
                                        <label class="form-check-label fw-bold text-uppercase text-primary" for="group-{{ Str::slug($group) }}">
                                            {{ ucfirst($group ?? 'General') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        @foreach ($groupPermissions as $permission)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    {{-- Individual Permission Checkbox --}}
                                                    <input class="form-check-input permission-checkbox group-{{ Str::slug($group) }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}" 
                                                           id="perm-{{ $permission->id }}">
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
                            <button type="submit" class="btn btn-primary px-4">Create Role</button>
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

            // Optional: If all permissions in a group are checked manually, check the group box
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            permissionCheckboxes.forEach(permCheckbox => {
                permCheckbox.addEventListener('change', function() {
                    // Find which group this belongs to
                    // This part is a bit tricky since we use classes, but for simple "Check All" logic, 
                    // the top part is sufficient. If you want full sync, let me know.
                });
            });
        });
    </script>
    @endpush
</x-app-layout>