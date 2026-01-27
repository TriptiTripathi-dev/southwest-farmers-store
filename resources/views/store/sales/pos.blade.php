<x-app-layout title="Retail POS Pro">
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pos-primary: #10b981;
            --pos-primary-dark: #059669;
            --pos-bg: #f3f4f6;
            --pos-card-bg: #ffffff;
            --pos-text: #111827;
            --pos-muted: #6b7280;
            --pos-border: #e5e7eb;
            --pos-accent: #3b82f6;
        }

        body { font-family: 'Manrope', sans-serif; background-color: var(--pos-bg); }

        /* Custom Scrollbar */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Layout */
        .pos-wrapper { height: calc(100vh - 70px); overflow: hidden; }
        .left-panel { background-color: #f9fafb; }
        .right-panel { background-color: #ffffff; border-left: 1px solid var(--pos-border); }

        /* Search Input */
        .search-input {
            border: 1px solid transparent;
            background: linear-gradient(135deg, #eefdf5 0%, #f0fdf4 100%);
            color: var(--pos-primary);
            font-weight: 500;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .search-input::placeholder { color: #6ee7b7; }
        .search-input:focus { 
            background-color: #fff; 
            border-color: var(--pos-primary); 
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            transform: translateY(-1px);
        }

        /* Categories */
        .cat-btn { 
            border-radius: 0.5rem; 
            font-weight: 600; 
            font-size: 0.85rem; 
            padding: 0.5rem 1rem; 
            border: 2px solid transparent; 
            background: white; 
            color: var(--pos-muted); 
            transition: all 0.2s;
            white-space: nowrap;
        }
        .cat-btn.active { 
            background: linear-gradient(135deg, var(--pos-primary) 0%, var(--pos-primary-dark) 100%);
            color: white; 
            border-color: var(--pos-primary);
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }
        .cat-btn:hover:not(.active) { background-color: #e5e7eb; border-color: #d1d5db; }

        /* Product Card */
        .product-card {
            border: 2px solid transparent;
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            cursor: pointer;
            overflow: hidden;
            position: relative;
        }
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .product-card:hover::before { opacity: 1; }
        .product-card:hover { 
            border-color: var(--pos-primary); 
            transform: translateY(-4px); 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .product-img { 
            height: 140px; 
            background-size: cover; 
            background-position: center; 
            border-radius: 0.75rem; 
            margin-bottom: 0.75rem; 
            background-color: #f3f4f6;
            position: relative;
            overflow: hidden;
        }
        .product-img::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent 60%, rgba(0,0,0,0.1) 100%);
        }
        
        /* Stock Badges */
        .badge-stock { 
            font-size: 0.65rem; 
            font-weight: 800; 
            padding: 0.25rem 0.5rem; 
            border-radius: 0.375rem; 
            letter-spacing: 0.025em; 
            text-transform: uppercase;
        }
        .badge-in-stock { background-color: #d1fae5; color: #059669; }
        .badge-low-stock { background-color: #fee2e2; color: #b91c1c; }

        /* Cart Styles */
        .cart-item { 
            border-bottom: 1px dashed var(--pos-border); 
            padding: 0.75rem 0;
            transition: all 0.2s;
        }
        .cart-item:hover { background-color: #f9fafb; margin: 0 -0.5rem; padding: 0.75rem 0.5rem; border-radius: 0.5rem; }
        
        .qty-btn { 
            width: 28px; 
            height: 28px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border-radius: 6px; 
            background: #f3f4f6; 
            color: var(--pos-muted); 
            border: none; 
            transition: all 0.2s;
            cursor: pointer;
        }
        .qty-btn:hover { background: var(--pos-primary); color: white; transform: scale(1.1); }
        
        /* Tax Section Enhanced */
        .tax-row {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s;
        }
        .tax-row:hover { background-color: #f9fafb; margin: 0 -0.75rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; }
        
        .tax-badge {
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            border-radius: 0.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        /* Discount Input */
        .discount-input {
            width: 80px;
            padding: 0.25rem 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            text-align: right;
            transition: all 0.2s;
        }
        .discount-input:focus {
            outline: none;
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* Pay Button */
        .btn-pay {
            background: linear-gradient(135deg, var(--pos-primary) 0%, var(--pos-primary-dark) 100%);
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
            border-radius: 0.75rem;
            border: none;
            padding: 1rem;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .btn-pay::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-pay:hover::before { left: 100%; }
        .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.5); }
        .btn-pay:active { transform: scale(0.98); }

        /* Payment Method Buttons */
        .payment-method-btn {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background: white;
            transition: all 0.2s;
            cursor: pointer;
        }
        .payment-method-btn:hover { border-color: var(--pos-primary); background-color: #f0fdf4; }
        .payment-method-btn.active {
            border-color: var(--pos-primary);
            background: linear-gradient(135deg, #eefdf5 0%, #d1fae5 100%);
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
        }

        /* Modal */
        .modal-content { border-radius: 1.5rem; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .form-control-custom { 
            background: #f9fafb; 
            border: 2px solid #e5e7eb; 
            border-radius: 0.75rem; 
            padding: 0.75rem;
            transition: all 0.2s;
        }
        .form-control-custom:focus { 
            border-color: var(--pos-primary); 
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            background-color: white;
        }

        /* Summary Cards */
        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
        }

        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cart-item { animation: slideIn 0.3s ease; }

        /* Cursor Pointer */
        .cursor-pointer { cursor: pointer; }
   .customer-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }
        .customer-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #f3f4f6; transition: 0.2s; }
        .customer-item:hover { background-color: #f0fdf4; }
    </style>
    @endpush

    <div class="container-fluid pos-wrapper p-0">
        <div class="row h-100 g-0">
            
            <div class="col-lg-8 col-md-7 h-100 left-panel d-flex flex-column">
                
                <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                            <i class="mdi mdi-cube-outline text-success fs-4"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Retail POS Pro</h5>
                    </div>
                    <div class="d-flex gap-3">
                        <span class="badge bg-light text-dark border d-flex align-items-center px-3">
                            <i class="mdi mdi-clock-outline me-2"></i> {{ now()->format('M d, Y') }}
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-danger">Exit</a>
                    </div>
                </div>

                <div class="px-4 py-3">
                    <div class="position-relative mb-3">
                        <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 fs-5 text-success"></i>
                        <input type="text" id="productSearch" class="form-control search-input ps-5" placeholder="Search by Name, SKU, or Barcode (F1)..." autofocus autocomplete="off">
                    </div>
                    
                    <div class="d-flex gap-2 overflow-auto hide-scrollbar">
                        <button class="cat-btn active" onclick="filterCategory('all', this)">All Categories</button>
                        @foreach($categories as $cat)
                            <button class="cat-btn" onclick="filterCategory('{{ $cat->slug }}', this)">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex-grow-1 overflow-auto px-4 pb-4">
                    <div id="productGrid" class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                        </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-5 h-100 right-panel d-flex flex-column shadow-lg z-1">
                
                <div class="p-4 border-bottom position-relative">
                    <label class="small fw-bold text-uppercase text-muted mb-2" style="font-size: 0.7rem;">Customer</label>
                    <div class="d-flex gap-2">
                        <div class="input-group flex-nowrap position-relative">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3 ps-3"><i class="mdi mdi-account-search text-success"></i></span>
                            <input type="text" id="customerSearch" class="form-control bg-light border-start-0" placeholder="Search Customer..." autocomplete="off">
                            <input type="hidden" id="selectedCustomerId" value="">
                            
                            <div id="customerDropdown" class="customer-dropdown"></div>
                        </div>
                        <button class="btn btn-success rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#addCustomerModal" title="New Customer">
                            <i class="mdi mdi-plus fw-bold"></i>
                        </button>
                    </div>
                </div>

                <div class="px-4 py-2 bg-light d-flex justify-content-between align-items-center border-bottom">
                    <span class="fw-bold small text-dark">Current Sale</span>
                    <button class="btn btn-link text-danger text-decoration-none small fw-bold p-0" onclick="clearCart()">CLEAR</button>
                </div>

                <div class="flex-grow-1 overflow-auto px-4 py-2" id="cartItems">
                    </div>

                <div class="p-4 bg-white border-top">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-bold">Subtotal</span>
                        <span class="fw-bold text-dark" id="subTotal">$0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-bold">GST (18%)</span>
                        <span class="fw-bold text-dark" id="gstAmount">$0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2 small align-items-center">
                        <span class="text-muted fw-bold">Discount</span>
                        <input type="number" id="discountInput" class="form-control form-control-sm text-end p-0 pe-2 border-0 bg-transparent" style="width: 60px; font-weight: bold; color: #dc3545;" placeholder="0">
                        <span class="fw-bold text-danger" id="discountAmount">-$0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-end mt-3 mb-4 pt-3 border-top border-dashed">
                        <span class="h5 fw-bold mb-0">Grand Total</span>
                        <span class="h2 fw-bolder text-success mb-0" id="grandTotal">$0.00</span>
                    </div>

                    <button class="btn-pay w-100 d-flex flex-column align-items-center justify-content-center gap-1" onclick="processCheckout()" id="payBtn" style="min-height: 60px;">
                        <i class="mdi mdi-cash-multiple fs-3"></i>
                        <span class="small">PAY NOW (F10)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <form id="createCustomerForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Name</label>
                            <input type="text" name="name" class="form-control form-control-custom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone</label>
                            <input type="text" name="phone" class="form-control form-control-custom" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">Save Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="paymentMethod" value="cash">

    @push('scripts')
    <script>
        let cart = [];
        let currentCategory = 'all';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        // Tax Rates
        const GST_RATE = 0.18;

        $(document).ready(function() {
            loadProducts();

            // Product Search
            $('#productSearch').on('keyup', function() {
                loadProducts($(this).val());
            });

            // Customer Search
            $('#customerSearch').on('keyup', function() {
                let term = $(this).val();
                if(term.length < 2) {
                    $('#customerDropdown').hide();
                    return;
                }
                
                $.ajax({
                    url: "{{ route('store.sales.customers.search') }}", // Updated Route Name
                    data: { term: term },
                    success: function(customers) {
                        let html = '';
                        if(customers.length === 0) {
                            html = '<div class="p-3 text-muted small">No customer found</div>';
                        } else {
                            customers.forEach(c => {
                                html += `<div class="customer-item" onclick="selectCustomer(${c.id}, '${c.name}', '${c.phone}')">
                                            <div class="fw-bold">${c.name}</div>
                                            <div class="small text-muted">${c.phone}</div>
                                         </div>`;
                            });
                        }
                        $('#customerDropdown').html(html).show();
                    }
                });
            });

            // Create Customer Form Submit
            $('#createCustomerForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.post("{{ route('store.sales.customers.store') }}", formData, function(res) {
                    if(res.success) {
                        $('#addCustomerModal').modal('hide');
                        selectCustomer(res.customer.id, res.customer.name, res.customer.phone);
                        alert('Customer Added Successfully');
                        $('#createCustomerForm')[0].reset();
                    }
                });
            });
        });

        // 1. Select Customer Logic
        function selectCustomer(id, name, phone) {
            $('#selectedCustomerId').val(id);
            $('#customerSearch').val(name + ' (' + phone + ')');
            $('#customerDropdown').hide();
        }

        // 2. Filter Category
        function filterCategory(slug, btn) {
            $('.cat-btn').removeClass('active');
            $(btn).addClass('active');
            currentCategory = slug;
            loadProducts($('#productSearch').val());
        }

        // 3. Load Products (With Stock Logic)
        function loadProducts(term = '') {
            $.ajax({
                url: "{{ route('store.sales.search') }}",
                data: { term: term, category: currentCategory },
                success: function(products) {
                    let html = '';
                    if(products.length === 0) {
                        html = '<div class="col-12 text-center text-muted mt-5"><p>No products found.</p></div>';
                    } else {
                        products.forEach(p => {
                            // Logic: Low Stock (<=5) shows Red Badge
                            let badge = '';
                            if(p.quantity == 0) badge = '<span class="badge-stock badge-low-stock">OUT OF STOCK</span>';
                            else if(p.quantity <= 5) badge = `<span class="badge-stock badge-low-stock">LOW: ${p.quantity}</span>`;
                            else badge = `<span class="badge-stock badge-in-stock">${p.quantity} IN STOCK</span>`;

                            // Use Placeholder or Real Image
                            let img = p.image ? `/storage/${p.image}` : `https://placehold.co/200x200/f3f4f6/10b981?text=${p.product_name.charAt(0)}`;

                            html += `
                            <div class="col">
                                <div class="product-card p-3 h-100 d-flex flex-column" 
                                     onclick="addToCart(${p.product_id}, '${p.product_name.replace(/'/g, "\\'")}', ${p.price}, ${p.quantity})">
                                    <div class="product-img" style="background-image: url('${img}');"></div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" title="${p.product_name}">${p.product_name}</h6>
                                        ${badge}
                                    </div>
                                    <div class="mt-auto">
                                        <small class="text-muted d-block mb-1" style="font-size: 0.7rem;">SKU: ${p.sku}</small>
                                        <span class="h5 fw-bolder text-success mb-0">$${parseFloat(p.price).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>`;
                        });
                    }
                    $('#productGrid').html(html);
                }
            });
        }

        // 4. Add To Cart
        window.addToCart = function(id, name, price, maxStock) {
            if(maxStock <= 0) return alert('Item Out of Stock');
            
            let existing = cart.find(c => c.id === id);
            if(existing) {
                if(existing.quantity + 1 > maxStock) return alert('Stock Limit Reached');
                existing.quantity++;
            } else {
                cart.push({ id, name, price: parseFloat(price), quantity: 1, max: maxStock });
            }
            renderCart();
        }

        // 5. Render Cart & Calculate GST
        function renderCart() {
            let html = '';
            let subtotal = 0;

            if(cart.length === 0) {
                $('#cartItems').html('<div class="text-center mt-5 text-muted small">Cart is empty</div>');
                updateTotals(0);
                return;
            }

            cart.forEach((item, index) => {
                subtotal += item.price * item.quantity;
                html += `
                <div class="cart-item d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width:150px;">${item.name}</h6>
                        <small class="text-success fw-bold">${item.price} x ${item.quantity}</small>
                    </div>
                    <div class="d-flex align-items-center bg-light rounded-3 p-1">
                        <button class="qty-btn" onclick="updateQty(${index}, -1)">-</button>
                        <span class="fw-bold px-2 text-dark">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                    <div class="text-end">
                        <span class="d-block fw-bold text-dark">${(item.price * item.quantity).toFixed(2)}</span>
                        <i class="mdi mdi-delete-outline text-danger cursor-pointer" onclick="removeFromCart(${index})"></i>
                    </div>
                </div>`;
            });

            $('#cartItems').html(html);
            updateTotals(subtotal);
        }

        function updateTotals(subtotal) {
            let discount = parseFloat($('#discountInput').val()) || 0;
            let taxableAmount = subtotal - discount;
            if(taxableAmount < 0) taxableAmount = 0;

            let gst = taxableAmount * GST_RATE;
            let grandTotal = taxableAmount + gst;

            $('#subTotal').text('$' + subtotal.toFixed(2));
            $('#discountAmount').text('-$' + discount.toFixed(2));
            $('#gstAmount').text('$' + gst.toFixed(2));
            $('#grandTotal').text('$' + grandTotal.toFixed(2));
        }

        $('#discountInput').on('input', function() { renderCart(); });

        window.updateQty = function(index, change) {
            let item = cart[index];
            let newQty = item.quantity + change;
            if(newQty > item.max) return alert('Max Stock Reached');
            if(newQty < 1) return;
            item.quantity = newQty;
            renderCart();
        }

        window.removeFromCart = function(index) {
            cart.splice(index, 1);
            renderCart();
        }

        window.clearCart = function() {
            cart = [];
            renderCart();
            $('#customerSearch').val('');
            $('#selectedCustomerId').val('');
        }

        window.processCheckout = function() {
            if(cart.length === 0) return alert('Cart is Empty');
            
            let btn = $('#payBtn');
            btn.prop('disabled', true).text('Processing...');
            
            // Get totals from DOM to ensure sync
            let sub = parseFloat($('#subTotal').text().replace('$',''));
            let tax = parseFloat($('#gstAmount').text().replace('$',''));
            let total = parseFloat($('#grandTotal').text().replace('$',''));
            let custId = $('#selectedCustomerId').val();

            $.ajax({
                url: "{{ route('store.sales.checkout') }}",
                method: "POST",
                data: {
                    _token: csrfToken,
                    cart: cart,
                    customer_id: custId,
                    payment_method: $('#paymentMethod').val(),
                    subtotal: sub,
                    tax_amount: tax,
                    total_amount: total
                },
                success: function(res) {
                    alert('Sale Successful! Invoice: ' + res.invoice);
                    clearCart();
                    loadProducts(); 
                },
                error: function(err) {
                    alert('Error: ' + err.responseJSON.message);
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="mdi mdi-cash-multiple fs-3"></i> <span class="small">PAY NOW (F10)</span>');
                }
            });
        }
    </script>
    @endpush
</x-app-layout>