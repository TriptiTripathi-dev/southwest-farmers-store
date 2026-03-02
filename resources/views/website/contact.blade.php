<x-website-layout title="Contact Us - FreshStore">
    
    @push('styles')
    <style>
        .page-header {
            background: linear-gradient(rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.1));
            padding: 80px 0;
            margin-bottom: 60px;
        }

        .contact-card {
            border-radius: 2rem;
            border: 1px solid #f1f5f9;
            background: white;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
        }

        .contact-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -12px rgba(16, 185, 129, 0.12);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: var(--theme-light, #ecfdf5);
            color: var(--theme-primary, #10b981);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .form-control-premium {
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-control-premium:focus {
            background-color: white;
            border-color: var(--theme-primary, #10b981);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--theme-primary, #10b981), #059669);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 100px;
            font-weight: 700;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.5);
            color: white;
        }

        .map-placeholder {
            border-radius: 2rem;
            overflow: hidden;
            background: #f1f5f9;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--theme-dark, #064e3b), var(--theme-primary, #10b981));
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
    </style>
    @endpush

    <!-- Header Section -->
    <section class="page-header">
        <div class="container text-center">
            <span class="badge bg-theme-light text-theme rounded-pill px-4 py-2 mb-3 fw-bold">
                👋 GET IN TOUCH
            </span>
            <h1 class="display-3 fw-black mb-3 text-gradient">How can we help?</h1>
            <p class="lead text-muted mx-auto" style="max-width: 600px;">Have questions about our products or your order? Our team is here to provide dedicated support.</p>
        </div>
    </section>

    <!-- Contact Info & Form -->
    <section class="pb-5 mb-5">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Info -->
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="contact-card p-4 reveal active">
                            <div class="contact-icon">
                                <i class="mdi mdi-map-marker-outline"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Visit Our Store</h5>
                            <p class="text-muted mb-0">123 Fresh Way, Organic Valley,<br/>Green City, GC 56789</p>
                        </div>

                        <div class="contact-card p-4 reveal active" style="transition-delay: 0.1s">
                            <div class="contact-icon">
                                <i class="mdi mdi-phone-outline"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Call Us Anytime</h5>
                            <p class="text-muted mb-0">+1 (555) fresh-store<br/>Mon-Sat: 8am - 8pm</p>
                        </div>

                        <div class="contact-card p-4 reveal active" style="transition-delay: 0.2s">
                            <div class="contact-icon">
                                <i class="mdi mdi-email-outline"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Email Support</h5>
                            <p class="text-muted mb-0">hello@freshstore.com<br/>support@freshstore.com</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="contact-card p-5 reveal active">
                        <h3 class="fw-black mb-4">Send us a Message</h3>
                        
                        @if(session('success'))
                            <div class="alert alert-success rounded-4 p-4 border-0 shadow-sm mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="mdi mdi-check fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ session('success') }}</h6>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('website.contact.submit') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Full Name</label>
                                        <input type="text" name="name" class="form-control form-control-premium @error('name') is-invalid @enderror" placeholder="John Doe" value="{{ old('name') }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Email Address</label>
                                        <input type="email" name="email" class="form-control form-control-premium @error('email') is-invalid @enderror" placeholder="john@example.com" value="{{ old('email') }}" required>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Subject</label>
                                        <select name="subject" class="form-select form-control-premium">
                                            <option {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                            <option {{ old('subject') == 'Order Status' ? 'selected' : '' }}>Order Status</option>
                                            <option {{ old('subject') == 'Product Question' ? 'selected' : '' }}>Product Question</option>
                                            <option {{ old('subject') == 'Feedback' ? 'selected' : '' }}>Feedback</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-2">Your Message</label>
                                        <textarea name="message" class="form-control form-control-premium @error('message') is-invalid @enderror" rows="5" placeholder="How can we help you today?" required>{{ old('message') }}</textarea>
                                        @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-premium btn-lg px-5">
                                        Send Message <i class="mdi mdi-send ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Map Placeholder -->
            <div class="mt-5 pt-5 reveal active">
                <div class="map-placeholder">
                    <div class="text-center">
                        <i class="mdi mdi-map-outline fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark">Interactive Map Coming Soon</h5>
                        <p class="text-muted">We're integrating Google Maps for easier navigation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-website-layout>
