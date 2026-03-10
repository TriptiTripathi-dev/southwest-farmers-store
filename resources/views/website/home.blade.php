<x-website-layout title="Home - FreshStore">
    
    @push('styles')
    <style>
        /* Modern Design System Tokens */
        :root {
            --theme-primary: #019934;
            --theme-dark: #004d1a;
            --theme-light: #e6fff0;
            --theme-hover: #01802b;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        /* Reveal Animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hero Section Enhancements */
        .hero-section {
            background: radial-gradient(circle at 10% 20%, rgba(1, 153, 52, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(251, 191, 36, 0.05) 0%, transparent 40%);
            min-height: 85vh;
            display: flex;
            align-items: center;
        }

        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            opacity: 0.12;
            animation: moveBlobs 20s infinite alternate;
        }

        @keyframes moveBlobs {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.1); }
        }

        /* Glassmorphism Elements */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        /* Feature Cards Upgrade */
        .feature-card-new {
            background: white;
            border-radius: 2rem;
            padding: 3rem 2rem;
            border: 1px solid #f1f5f9;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
        }

        .feature-card-new:hover {
            transform: translateY(-15px);
            border-color: var(--theme-primary);
            box-shadow: 0 30px 60px -12px rgba(1, 153, 52, 0.12);
        }

        .feature-icon-new {
            width: 70px;
            height: 70px;
            background: var(--theme-light);
            color: var(--theme-primary);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            transition: all 0.5s ease;
        }

        .feature-card-new:hover .feature-icon-new {
            background: var(--theme-primary);
            color: white;
            transform: rotateY(180deg);
        }

        /* Product Cards Upgrade */
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
            background: rgba(1, 153, 52, 0.9);
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

        /* Typography */
        .text-gradient {
            background: linear-gradient(135deg, #019934 0%, #01802b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-premium {
            background: linear-gradient(135deg, #019934, #01802b);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 100px;
            font-weight: 700;
            box-shadow: 0 10px 20px -5px rgba(0, 154, 54, 0.4);
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(0, 154, 54, 0.5);
            color: white;
        }
    </style>
    @endpush

    <!-- Hero Section -->
    <section class="hero-section position-relative overflow-hidden">
        <div class="hero-blob" style="width: 600px; height: 600px; top: -200px; right: -100px; background: #009A36;"></div>
        <div class="hero-blob" style="width: 400px; height: 400px; bottom: -100px; left: -100px; background: #fbbf24; animation-delay: -5s;"></div>
        
        <div class="container py-5 position-relative z-1">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="reveal active">
                        <span class="badge bg-theme-light text-theme rounded-pill px-4 py-2 mb-4 fw-bold">
                            {{ $homeSettings?->hero_badge ?? '✨ THE MODERN GROCERY EXPERIENCE' }}
                        </span>
                        <h1 class="display-2 fw-black mb-4 lh-sm">
                            {!! $homeSettings?->hero_title ?? 'Freshness <br/> <span class="text-gradient">Redefined</span> for Your Home.' !!}
                        </h1>
                        <div class="lead text-muted mb-5 pe-lg-5">
                            {!! $homeSettings?->hero_subtitle ?? 'Experience the pinnacle of quality with our curated selection of organic produce and daily essentials, delivered with surgical precision to your doorstep.' !!}
                        </div>
                        <div class="d-flex flex-wrap gap-4">
                            <a href="{{ $homeSettings?->hero_button_url ?? route('website.products.index') }}" class="btn btn-premium btn-lg">
                                <i class="mdi mdi-shopping-outline me-2"></i>{{ $homeSettings?->hero_button_text ?? 'Browse Products' }}
                            </a>
                            <a href="#features" class="btn btn-link text-dark fw-bold text-decoration-none d-flex align-items-center">
                                Learn our Story <i class="mdi mdi-arrow-right ms-2 fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative reveal active delay-200">
                        <div class="glass-card position-absolute p-4 rounded-4 shadow-lg d-flex align-items-center gap-3" style="top: 10%; right: -5%; z-index: 10; width: 240px;">
                            <div class="bg-warning text-white rounded-circle p-2">
                                <i class="mdi mdi-star fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">4.9/5 Rating</h6>
                                <small class="text-muted">From 2k+ Customers</small>
                            </div>
                        </div>
                        
                        <div class="glass-card position-absolute p-4 rounded-4 shadow-lg d-flex align-items-center gap-3" style="bottom: 10%; left: -5%; z-index: 10; width: 240px;">
                            <div class="bg-theme text-white rounded-circle p-2">
                                <i class="mdi mdi-truck-delivery fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Fast Delivery</h6>
                                <small class="text-muted">Within 30 Mins</small>
                            </div>
                        </div>

                        <img src="{{ ($homeSettings?->hero_image) ? asset('storage/'.$homeSettings->hero_image) : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80' }}" 
                             alt="Fresh Produce" 
                             class="img-fluid rounded-[3rem] shadow-2xl floating-element"
                             style="border: 15px solid white;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5 reveal">
                <h2 class="display-5 fw-black text-dark mb-3">{{ $homeSettings?->features_title ?? 'Why Shop With Us?' }}</h2>
                <div class="text-muted mx-auto" style="max-width: 600px;">{!! $homeSettings?->features_subtitle ?? "We've optimized every step of the process to ensure you get the freshest items at the best prices." !!}</div>
            </div>
            
            <div class="row g-4">
                @for($i = 1; $i <= 3; $i++)
                <div class="col-md-4 reveal delay-{{ $i * 100 }}">
                    <div class="feature-card-new">
                        <div class="feature-icon-new">
                            <i class="mdi {{ $homeSettings?->{'feature_'.$i.'_icon'} ?? ($i==1 ? 'mdi-leaf-circle-outline' : ($i==2 ? 'mdi-shield-check-outline' : 'mdi-clock-fast')) }} fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ $homeSettings?->{'feature_'.$i.'_title'} ?? ($i==1 ? 'Eco-Friendly' : ($i==2 ? 'Quality First' : 'Always On Time')) }}</h4>
                        <div class="text-muted">{!! $homeSettings?->{'feature_'.$i.'_text'} ?? ($i==1 ? '100% plastic-free packaging options and locally sourced organic produce.' : ($i==2 ? 'Every single item undergoes a 5-point quality check before leaving our facility.' : 'Real-time tracking and precise delivery windows you can count on.')) !!}</div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- Best Sellers -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 reveal">
                <div>
                    <h2 class="display-5 fw-black text-dark mb-0">{{ $homeSettings?->trending_title ?? 'Trending Products' }}</h2>
                    <p class="text-muted mt-2">{{ $homeSettings?->trending_subtitle ?? "The local community's favorites this week." }}</p>
                </div>
                <a href="{{ route('website.products.index') }}" class="btn btn-outline-theme rounded-pill px-4">
                    View Complete Catalog <i class="mdi mdi-chevron-right"></i>
                </a>
            </div>

            <div class="row g-4">
                @forelse($featuredProducts ?? [] as $index => $product)
                    <div class="col-6 col-md-4 col-lg-3 reveal" style="transition-delay: {{ ($index % 4) * 0.1 }}s">
                        <div class="product-card-enhanced h-100">
                            <div class="product-image-container">
                                <img src="{{ $product->image ? Storage::url($product->image) : 'https://placehold.co/400x400/e6ffef/009A36?text=Fresh+Product' }}" 
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
                            <div class="card-body p-4 text-center">
                                <span class="badge bg-theme-light text-theme rounded-pill p-2 px-3 small mb-2">
                                    {{ $product->category->name ?? 'Fresh Produce' }}
                                </span>
                                <h5 class="fw-bold text-dark mb-3 text-truncate">{{ $product->product_name }}</h5>
                                <p class="h4 fw-black text-theme">${{ number_format($product->price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-4 shadow-sm inline-block">
                            <i class="mdi mdi-package-variant-closed fs-1 text-muted"></i>
                            <h4 class="mt-4 fw-bold text-dark">No products available</h4>
                            <p class="text-muted">We're restocking fresh items. Check back in a few hours!</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 mb-5">
        <div class="container py-5">
            <div class="glass-card p-5 rounded-[3rem] text-center overflow-hidden position-relative reveal">
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10 bg-theme"></div>
                <div class="position-relative z-1">
                    <h2 class="display-4 fw-black text-dark mb-3">{{ $homeSettings?->cta_title ?? 'Ready for a Healthy Change?' }}</h2>
                    <div class="lead text-muted mb-5 mx-auto" style="max-width: 600px;">{!! $homeSettings?->cta_subtitle ?? 'Join thousands of families getting farm-fresh organics delivered straight to their kitchen.' !!}</div>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ $homeSettings?->cta_button_1_url ?? route('website.register') }}" class="btn btn-premium btn-lg">{{ $homeSettings?->cta_button_1_text ?? 'Join us Today' }}</a>
                        <a href="{{ $homeSettings?->cta_button_2_url ?? route('website.products.index') }}" class="btn btn-white btn-lg rounded-pill shadow-sm border px-5 fw-bold">{{ $homeSettings?->cta_button_2_text ?? 'Shop Now' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Reveal on Scroll Logic
            const revealCallback = (entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            };

            const observer = new IntersectionObserver(revealCallback, {
                threshold: 0.1
            });

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>
    @endpush
</x-website-layout>