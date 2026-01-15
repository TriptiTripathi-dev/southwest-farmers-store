<x-app-layout title="Create Store">

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ route('Store.store') }}" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Store Name <span class="text-danger">*</span></label>
                        <input type="text" name="Store_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>

                    <button class="btn btn-success">Save Warehouse</button>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>
