<x-app-layout title="New Recipe">
    <div class="content-wrapper">
        <div class="container-fluid px-3 px-md-4 py-4">

            <div class="mb-4">
                <nav class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="page-breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('store.cookbook.index') }}" class="page-breadcrumb-link">Cookbook Builder</a></li>
                        <li class="breadcrumb-item text-muted">New Recipe</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-dark">New Recipe</h4>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('store.cookbook.store') }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Menu Item <span class="text-danger">*</span></label>
                                <select name="menu_item_id" class="form-select" required>
                                    <option value="">Select menu item</option>
                                    @foreach($menuItems as $item)
                                        <option value="{{ $item->id }}" @selected(old('menu_item_id') == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Recipe Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Prep Time (min)</label>
                                <input type="number" min="0" name="prep_time_minutes" class="form-control" value="{{ old('prep_time_minutes') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Cook Time (min)</label>
                                <input type="number" min="0" name="cook_time_minutes" class="form-control" value="{{ old('cook_time_minutes') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Yield Quantity</label>
                                <input type="number" min="0" step="0.01" name="yield_quantity" class="form-control" value="{{ old('yield_quantity') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Yield Unit</label>
                                <input type="text" name="yield_unit" class="form-control" placeholder="e.g. servings" value="{{ old('yield_unit') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Steps</h6>
                            <button type="button" id="add-step" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-plus"></i> Add Step
                            </button>
                        </div>
                        <div id="steps-container"></div>
                        <div id="no-steps-msg" class="text-muted small">No steps added yet.</div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold px-4">Save Recipe</button>
                    <a href="{{ route('store.cookbook.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>

        </div>
    </div>

    <template id="step-row-template">
        <div class="step-row d-flex align-items-start gap-2 mb-2">
            <div class="step-number fw-bold text-muted pt-2" style="width: 28px;"></div>
            <div class="flex-grow-1">
                <textarea class="form-control step-instruction" rows="1" placeholder="Instruction" required></textarea>
            </div>
            <div style="width: 140px;">
                <input type="number" min="0" class="form-control step-timer" placeholder="Timer (min)">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-step"><i class="mdi mdi-close"></i></button>
        </div>
    </template>

    <script>
        (function () {
            const container = document.getElementById('steps-container');
            const noStepsMsg = document.getElementById('no-steps-msg');
            const template = document.getElementById('step-row-template');
            const addBtn = document.getElementById('add-step');

            function renumber() {
                const rows = container.querySelectorAll('.step-row');
                rows.forEach((row, i) => {
                    row.querySelector('.step-number').textContent = (i + 1) + '.';
                    row.querySelector('.step-instruction').setAttribute('name', `steps[${i}][instruction]`);
                    row.querySelector('.step-timer').setAttribute('name', `steps[${i}][timer_minutes]`);
                });
                noStepsMsg.style.display = rows.length ? 'none' : 'block';
            }

            function addStep(instruction = '', timer = '') {
                const clone = template.content.cloneNode(true);
                clone.querySelector('.step-instruction').value = instruction;
                clone.querySelector('.step-timer').value = timer;
                clone.querySelector('.remove-step').addEventListener('click', function () {
                    this.closest('.step-row').remove();
                    renumber();
                });
                container.appendChild(clone);
                renumber();
            }

            addBtn.addEventListener('click', () => addStep());
            renumber();
        })();
    </script>
</x-app-layout>
