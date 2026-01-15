<x-app-layout title="Create Permission | Store Panel">
    <div class="content">
        <div class="container-fluid">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1"><h4 class="fs-18 fw-semibold m-0">Create Permission</h4></div>
                <div class="text-end">
                    <a href="{{ route('permissions.index') }}" class="btn btn-light border">Back</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('permissions.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Permission Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. view orders" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Group Name</label>
                                    <input type="text" name="group_name" class="form-control" placeholder="e.g. Order Management">
                                </div>
                                <button type="submit" class="btn btn-primary">Save Permission</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>