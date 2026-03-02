<x-website-layout :title="$product->product_name">
    @push('styles')
    <style>
        .product-gallery {
            background: #f8fafc;
            border-radius: 1.5rem;
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            border: 1px solid #e2e8f0;
        }
        .main-img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .main-img:hover {
            transform: scale(1.05);
        }
        .product-badge {
            background: var(--theme-light);
            color: var(--pos-primary);
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }
        .price-tag {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--pos-text);
            margin: 1.5rem 0;
        }
        .qty-input-group {
            background: #f1f5f9;
            border-radius: 0.75rem;
            padding: 0.5rem;
            display: inline-flex;
            align-items: center;
            gap: 1rem;
        }
        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            border: none;
            background: white;
            color: var(--pos-text);
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .qty-btn:hover {
            background: var(--pos-primary);
            color: white;
        }
        .quantity-display {
            font-weight: 700;
            min-width: 30px;
            text-align: center;
        }
        .btn-buy-now {
            background: var(--pos-primary);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 1rem;
            font-weight: 800;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }
        .btn-buy-now:hover {
            background: var(--pos-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4);
        }
    </style>
    @endpush

    <div class="py-5">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('website.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('website.products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
                </ol>
            </nav>

            <div class="row g-5">
                <!-- Product Image -->
                <div class="col-lg-6">
                    <div class="product-gallery">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/600x600/ecfdf5/10b981?text=' . urlencode($product->product_name) }}" 
                             class="main-img" alt="{{ $product->product_name }}">
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="ps-lg-4">
                        <span class="product-badge">{{ $product->category->name ?? 'Premium Item' }}</span>
                        <h1 class="display-5 fw-black text-dark mb-3">{{ $product->product_name }}</h1>
                        
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="text-warning">
                                <i class="mdi mdi-star"></i>
                                <i class="mdi mdi-star"></i>
                                <i class="mdi mdi-star"></i>
                                <i class="mdi mdi-star"></i>
                                <i class="mdi mdi-star-outline"></i>
                            </div>
                            <span class="text-muted">(48 reviews)</span>
                        </div>

                        <div class="price-tag">
                            ${{ number_format($product->price, 2) }}
                        </div>

                        <p class="text-muted fs-5 mb-5">
                            {{ $product->description ?: 'This premium product is crafted with excellence, ensuring the highest quality standards for our valued customers. Experience the difference today.' }}
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-4">
                            <div class="qty-input-group">
                                <button class="qty-btn" onclick="updateQty(-1)">-</button>
                                <span class="quantity-display" id="productQty">1</span>
                                <button class="qty-btn" onclick="updateQty(1)">+</button>
                            </div>
                            
                            <button class="btn-buy-now w-100 w-md-auto" onclick="submitToCart()">
                                <i class="mdi mdi-cart-plus me-2"></i> ADD TO CART
                            </button>
                        </div>

                        <hr class="my-5">

                        <div class="row g-4">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light p-3 rounded-circle text-theme">
                                        <i class="mdi mdi-truck-delivery fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Fast Delivery</h6>
                                        <small class="text-muted">Within 24-48 hours</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light p-3 rounded-circle text-theme">
                                        <i class="mdi mdi-shield-check fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Secure Pay</h6>
                                        <small class="text-muted">100% Secure Transaction</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let quantity = 1;

        function updateQty(delta) {
            quantity = Math.max(1, quantity + delta);
            document.getElementById('productQty').textContent = quantity;
        }

        function submitToCart() {
            @if(!auth('customer')->check())
                window.location.href = '{{ route('website.login') }}';
                return;
            @endif

            const btn = document.querySelector('.btn-buy-now');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ADDING...';

            const formData = new FormData();
            formData.append('product_id', '{{ $product->id }}');
            formData.append('quantity', quantity);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('website.cart.store') }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.classList.replace('btn-buy-now', 'btn-success');
                    btn.innerHTML = '<i class="mdi mdi-check me-2"></i> ADDED TO CART';
                    
                    // Update global cart count
                    const badge = document.querySelector('.navbar .badge');
                    if (badge) badge.textContent = data.cart_count;

                    setTimeout(() => {
                        btn.classList.replace('btn-success', 'btn-buy-now');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 2000);
                }
            })
            .catch(err => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Failed to add product');
            });
        }
    </script>
    @endpush
</x-website-layout>
