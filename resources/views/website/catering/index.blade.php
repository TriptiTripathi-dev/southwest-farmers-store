<x-website-layout title="Catering - Southwest Farmers">
    @push('styles')
    <style>
        .catering-header { background: linear-gradient(rgba(127, 5, 27, 0.05), rgba(127, 5, 27, 0.1)); padding: 60px 0; margin-bottom: 40px; }
        .cat-item { border: 1px solid #f1f5f9; border-radius: 1rem; background: #fff; }
        .cat-item input[type=number] { width: 90px; }
        .summary-card { position: sticky; top: 100px; border-radius: 1.25rem; }
    </style>
    @endpush

    <section class="catering-header text-center">
        <div class="container">
            <h1 class="fw-black text-dark mb-2">Catering</h1>
            <p class="text-muted mb-0">Order for your event{{ $store ? ' from ' . $store->store_name : '' }}. Please order at least <strong>{{ $options['notice_days'] }} days</strong> ahead.</p>
        </div>
    </section>

    <div class="container pb-5">
        @if (!$store || !$options['enabled'])
            <div class="alert alert-warning text-center">Catering orders are not being accepted at this store right now.</div>
        @else
        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Rules, shown before anything is ordered (spec 8.1) --}}
                <div class="alert alert-light border small mb-4">
                    <strong>Notice:</strong> {{ $options['notice_days'] }}+ days before your event = standard prices.
                    Orders placed after {{ \Carbon\Carbon::createFromFormat('H:i', $options['cutoff_time'])->format('g:i A') }} count from the next day.
                    @if ($tiers->isNotEmpty())
                        <ul class="mb-0 mt-1">
                            @foreach ($tiers as $t)
                                <li>{{ $t->label() }}</li>
                            @endforeach
                        </ul>
                    @else
                        Orders with less notice can't be accepted.
                    @endif
                </div>

                @forelse ($categories as $cat)
                    @continue(!$items->has($cat->id))
                    <h5 class="fw-bold mt-4 mb-3">{{ $cat->name }}</h5>
                    @foreach ($items[$cat->id] as $item)
                        <div class="cat-item p-3 mb-2 d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <div class="fw-semibold">{{ $item->name }}
                                    @if ($item->is_catering_only)<span class="badge bg-warning text-dark ms-1">Catering only</span>@endif
                                </div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($item->description, 90) }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold text-danger">${{ number_format($item->price, 2) }}</span>
                                <input type="number" min="0" step="1" value="0" class="form-control form-control-sm qty" data-id="{{ $item->id }}" aria-label="Quantity of {{ $item->name }}">
                            </div>
                        </div>
                    @endforeach
                @empty
                    <p class="text-muted">No menu items are available for catering yet.</p>
                @endforelse
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 summary-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Your catering order</h5>
                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <label class="form-label small fw-semibold">Event date</label>
                                <input type="date" id="dueDate" class="form-control" value="{{ $earliest }}" min="{{ now(\App\Services\CateringService::tz())->format('Y-m-d') }}">
                            </div>
                            <div class="col-5">
                                <label class="form-label small fw-semibold">Time</label>
                                <input type="time" id="dueTime" class="form-control" value="12:00">
                            </div>
                        </div>

                        <div id="quoteBox" class="small mb-3">
                            <div class="text-muted">Add items to see your total.</div>
                        </div>

                        <hr>
                        <div class="mb-2"><input type="text" id="contactName" class="form-control form-control-sm" placeholder="Contact name" value="{{ auth('customer')->user()->name ?? '' }}"></div>
                        <div class="mb-2"><input type="tel" id="contactPhone" class="form-control form-control-sm" placeholder="Contact phone" value="{{ auth('customer')->user()->phone ?? '' }}"></div>
                        <div class="mb-2"><input type="number" id="guestCount" min="1" class="form-control form-control-sm" placeholder="Number of guests (optional)"></div>
                        <div class="mb-2 d-flex gap-3 small">
                            <div class="form-check"><input class="form-check-input" type="radio" name="orderType" id="otPickup" value="Pickup" checked><label class="form-check-label" for="otPickup">Pickup</label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="orderType" id="otDelivery" value="Delivery"><label class="form-check-label" for="otDelivery">Delivery</label></div>
                        </div>
                        <div class="mb-3"><textarea id="notes" rows="2" class="form-control form-control-sm" placeholder="Notes (allergies, delivery address, setup...)"></textarea></div>
                        <div class="mb-3 d-flex gap-3 small">
                            <div class="form-check"><input class="form-check-input" type="radio" name="payMethod" id="payCard" value="card" checked><label class="form-check-label" for="payCard">Pay by card</label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="payMethod" id="payCod" value="cod"><label class="form-check-label" for="payCod">Pay on pickup / delivery</label></div>
                        </div>

                        <button type="button" id="placeOrder" class="btn btn-danger w-100 rounded-pill fw-bold" disabled>Place catering order</button>
                        @guest('customer')
                            <small class="text-muted d-block mt-2 text-center">You'll be asked to log in before ordering.</small>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if ($store && $options['enabled'])
    @push('scripts')
    <script>
        (function () {
            const csrf = '{{ csrf_token() }}';
            const loggedIn = {{ auth('customer')->check() ? 'true' : 'false' }};
            const box = document.getElementById('quoteBox');
            const btn = document.getElementById('placeOrder');
            const money = n => '$' + Number(n || 0).toFixed(2);
            let lastQuote = null, timer = null;

            function payload() {
                const items = {};
                document.querySelectorAll('.qty').forEach(i => { const q = parseFloat(i.value); if (q > 0) items[i.dataset.id] = q; });
                return { items, due_date: document.getElementById('dueDate').value, due_time: document.getElementById('dueTime').value };
            }

            function render(q) {
                lastQuote = q;
                if (!q.lines || !q.lines.length) {
                    box.innerHTML = '<div class="text-muted">Add items to see your total.</div>';
                    btn.disabled = true;
                    return;
                }
                let html = q.lines.map(l => `<div class="d-flex justify-content-between"><span>${l.quantity} × ${l.name}</span><span>${money(l.total)}</span></div>`).join('');
                html += `<hr class="my-2"><div class="d-flex justify-content-between"><span>Subtotal</span><span>${money(q.subtotal)}</span></div>`;
                if (q.ok && q.surcharge > 0) {
                    html += `<div class="d-flex justify-content-between text-danger"><span>Urgency surcharge (${q.percent}%, ${q.notice_days} days' notice)</span><span>${money(q.surcharge)}</span></div>`;
                }
                html += `<div class="d-flex justify-content-between fw-bold fs-5 mt-1"><span>Total</span><span>${money(q.total)}</span></div>`;
                if (!q.ok) {
                    html += `<div class="alert alert-danger py-2 mt-2 mb-0">${q.message}</div>`;
                } else if (q.surcharge > 0) {
                    html += `<div class="alert alert-warning py-2 mt-2 mb-0">Less than ${q.standard_days} days' notice: a ${q.percent}% urgency surcharge applies.</div>`;
                }
                box.innerHTML = html;
                btn.disabled = !q.ok;
            }

            function refresh() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fetch("{{ route('website.catering.quote') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: JSON.stringify(payload())
                    }).then(r => r.json()).then(render).catch(() => { box.innerHTML = '<div class="text-danger">Could not check the price. Try again.</div>'; btn.disabled = true; });
                }, 300);
            }

            document.querySelectorAll('.qty, #dueDate, #dueTime').forEach(el => el.addEventListener('input', refresh));
            refresh();

            btn.addEventListener('click', () => {
                if (!loggedIn) { window.location.href = "{{ route('website.login') }}"; return; }
                const body = Object.assign(payload(), {
                    contact_name: document.getElementById('contactName').value,
                    contact_phone: document.getElementById('contactPhone').value,
                    guest_count: document.getElementById('guestCount').value || null,
                    order_type: document.querySelector('input[name=orderType]:checked').value,
                    notes: document.getElementById('notes').value,
                    payment_method: document.querySelector('input[name=payMethod]:checked').value,
                });
                btn.disabled = true; btn.textContent = 'Placing order...';
                fetch("{{ route('website.catering.store') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                }).then(r => r.json().then(d => ({ ok: r.ok, d }))).then(({ ok, d }) => {
                    if (ok && d.success) { window.location.href = d.redirect; return; }
                    const msg = d.message || (d.errors ? Object.values(d.errors)[0][0] : 'The order could not be placed.');
                    Swal.fire({ icon: 'error', title: 'Not placed', text: msg });
                    btn.disabled = false; btn.textContent = 'Place catering order';
                }).catch(() => { Swal.fire({ icon: 'error', title: 'Error', text: 'Could not reach the server.' }); btn.disabled = false; btn.textContent = 'Place catering order'; });
            });
        })();
    </script>
    @endpush
    @endif
</x-website-layout>
