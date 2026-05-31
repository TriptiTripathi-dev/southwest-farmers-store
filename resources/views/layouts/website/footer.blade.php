@php
    $settings = \App\Models\StoreSetting::first();
@endphp
<footer id="contact" class="bg-theme-dark text-white pt-5 pb-3 mt-auto">
    <div class="container pt-4">
        <div class="row g-4 mb-5">
            
            <div class="col-12 col-lg-4 pe-lg-5">
                <h4 class="fw-bold text-white mb-4 d-flex align-items-center">
                    <img src="{{ optional($settings)->logo ? Storage::disk('r2')->url($settings->logo) : asset('assets/images/logo.jpg') }}" alt="{{ optional($settings)->app_name ?? 'Southwest Farmers' }}" height="80" class="me-2 rounded shadow-sm bg-white p-1">
                </h4>
                <p class="text-white-50 small mb-4 pe-md-4">
                    {{ optional($settings)->footer_description ?? 'Your premier destination for high-quality, fresh, and organic everyday essentials. We deliver happiness and health right to your doorstep.' }}
                </p>
                <div class="d-flex gap-3">
                    @if(optional($settings)->facebook_url)
                    <a href="{{ $settings->facebook_url }}" target="_blank" class="btn btn-outline-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="mdi mdi-facebook fs-5"></i></a>
                    @endif
                    @if(optional($settings)->instagram_url)
                    <a href="{{ $settings->instagram_url }}" target="_blank" class="btn btn-outline-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="mdi mdi-instagram fs-5"></i></a>
                    @endif
                    @if(optional($settings)->twitter_url)
                    <a href="{{ $settings->twitter_url }}" target="_blank" class="btn btn-outline-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="mdi mdi-twitter fs-5"></i></a>
                    @endif
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-2">
                <h6 class="fw-bold text-uppercase mb-4 text-white">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('website.home') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">Home</a></li>
                    <li class="mb-2"><a href="{{ route('website.products.pos') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">Products</a></li>
                    <li class="mb-2"><a href="{{ route('website.about') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">About Us</a></li>
                    <li class="mb-2"><a href="{{ route('website.contact') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">Contact Us</a></li>
                    <li class="mb-2"><a href="{{ route('website.legal', 'privacy-policy') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">Privacy Policy</a></li>
                    <li class="mb-2"><a href="{{ route('website.legal', 'terms-and-conditions') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">Terms & Conditions</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                <h6 class="fw-bold text-uppercase mb-4 text-white">Contact Us</h6>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="mdi mdi-map-marker mt-1 me-2 text-theme"></i>
                        <span>{!! nl2br(e(optional($settings)->address ?? "123 Fresh Market Street,\nGreen City, Earth 10020")) !!}</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="mdi mdi-phone me-2 text-theme"></i>
                        <span>{{ optional($settings)->app_phone ?? '+1 (234) 567-8900' }}</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="mdi mdi-email me-2 text-theme"></i>
                        <span>{{ optional($settings)->support_email ?? 'support@southwestfarmers.com' }}</span>
                    </li>
                </ul>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                <h6 class="fw-bold text-uppercase mb-4 text-white">Newsletter</h6>
                <p class="text-white-50 small mb-3">Subscribe to get updates on new products and special discounts.</p>
                <form id="newsletterForm" action="{{ route('website.newsletter.subscribe') }}" method="POST" class="d-flex">
                    @csrf
                    <input type="email" name="email" id="newsletterEmail" class="form-control rounded-start-pill border-0" placeholder="Your email address" required>
                    <button class="btn btn-theme rounded-end-pill px-3" type="submit">
                        <i class="mdi mdi-send"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-50 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} {{ optional($settings)->app_name ?? 'Southwest Farmers' }}. All rights reserved.</p>
            <div class="d-flex gap-3 mt-3 mt-md-0">
                <i class="mdi mdi-cc-visa fs-3 text-white-50"></i>
                <i class="mdi mdi-cc-mastercard fs-3 text-white-50"></i>
                <i class="mdi mdi-cc-paypal fs-3 text-white-50"></i>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Utility for footer link hover */
    .text-white-hover:hover { color: #019934 !important; padding-left: 5px; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('newsletterForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const emailInput = document.getElementById('newsletterEmail');
                const email = emailInput.value;
                const token = form.querySelector('input[name="_token"]').value;

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    if (res.status === 200) {
                        toastr.success(res.body.message || 'Subscribed successfully!');
                        emailInput.value = '';
                    } else {
                        toastr.error(res.body.message || 'Subscription failed. Please try again.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error('An error occurred. Please try again.');
                });
            });
        }
    });
</script>
@endpush