<x-website-layout title="Home">
    
    @push('styles')
    <style>
        /* Custom Animations */
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulseSoft {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Animation Classes */
        .animate-fade-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }

        /* Hero Background Enhancement */
        .hero-section {
            background: linear-gradient(135deg, var(--theme-light) 0%, #ffffff 100%);
            position: relative;
        }
        
        .hero-blob {
            position: absolute;
            background: linear-gradient(to right, #10b981, #34d399);
            filter: blur(80px);
            opacity: 0.15;
            z-index: 0;
            border-radius: 50%;
        }

        /* Feature Cards */
        .feature-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: all 0.4s ease;
            border: 1px solid rgba(16, 185, 129, 0.1);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.08);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .feature-icon-wrapper {
            transition: all 0.4s ease;
        }
        .feature-card:hover .feature-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
            background: var(--theme-primary) !important;
            color: white !important;
        }

        /* Product Cards Enhancement */
        .product-card {
            border: 1px solid #f1f5f9;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .product-card:hover {
            border-color: var(--theme-primary);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06) !important;
        }
        .product-img-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
            background: #f8fafc;
        }
        .product-img-wrapper img {
            transition: transform 0.6s ease;
        }
        .product-card:hover .product-img-wrapper img {
            transform: scale(1.08);
        }
        
        /* Add to Cart Button Hover */
        .btn-add-cart {
            transition: all 0.3s ease;
        }
        .product-card:hover .btn-add-cart {
            background-color: var(--theme-primary) !important;
            color: white !important;
            animation: pulseSoft 1.5s infinite;
        }

        /* Image Mask for About Section */
        .about-image-mask {
            mask-image: radial-gradient(circle, black 70%, transparent 100%);
            -webkit-mask-image: radial-gradient(circle, black 70%, transparent 100%);
            transition: transform 0.8s ease;
        }
        .about-section:hover .about-image-mask {
            transform: scale(1.05);
        }
    </style>
    @endpush

    <section class="hero-section py-5 position-relative overflow-hidden min-vh-75 d-flex align-items-center">
        <div class="hero-blob" style="width: 500px; height: 500px; top: -100px; left: -100px;"></div>
        <div class="hero-blob" style="width: 400px; height: 400px; bottom: -50px; right: -50px; background: linear-gradient(to right, #fbbf24, #10b981);"></div>
        
        <div class="container py-5 mt-md-4 position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-6 z-1">
                    <div class="animate-fade-up">
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-4 py-2 mb-4 fw-bold border border-success border-opacity-25 shadow-sm">
                            <i class="mdi mdi-sparkles me-1"></i> 100% Fresh & Organic
                        </span>
                    </div>
                    <h1 class="display-3 fw-bolder text-dark mb-4 animate-fade-up delay-100" style="line-height: 1.15;">
                        Healthy food for a <br/>
                        <span class="text-theme position-relative">
                            healthier
                            <svg class="position-absolute w-100" style="bottom: -10px; left: 0; height: 12px;" viewBox="0 0 200 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.00035 6.81355C44.8569 2.50233 113.676 -1.82136 197.807 7.00015" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span> lifestyle.
                    </h1>
                    <p class="lead text-muted mb-5 pe-lg-5 animate-fade-up delay-200">
                        Discover the freshest groceries, daily essentials, and organic products delivered straight to your doorstep with guaranteed quality.
                    </p>
                    <div class="d-flex flex-wrap gap-3 animate-fade-up delay-300">
                        <a href="{{ route('website.products.index') }}" class="btn btn-theme btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                            <i class="mdi mdi-cart-outline me-2"></i>Start Shopping
                        </a>
                        <a href="#about" class="btn btn-white btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm border text-dark hover-lift">
                            <i class="mdi mdi-play-circle-outline me-2 text-theme"></i> Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center z-1 animate-fade-up delay-400">
                    <div class="position-relative floating-element">
                        <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Fresh Groceries" class="img-fluid rounded-circle shadow-lg" style="width: 90%; aspect-ratio: 1/1; object-fit: cover; border: 8px solid white;">
                        
                        <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center" style="bottom: 10%; left: -5%; animation: float 4s ease-in-out infinite reverse;">
                            <div class="bg-warning bg-opacity-25 text-warning rounded-circle p-2 me-3">
                                <i class="mdi mdi-star fs-4"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="mb-0 fw-bold">Top Quality</h6>
                                <small class="text-muted">Handpicked daily</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 position-relative z-2" style="margin-top: -60px;">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4 animate-fade-up delay-100">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper d-inline-flex align-items-center justify-content-center bg-theme-light text-theme rounded-circle mb-4" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-truck-fast fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Free Delivery</h4>
                        <p class="text-muted small px-3 mb-0">On all orders over $50. Fast and secure delivery right to your home.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-up delay-200">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper d-inline-flex align-items-center justify-content-center bg-theme-light text-theme rounded-circle mb-4" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-leaf fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Fresh & Organic</h4>
                        <p class="text-muted small px-3 mb-0">We source the best quality products directly from trusted local farmers.</p>
                    </div>
                </div>
                <div class="col-md-4 animate-fade-up delay-300">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper d-inline-flex align-items-center justify-content-center bg-theme-light text-theme rounded-circle mb-4" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-shield-check fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Secure Payment</h4>
                        <p class="text-muted small px-3 mb-0">100% secure payment methods with end-to-end encryption for peace of mind.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 mt-4">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
                <div class="animate-fade-up">
                    <span class="text-theme fw-bold text-uppercase tracking-wide small"><i class="mdi mdi-fire me-1"></i> Trending Now</span>
                    <h2 class="display-6 fw-bold text-dark mb-0 mt-2">Our Best Sellers</h2>
                </div>
                <a href="{{ route('website.products.index') }}" class="btn btn-outline-theme rounded-pill px-4 py-2 fw-bold d-none d-md-inline-flex align-items-center animate-fade-up">
                    View All Products <i class="mdi mdi-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="row g-4">
                {{-- DYNAMIC PRODUCT LOOP --}}
                @forelse($featuredProducts ?? [1,2,3,4] as $index => $product)
                    @php
                        // Check if it's actual DB object or fallback dummy data
                        $isObj = is_object($product);
                        $name = $isObj ? $product->product_name : 'Fresh Organic Item';
                        $price = $isObj ? $product->price : '4.99';
                        $catName = ($isObj && $product->category) ? $product->category->name : 'Fresh Produce';
                        $img = ($isObj && $product->image) ? Storage::url($product->image) : 'https://placehold.co/400x400/ecfdf5/10b981?text=Fresh+Product';
                        $delay = ($index % 4) * 100; // Staggered animation
                    @endphp

                    <div class="col-6 col-md-4 col-lg-3 animate-fade-up" style="animation-delay: {{ $delay }}ms;">
                        <div class="card h-100 product-card rounded-4 bg-white p-2">
                            <div class="product-img-wrapper p-3 text-center mb-3">
                                @if($index == 0 || $index == 2)
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2 z-1 rounded-pill px-2 py-1 shadow-sm">-10%</span>
                                @endif
                                <button class="btn btn-light rounded-circle position-absolute top-0 end-0 m-2 z-1 shadow-sm hover-lift" style="width: 35px; height: 35px; padding: 0;">
                                    <i class="mdi mdi-heart-outline text-muted"></i>
                                </button>
                                <img src="{{ $img }}" class="img-fluid" alt="{{ $name }}" style="height: 160px; object-fit: contain;">
                            </div>
                            
                            <div class="card-body p-2 pt-0 d-flex flex-column">
                                <small class="text-theme fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">{{ $catName }}</small>
                                <h6 class="card-title fw-bold text-dark mb-2 text-truncate" title="{{ $name }}">{{ $name }}</h6>
                                
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="fs-5 fw-black text-dark">${{ number_format((float)$price, 2) }}</span>
                                    <button class="btn btn-light btn-add-cart rounded-circle text-theme shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;" title="Add to Cart">
                                        <i class="mdi mdi-cart-plus fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 animate-fade-up">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="mdi mdi-package-variant-closed fs-1 text-muted"></i>
                        </div>
                        <h4 class="fw-bold">No products found</h4>
                        <p class="text-muted">Products are being updated. Check back soon!</p>
                    </div>
                @endforelse
            </div>
            
            <div class="text-center mt-5 d-md-none">
                <a href="{{ route('website.products.index') }}" class="btn btn-outline-theme btn-lg rounded-pill px-5 fw-bold w-100">View All Products</a>
            </div>
        </div>
    </section>

    <section id="about" class="py-5 about-section overflow-hidden">
        <div class="container py-5">
            <div class="card border-0 bg-theme-dark text-white rounded-5 overflow-hidden shadow-lg position-relative">
                <div class="position-absolute top-0 end-0 opacity-10" style="width: 300px; height: 300px; background-image: radial-gradient(circle, white 2px, transparent 2px); background-size: 20px 20px;"></div>
                
                <div class="row g-0 align-items-center">
                    <div class="col-lg-6 p-5 p-xl-5 z-1 animate-fade-up">
                        <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3 py-2 fw-bold text-uppercase letter-spacing-1 mb-3 border border-success border-opacity-25">
                            About FreshStore
                        </span>
                        <h2 class="display-5 fw-bold mb-4 line-height-tight">Quality you can trust,<br> prices you'll love.</h2>
                        <p class="lead opacity-75 mb-5 fs-6" style="line-height: 1.8;">
                            We believe that everyone deserves access to fresh, healthy, and affordable food. Our dedicated team works day and night to bring the best directly from local farms right to your dining table.
                        </p>
                        <div class="d-flex align-items-center gap-4 mb-5 opacity-75">
                            <div class="d-flex align-items-center"><i class="mdi mdi-check-circle text-success fs-4 me-2"></i> 100% Organic</div>
                            <div class="d-flex align-items-center"><i class="mdi mdi-check-circle text-success fs-4 me-2"></i> Daily Fresh</div>
                        </div>
                        <div>
                            <a href="#contact" class="btn btn-theme btn-lg rounded-pill px-5 py-3 fw-bold border border-white border-2 shadow-sm hover-lift">
                                Contact Us Today <i class="mdi mdi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 h-100 animate-fade-up delay-200">
                        <div class="h-100 w-100 about-image-mask" style="min-height: 500px; background-image: url('https://images.unsplash.com/photo-1604719312566-8912e9227c6a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'); background-size: cover; background-position: center;">
                            <div class="w-100 h-100 bg-success bg-opacity-25"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-website-layout>