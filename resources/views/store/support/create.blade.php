<x-app-layout title="Raise Ticket">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Create Support Ticket</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('store.support.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select" required>
                                        <option value="Stock Issue">Stock Issue</option>
                                        <option value="Technical">Technical/POS</option>
                                        <option value="Logistics">Logistics/Delivery</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select" required>
                                        <option value="low">Low (General Query)</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High (Urgent)</option>
                                        <option value="critical">Critical (System Down)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>