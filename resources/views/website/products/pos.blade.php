<x-website-layout title="Quick Shop POS">
    @push('styles')
    <style>
        :root {
            --pos-primary: #10b981;
            --pos-primary-dark: #065f46;
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
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
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
                        <h5 class="fw-black mb-4">Categories</h5>
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
                <div class="col-lg-9">
                    <!-- Top Bar -->
                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-4">
                        <div class="w-100 flex-grow-1" style="max-width: 500px;">
                            <div class="search-box">
                                <i class="mdi mdi-magnify fs-4 text-muted"></i>
                                <input type="text" id="posSearch" class="search-input" placeholder="Search products by name or SKU...">
                            </div>
                        </div>
                        <div class="d-lg-none w-100">
                            <select class="form-select rounded-pill px-4" onchange="filterCategory(this.value)">
                                <option value="all">All Categories</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->code }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="row g-3 g-md-4" id="productGrid">
                        <!-- Content loaded via AJAX -->
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
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-theme" role="status"></div>
                </div>
            `;

            fetch(`{{ route('website.products.pos') }}?category=${currentCategory}&term=${searchTerm}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(products => {
                if (products.length === 0) {
                    grid.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="mdi mdi-package-variant-closed fs-1 text-muted"></i>
                            <h4 class="mt-3 fw-bold">No products found</h4>
                            <p class="text-muted">Try adjusting your search or category filter.</p>
                        </div>
                    `;
                    return;
                }

                grid.innerHTML = products.map(p => `
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="pos-card shadow-sm">
                            <div class="pos-img-container">
                                <img src="${p.image ? '/storage/' + p.image : 'https://placehold.co/400x400/ecfdf5/10b981?text=' + encodeURIComponent(p.product_name)}" 
                                     class="pos-img" alt="${p.product_name}">
                            </div>
                            <div class="pos-content">
                                <h3 class="pos-title">${p.product_name}</h3>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="pos-price">$${parseFloat(p.price).toFixed(2)}</span>
                                    <small class="text-muted fw-bold">SKU: ${p.sku || 'N/A'}</small>
                                </div>
                                <button type="button" class="btn-add-cart" onclick="addToCart(${p.id}, this)">
                                    <i class="mdi mdi-cart-plus"></i> ADD
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            })
            .catch(err => {
                grid.innerHTML = `<div class="col-12 text-center py-5 text-danger">Error loading products. Please try again.</div>`;
            });
        }

        function addToCart(productId, btn) {
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', 1);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('website.cart.store') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Show success state on button
                    btn.classList.replace('btn-add-cart', 'btn-success');
                    btn.innerHTML = '<i class="mdi mdi-check"></i> ADDED';
                    
                    // Update navbar cart count if possible
                    const cartBadge = document.querySelector('.navbar .badge');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                        cartBadge.classList.replace('bg-success', 'bg-danger'); // Optional animation/color change
                    }

                    setTimeout(() => {
                        btn.classList.replace('btn-success', 'btn-add-cart');
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    }, 2000);
                }
            })
            .catch(err => {
                console.error('Cart Error:', err);
                btn.innerHTML = originalContent;
                btn.disabled = false;
            });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>
    @endpush
</x-website-layout>
