<x-website-layout title="About Us | Southwest Farmers Store">
    <!-- Hero Section -->
    <div class="py-5" style="background: linear-gradient(135deg, #10b981 0%, #065f46 100%); color: white;">
        <div class="container py-5 text-center">
            <h1 class="display-3 fw-black mb-4">Our Growing Story</h1>
            <p class="fs-4 opacity-90 mx-auto" style="max-width: 700px;">
                Empowering farmers and nourishing communities since 1995. We are more than just a store; we are your partners in growth.
            </p>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="py-5 bg-white">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" 
                         class="img-fluid rounded-4 shadow-lg" alt="Our Farm">
                </div>
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-3">SINCE 1995</span>
                    <h2 class="display-5 fw-black text-dark mb-4">Nurturing the Land, Empowering People</h2>
                    <p class="text-muted fs-5 mb-4">
                        Southwest Farmers Store began as a small family-owned cooperative. Today, we stand as the leading supplier of agricultural essentials, fresh produce, and sustainable farming solutions in the region.
                    </p>
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle p-2">
                                    <i class="mdi mdi-check fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Premium Quality</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle p-2">
                                    <i class="mdi mdi-account-group fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Farmer First</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle p-2">
                                    <i class="mdi mdi-leaf fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Sustainable</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle p-2">
                                    <i class="mdi mdi-truck-delivery fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Direct Supply</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="py-5" style="background: #f8fafc;">
        <div class="container py-5">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <h2 class="display-4 fw-black text-primary">25+</h2>
                    <p class="text-muted fw-bold">Years of Trust</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-black text-primary">10k+</h2>
                    <p class="text-muted fw-bold">Farmers Served</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-black text-primary">500+</h2>
                    <p class="text-muted fw-bold">Products</p>
                </div>
                <div class="col-md-3">
                    <h2 class="display-4 fw-black text-primary">50+</h2>
                    <p class="text-muted fw-bold">Local Partners</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Team / Values Section -->
    <div class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-black text-dark">Our Core Values</h2>
                <div class="mx-auto" style="width: 80px; height: 5px; background: var(--pos-primary); margin: 20px auto;"></div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100 text-center hover-lift transition">
                        <i class="mdi mdi-heart display-4 text-danger mb-3"></i>
                        <h4 class="fw-bold text-dark">Integrity</h4>
                        <p class="text-muted">We build relationships based on honesty and transparency with every farmer we serve.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100 text-center hover-lift transition">
                        <i class="mdi mdi-lightbulb-on display-4 text-warning mb-3"></i>
                        <h4 class="fw-bold text-dark">Innovation</h4>
                        <p class="text-muted">Bringing the latest agricultural technology to local farms for better yields.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100 text-center hover-lift transition">
                        <i class="mdi mdi-account-group display-4 text-primary mb-3"></i>
                        <h4 class="fw-bold text-dark">Community</h4>
                        <p class="text-muted">We grow when our community grows. Dedicated to rural development.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-5">
        <div class="container py-5">
            <div class="bg-dark rounded-5 p-5 text-center text-white position-relative overflow-hidden shadow-lg">
                <div class="position-relative z-1">
                    <h2 class="display-5 fw-black mb-4">Ready to grow with us?</h2>
                    <p class="fs-5 opacity-75 mb-5">Join the thousands of farmers who trust Southwest Farmers Store.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('website.products.index') }}" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">Browse Catalog</a>
                        <a href="{{ route('website.contact') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill fw-bold">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-website-layout>
