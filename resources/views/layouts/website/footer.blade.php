<footer id="contact" class="bg-theme-dark text-white pt-5 pb-3 mt-auto">
    <div class="container pt-4">
        <div class="row g-4 mb-5">
            
            <div class="col-12 col-lg-4 pe-lg-5">
                <h4 class="fw-bold text-white mb-4 d-flex align-items-center">
                    <i class="mdi mdi-leaf text-success me-2 fs-3"></i> FreshStore
                </h4>
                <p class="text-white-50 small mb-4 pe-md-4">
                    Your premier destination for high-quality, fresh, and organic everyday essentials. We deliver happiness and health right to your doorstep.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="mdi mdi-facebook fs-5"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="mdi mdi-instagram fs-5"></i></a>
                    <a href="#" class="btn btn-outline-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="mdi mdi-twitter fs-5"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-2">
                <h6 class="fw-bold text-uppercase mb-4 text-white">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('website.home') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">Home</a></li>
                    <li class="mb-2"><a href="{{ route('website.products.index') }}" class="text-white-50 text-decoration-none text-white-hover transition-all">Shop Products</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none text-white-hover transition-all">About Us</a></li>
                    <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none text-white-hover transition-all">Contact</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                <h6 class="fw-bold text-uppercase mb-4 text-white">Contact Us</h6>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="mdi mdi-map-marker mt-1 me-2 text-success"></i>
                        <span>123 Fresh Market Street,<br>Green City, Earth 10020</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="mdi mdi-phone me-2 text-success"></i>
                        <span>+1 (234) 567-8900</span>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="mdi mdi-email me-2 text-success"></i>
                        <span>support@freshstore.com</span>
                    </li>
                </ul>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                <h6 class="fw-bold text-uppercase mb-4 text-white">Newsletter</h6>
                <p class="text-white-50 small mb-3">Subscribe to get updates on new products and special discounts.</p>
                <form class="d-flex">
                    <input type="email" class="form-control rounded-start-pill border-0" placeholder="Your email address" required>
                    <button class="btn btn-success rounded-end-pill px-3" type="submit">
                        <i class="mdi mdi-send"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-50 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} FreshStore. All rights reserved.</p>
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
    .text-white-hover:hover { color: #10b981 !important; padding-left: 5px; }
</style>