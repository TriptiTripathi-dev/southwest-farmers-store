<x-website-layout title="Prepared Kitchen Menus - Southwest Farmers">
    
    @push('styles')
    <style>
        .page-header {
            background: linear-gradient(rgba(127, 5, 27, 0.05), rgba(127, 5, 27, 0.1));
            padding: 80px 0;
            margin-bottom: 60px;
        }

        .menu-card-enhanced {
            border-radius: 2rem;
            border: 1px solid #f1f5f9;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            background: white;
            height: 100%;
        }

        .menu-card-enhanced:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.1);
        }

        .menu-image-container {
            aspect-ratio: 4/3;
            padding: 1rem;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .menu-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 1.5rem;
            transition: transform 0.6s ease;
        }

        .menu-card-enhanced:hover .menu-image-container img {
            transform: scale(1.05);
        }

        .text-gradient {
            background: linear-gradient(135deg, #7F051B, #fb7185);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    @endpush

    <!-- Header Section -->
    <section class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-4 py-2 mb-3 fw-bold">
                        🍲 FRESH FROM OUR KITCHEN
                    </span>
                    <h1 class="display-3 fw-black text-dark mb-3">Prepared Kitchen <span class="text-gradient">Menus</span></h1>
                    <p class="lead text-muted mb-4">
                        Authentic, delicious African meals prepared fresh daily. Enjoy fast delivery or hot pickup from your nearest store.
                    </p>
                    @if($currentStore)
                        <div class="d-flex align-items-center gap-2 text-dark fw-bold">
                            <i class="mdi mdi-storefront text-danger fs-4"></i>
                            Ordering from: <span class="text-danger">{{ $currentStore->store_name }}</span>
                        </div>
                    @endif
                </div>
                <div class="col-lg-6 d-none d-lg-block text-center">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop" class="img-fluid rounded-4 shadow" alt="African Food" style="max-height: 350px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Items List -->
    <div class="container mb-5">
        @if(empty($categories) || $categories->isEmpty())
            <div class="text-center py-5">
                <i class="mdi mdi-food-fork-drink text-muted" style="font-size: 5rem;"></i>
                <h3 class="fw-bold mt-4">No Menus Available</h3>
                <p class="text-muted">We couldn't find any prepared kitchen menus for your current store location.</p>
            </div>
        @else
            @foreach($categories as $category)
                @if(isset($menuItems[$category->id]) && !$menuItems[$category->id]->isEmpty())
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h3 class="fw-bold text-dark mb-0">{{ $category->name }}</h3>
                            <a href="{{ route('website.menus.category', $category->id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">View All</a>
                        </div>

                        <div class="row g-4">
                            @foreach($menuItems[$category->id]->take(4) as $item)
                                <div class="col-md-6 col-lg-3">
                                    <div class="menu-card-enhanced d-flex flex-column">
                                        <div class="menu-image-container">
                                            @if($item->image)
                                                <img src="{{ Storage::disk('r2')->url($item->image) }}" alt="{{ $item->name }}">
                                            @else
                                                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&auto=format&fit=crop" alt="{{ $item->name }}">
                                            @endif
                                        </div>
                                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                            <h5 class="fw-bold text-dark mb-1">{{ $item->name }}</h5>
                                            <p class="text-muted small flex-grow-1">{{ Str::limit($item->description, 70) }}</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                                <span class="fs-4 fw-black text-danger">${{ number_format($item->price, 2) }}</span>
                                                <button class="btn btn-danger rounded-pill px-3 fw-bold d-flex align-items-center gap-1 add-to-cart-btn" data-id="{{ $item->id }}">
                                                    <i class="mdi mdi-cart-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.add-to-cart-btn').click(function(e) {
                e.preventDefault();
                let itemId = $(this).data('id');
                let btn = $(this);
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Adding...');

                $.ajax({
                    type: "POST",
                    url: "{{ route('website.cart.store') }}",
                    data: {
                        'menu_item_id': itemId,
                        'quantity': 1,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        btn.prop('disabled', false).html('<i class="mdi mdi-cart-plus"></i> Add');
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Added to cart successfully!'
                        });
                        
                        // Update cart badge
                        const badge = document.getElementById('cart-badge');
                        if (badge && data.cart_count) {
                            badge.textContent = data.cart_count;
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="mdi mdi-cart-plus"></i> Add');
                        if(xhr.status === 401) {
                            window.location.href = "{{ route('website.login') }}";
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong!'
                            });
                        }
                    }
                });
            });
        });
    </script>
    @endpush
</x-website-layout>
