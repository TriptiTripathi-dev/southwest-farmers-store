<x-website-layout title="Shop All Products - Southwest Farmers">
    
    @push('styles')
    <style>
        .page-header {
            background: linear-gradient(rgba(0, 154, 54, 0.05), rgba(0, 154, 54, 0.1));
            padding: 80px 0;
            margin-bottom: 60px;
        }

        .product-card-enhanced {
            border-radius: 2rem;
            border: 1px solid #f1f5f9;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            background: white;
        }

        .product-card-enhanced:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.1);
        }

        .product-image-container {
            aspect-ratio: 1/1;
            padding: 2rem;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .cart-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 154, 54, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.4s ease;
            transform: translateY(20px);
        }

        .product-card-enhanced:hover .cart-overlay {
            opacity: 1;
            transform: translateY(0);
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--theme-dark, #004d1b), var(--theme-primary, #009A36));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Pagination Styling */
        .pagination {
            justify-content: center;
            gap: 10px;
        }
        .page-item .page-link {
            border-radius: 50% !important;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--theme-text);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .page-item.active .page-link {
            background-color: var(--theme-primary);
            border-color: var(--theme-primary);
        }
        .page-item .page-link:hover {
            background-color: var(--theme-light);
            border-color: var(--theme-primary);
        }
    </style>
    @endpush

    <!-- Header Section -->
    <section class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="badge bg-theme-light text-theme rounded-pill px-4 py-2 mb-3 fw-bold">
                        🛒 OUR CATALOGUE
                    </span>
                    <h1 class="display-3 fw-black mb-3 text-gradient">Fresh Products</h1>
                    <p class="lead text-muted mb-0">Browse our hand-picked selection of fresh produce and premium pantry staples.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid -->
    <section class="pb-5 mb-5">
        <div class="container">
            <div class="row g-4">
                @forelse($products as $index => $product)
                    <div class="col-6 col-md-3 col-lg-2 reveal active" style="transition-delay: {{ ($index % 6) * 0.1 }}s">
                        <div class="product-card-enhanced h-100">
                            <div class="product-image-container">
                                <img src="{{ $product->image ? Storage::disk('r2')->url($product->image) : 'https://placehold.co/400x400/e6ffef/009A36?text=Fresh+Product' }}" 
                                     class="img-fluid" alt="{{ $product->product_name }}">
                                <div class="cart-overlay">
                                    <form action="{{ route('website.cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-light rounded-pill px-4 fw-bold">
                                            <i class="mdi mdi-cart-plus me-2"></i>Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-3 text-center">
                                <span class="badge bg-theme-light text-theme rounded-pill px-2 py-1 small mb-2" style="font-size: 0.7rem;">
                                    {{ $product->category->name ?? 'Fresh Produce' }}
                                </span>
                                @if(isset($currentStore))
                                <div class="fw-bold text-muted mb-2" style="font-size: 0.75rem;">
                                    <i class="mdi mdi-storefront me-1 text-theme"></i> {{ $currentStore->name }}
                                </div>
                                @elseif($product->storeStocks->isNotEmpty())
                                <div class="fw-bold text-muted mb-2" style="font-size: 0.75rem;">
                                    <i class="mdi mdi-storefront me-1 text-theme"></i> {{ $product->storeStocks->first()->store->name ?? 'Home Food' }}
                                </div>
                                @endif
                                <h6 class="fw-bold text-dark mb-2 text-truncate" style="font-size: 0.9rem;">{{ $product->product_name ?? $product->name }}</h6>
                                <p class="fw-black text-theme mb-0" style="font-size: 1.1rem;">${{ number_format($product->price, 2) }}</p>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-4 shadow-sm inline-block">
                            <i class="mdi mdi-package-variant-closed fs-1 text-muted"></i>
                            <h4 class="mt-4 fw-bold text-dark">No products found</h4>
                            <p class="text-muted">Try adjusting your filters or check back later.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-5 pt-4">
                {{ $products->links() }}
            </div>
        </div>
    </section>

</x-website-layout>
