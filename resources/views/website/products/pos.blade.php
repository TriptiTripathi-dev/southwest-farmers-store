<x-website-layout title="{{ $posSettings->title ?? 'Products' }}">
    @push('styles')
    <style>
        :root {
            --theme-primary: #019934;
            --theme-dark: #004d1a;
            --theme-hover: #01802b;
            --theme-light: #e6fff0;
            --pos-primary: #019934;
            --pos-primary-dark: #007a29;
            --pos-bg: #f8fafc;
            --pos-card-bg: #ffffff;
            --pos-text: #1e293b;
            --pos-muted: #64748b;
            --pos-border: #e2e8f0;
        }

        .pos-container {
            min-height: calc(100vh - 80px);
            background-color: var(--pos-bg);
            padding: 20px 0;
        }

        .category-sidebar {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 100px;
        }

        .cat-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid transparent;
            background: transparent;
            color: var(--pos-text);
            font-weight: 600;
            transition: all 0.2s;
        }

        .cat-item:hover {
            background: var(--theme-light);
            color: var(--pos-primary);
        }

        .cat-item.active {
            background: var(--pos-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 154, 54, 0.2);
        }

        .pos-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid var(--pos-border);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .pos-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--pos-primary);
        }

        .pos-img-container {
            aspect-ratio: 1/1;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .pos-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .pos-content {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .pos-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--pos-text);
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.5rem;
        }

        .pos-price {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--pos-primary);
            margin-top: auto;
        }

        .btn-add-cart {
            width: 100%;
            background: var(--pos-primary);
            color: white;
            border: none;
            padding: 0.6rem;
            border-radius: 0.5rem;
            font-weight: 700;
            margin-top: 1rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-add-cart:hover {
            background: var(--pos-primary-dark);
            transform: scale(1.02);
        }

        .search-box {
            background: white;
            border-radius: 100px;
            padding: 0.5rem 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--pos-border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .search-input {
            border: none;
            width: 100%;
            font-weight: 500;
        }

        .search-input:focus {
            outline: none;
        }

        #productGrid {
            min-height: 400px;
        }

        .skeleton {
            background: #e2e8f0;
            border-radius: 0.5rem;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
    @endpush

    <div class="pos-container">
        <div class="container">
            <div class="row g-4">
                <!-- Sidebar -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="category-sidebar">
                        <h5 class="fw-black mb-4">{{ $posSettings->title ?? 'Products' }}</h5>
                        <p class="text-muted small mb-4">{{ $posSettings->subtitle ?? 'Quickly browse and add products to your cart.' }}</p>
                        <hr class="mb-4">
                        <h6 class="fw-bold mb-3">Categories</h6>
                        <button class="cat-item active" onclick="filterCategory('all', this)">
                            <i class="mdi mdi-apps me-2"></i> All Products
                        </button>
                        @foreach($categories as $cat)
                        <button class="cat-item" onclick="filterCategory('{{ $cat->code }}', this)">
                            <i class="mdi mdi-chevron-right me-2"></i> {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-6">
                    <!-- Top Bar -->
                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-4">
                        <div class="w-100 flex-grow-1">
                            <div class="search-box">
                                <i class="mdi mdi-magnify fs-4 text-muted"></i>
                                <input type="text" id="posSearch" class="search-input" placeholder="Search products...">
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="row g-3" id="productGrid">
                        <!-- Content loaded via AJAX -->
                    </div>
                </div>

                <!-- Side Cart -->
                <div class="col-lg-3">
                    <div class="category-sidebar p-0 overflow-hidden" style="position: sticky; top: 100px;">
                        <div class="p-3 bg-dark text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Current Order</h6>
                            <i class="mdi mdi-cart-outline"></i>
                        </div>
                        <div id="sideCartItems" style="max-height: 400px; overflow-y: auto;" class="p-3">
                            <!-- Items loaded via AJAX -->
                            <div class="text-center py-4 text-muted">
                                <p>No items added yet</p>
                            </div>
                        </div>
                        <div class="p-3 bg-light border-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Amount</span>
                                <span class="fw-bold" id="sideCartTotal">$0.00</span>
                            </div>
                            <button class="btn btn-theme w-100 py-3 fw-bold rounded-pill mt-2" id="checkoutBtn" disabled onclick="window.location.href='{{ route('website.cart.index') }}'">
                                CHECKOUT NOW
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentCategory = 'all';
        let searchTerm = '';

        function filterCategory(slug, btn = null) {
            currentCategory = slug;
            if (btn) {
                document.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
                btn.classList.add('active');
            }
            loadProducts();
        }

        document.getElementById('posSearch').addEventListener('input', function(e) {
            searchTerm = e.target.value;
            loadProducts();
        });

        function loadProducts() {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-theme"></div></div>';

            fetch(`{{ route('website.products.pos') }}?category=${currentCategory}&term=${searchTerm}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(products => {
                if (products.length === 0) {
                    grid.innerHTML = '<div class="col-12 text-center py-5">No products found</div>';
                    return;
                }

                grid.innerHTML = products.map(p => `
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="pos-card shadow-sm h-100 d-flex flex-column">
                            <div class="pos-img-container position-relative overflow-hidden" style="height: 140px; padding: 0.5rem;">
                                <img src="${p.image ? '/storage/' + p.image : 'https://placehold.co/200x200/e6ffef/009A36?text=' + encodeURIComponent(p.product_name)}" 
                                     class="pos-img w-100 h-100 object-fit-contain" alt="${p.product_name}">
                                ${p.stock <= 5 && p.stock !== null ? '<span class="badge bg-danger position-absolute top-0 end-0 m-2 shadow-sm rounded-pill">Low Stock</span>' : ''}
                            </div>
                            <div class="pos-content p-3 d-flex flex-column flex-grow-1 border-top border-light">
                                <h3 class="fw-bold text-dark mb-1" style="font-size: 0.85rem; line-height: 1.25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5rem;">${p.product_name}</h3>
                                <p class="text-muted small mb-3" style="font-size: 0.75rem;">${p.barcode ? '<i class="mdi mdi-barcode me-1"></i>'+p.barcode : '&nbsp;'}</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center bg-light rounded-pill p-1 ps-3">
                                    <span class="fw-black text-theme" style="font-size: 1rem;">$${parseFloat(p.price).toFixed(2)}</span>
                                    <button type="button" class="btn btn-theme rounded-circle shadow-sm d-flex align-items-center justify-content-center hover-lift" style="width: 32px; height: 32px; padding: 0;" onclick="addToCart(${p.id}, this)">
                                        <i class="mdi mdi-cart-plus fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            });
        }

        function addToCart(productId, btn) {
            @if(!auth('customer')->check())
                window.location.href = '{{ route('website.login') }}';
                return;
            @endif

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('website.cart.store') }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadSideCart();
                    // Update global badge
                    const badge = document.querySelector('.navbar .badge');
                    if (badge) badge.textContent = data.cart_count;
                }
            });
        }

        function loadSideCart() {
            @if(auth('customer')->check())
                updateSideCartUI();
            @endif
        }

        function updateSideCartUI() {
            @if(!auth('customer')->check())
                // Not logged in — show login prompt in sidebar
                const sideItems = document.getElementById('sideCartItems');
                sideItems.innerHTML = '<div class="text-center py-4 text-muted"><i class="mdi mdi-lock-outline fs-3 d-block mb-2"></i><small>Please <a href="{{ route('website.login') }}" class="text-theme fw-bold">login</a> to use your cart.</small></div>';
                document.getElementById('checkoutBtn').disabled = true;
                return;
            @endif

            // We'll fetch the cart data as JSON for the sidebar
            fetch('{{ route('website.cart.index') }}', {
                headers: { 
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' 
                }
            })
            .then(res => {
                if (!res.ok) {
                    // Non-2xx (e.g. 401) — show empty state and bail
                    const sideItems = document.getElementById('sideCartItems');
                    sideItems.innerHTML = '<div class="text-center py-4 text-muted">No items added yet</div>';
                    document.getElementById('sideCartTotal').textContent = '$0.00';
                    document.getElementById('checkoutBtn').disabled = true;
                    return null;
                }
                return res.json();
            })
            .then(data => {
                if (!data) return;

                const sideItems = document.getElementById('sideCartItems');
                const sideTotal = document.getElementById('sideCartTotal');
                const checkoutBtn = document.getElementById('checkoutBtn');

                if (!data.cart || data.cart.items.length === 0) {
                    sideItems.innerHTML = '<div class="text-center py-4 text-muted">No items added yet</div>';
                    sideTotal.textContent = '$0.00';
                    checkoutBtn.disabled = true;
                    return;
                }

                sideItems.innerHTML = data.cart.items.map(item => `
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                        <div class="pe-3">
                            <small class="fw-bold d-block text-dark lh-sm mb-1">${item.product.product_name}</small>
                            <small class="text-muted d-block">${item.quantity} x <span class="text-theme fw-semibold">$${parseFloat(item.price).toFixed(2)}</span></small>
                        </div>
                        <span class="fw-bold text-dark bg-light px-2 py-1 rounded-pill" style="font-size: 0.9rem;">$${parseFloat(item.total).toFixed(2)}</span>
                    </div>
                `).join('');

                sideTotal.textContent = `$${parseFloat(data.cart.total_amount).toFixed(2)}`;
                checkoutBtn.disabled = false;
            })
            .catch(() => {
                // Network error — silently show empty state
                document.getElementById('sideCartItems').innerHTML = '<div class="text-center py-4 text-muted">No items added yet</div>';
                document.getElementById('sideCartTotal').textContent = '$0.00';
                document.getElementById('checkoutBtn').disabled = true;
            });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            @if(auth('customer')->check())
                updateSideCartUI();
            @endif
        });
    </script>
    @endpush
</x-website-layout>
