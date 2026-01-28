<x-app-layout title="Retail POS Pro">
    @push('styles')
    <style>
        :root {
            --pos-primary: #10b981;
            --pos-bg: #f9fafb;
            --pos-card: #ffffff;
            --pos-border: #e5e7eb;
        }
        body { background-color: var(--pos-bg); font-family: 'Manrope', sans-serif; }
        .pos-container { min-height: calc(100vh - 70px); }
        .panel { background: var(--pos-card); border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 1.5rem; }
        .cat-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: #e6f4ea;
            border: 1px solid #a7e9bb;
            transition: all 0.2s;
            color: #065f46;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .cat-btn.active, .cat-btn:hover {
            background: var(--pos-primary);
            color: white;
            border-color: var(--pos-primary);
        }
        .product-card {
            border: 1px solid var(--pos-border);
            border-radius: 0.75rem;
            transition: all 0.2s;
            cursor: pointer;
            background: white;
        }
        .product-card:hover {
            border-color: var(--pos-primary);
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.2);
        }
        .product-img {
            height: 120px;
            background-size: cover;
            background-position: center;
            border-radius: 0.5rem;
            background-color: #f3f4f6;
        }
        .pay-btn {
            background: linear-gradient(135deg, var(--pos-primary), #059669);
            color: white;
            font-weight: 800;
            padding: 1.4rem;
            border-radius: 0.75rem;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3);
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px dashed var(--pos-border);
        }
        .totals-row:last-of-type { border-bottom: none; }
        .totals-label { font-weight: 600; color: #374151; }
        .totals-input { width: 100px; text-align: right; }
        .totals-amount { font-weight: 700; min-width: 100px; text-align: right; }
        .grand-total-label { font-size: 1.5rem; font-weight: 800; }
        .grand-total-amount { font-size: 1.8rem; font-weight: 800; color: var(--pos-primary); }
        #imagePreview {
            max-width: 120px;
            max-height: 120px;
            margin-top: 10px;
            display: none;
            border-radius: 0.5rem;
            border: 2px dashed #d1d5db;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @endpush

    <div class="container-fluid pos-container p-4">
        <div class="row g-4">
            <!-- Left Panel: Categories + Product Grid -->
            <div class="col-lg-7 panel d-flex flex-column">
                <h5 class="fw-bold mb-4 text-dark">Add Products</h5>

                <!-- Categories -->
                <div class="d-flex gap-2 flex-wrap mb-4 overflow-auto hide-scrollbar" style="max-width: 100%;">
                    <button class="cat-btn active" data-category="all">All</button>
                    @foreach($categories as $cat)
                        <button class="cat-btn" data-category="{{ $cat->id ?? $cat->name }}">{{ $cat->name }}</button>
                    @endforeach
                </div>

                <!-- Product Grid -->
                <div id="productGrid" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 flex-grow-1 overflow-auto">
                    <div class="col-12 text-center text-muted py-5">Loading products...</div>
                </div>
            </div>

            <!-- Right Panel: Customer + Cart + Totals + Pay -->
            <div class="col-lg-5 panel d-flex flex-column">
                <h5 class="fw-bold mb-4 text-dark">Customer</h5>

                <!-- Customer Search -->
                <div class="position-relative mb-3">
                    <input type="text" id="customerSearch" class="form-control" placeholder="Search by name/phone..." autocomplete="off">
                    <div id="customerDropdown" class="border bg-white shadow-sm w-100 position-absolute mt-1" style="max-height: 250px; overflow-y: auto; display: none; z-index: 1000;"></div>
                </div>

                <!-- Selected Customer -->
                <div id="selectedCustomer" class="alert alert-success d-none mb-3 p-3 rounded">
                    <strong>Selected:</strong> <span id="customerName" class="fw-bold"></span> (<span id="customerPhone"></span>)
                </div>

                <!-- Add New Customer Button -->
                <button class="btn btn-outline-success w-100 mb-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    + Add New Customer
                </button>

                <!-- Cart Section -->
                <div class="border rounded p-3 mb-4 flex-grow-1 d-flex flex-column">
                    <h6 class="fw-bold mb-3">Cart</h6>
                    <div class="overflow-auto flex-grow-1">
                        <table class="table table-sm table-borderless">
                            <tbody id="cartBody"></tbody>
                        </table>
                    </div>
                    <div id="emptyCart" class="text-center text-muted py-4 fw-bold">Cart is empty</div>
                </div>

                <!-- Totals Section -->
                <div class="border rounded p-4 bg-light mb-4">
                    <div class="totals-row">
                        <span class="totals-label">Subtotal</span>
                        <span id="subtotal" class="totals-amount">₹0.00</span>
                    </div>
                    <div class="totals-row">
                        <span class="totals-label">GST (18%)</span>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="gstInput" class="form-control form-control-sm totals-input" value="0" step="0.01">
                            <span id="gstAmount" class="totals-amount">₹0.00</span>
                        </div>
                    </div>
                    <div class="totals-row">
                        <span class="totals-label">Tax</span>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="taxInput" class="form-control form-control-sm totals-input" value="0" step="0.01">
                            <span id="taxAmount" class="totals-amount">₹0.00</span>
                        </div>
                    </div>
                    <div class="totals-row">
                        <span class="totals-label">Discount</span>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" id="discount" class="form-control form-control-sm totals-input" value="0" step="0.01">
                            <span id="discountAmount" class="totals-amount">-₹0.00</span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="totals-row">
                        <span class="grand-total-label">Grand Total</span>
                        <span id="grandTotal" class="grand-total-amount">₹0.00</span>
                    </div>
                </div>

                <button class="pay-btn w-100" id="payBtn">PAY NOW</button>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Customer Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Due Amount</label>
                            <input type="number" name="due" class="form-control" value="0" min="0" step="0.01">
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Customer Image</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif">
                            <small class="text-muted">Accepted formats: JPG, PNG, GIF (Max: 2MB)</small>
                            <img id="imagePreview" src="" alt="Image Preview">
                        </div>
                        <div class="d-flex gap-3 mt-4">
                            <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success flex-fill fw-bold">Save Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let cart = [];
        let selectedCustomerId = null;
        let selectedCategory = 'all';
        const GST_RATE = 0.18;
        const CSRF_TOKEN = '{{ csrf_token() }}';

        // Image preview in modal
        document.querySelector('[name="image"]')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            if (file && file.size <= 2 * 1024 * 1024) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
                if (file) alert('File size must be <= 2MB');
            }
        });

        $(document).ready(function() {
            loadProducts();

            // Category filter
            $(document).on('click', '.cat-btn', function() {
                $('.cat-btn').removeClass('active');
                $(this).addClass('active');
                selectedCategory = $(this).data('category');
                loadProducts();
            });

            // Load products
            function loadProducts() {
                $.ajax({
                    url: '{{ route("store.sales.search") }}',
                    data: { category: selectedCategory === 'all' ? '' : selectedCategory },
                    success: function(products) {
                        let html = '';
                        if (products.length === 0) {
                            html = '<div class="col-12 text-center text-muted py-5">No products found in this category</div>';
                        } else {
                            products.forEach(p => {
                                const badgeClass = p.quantity <= 5 ? 'bg-danger' : 'bg-success';
                                const badgeText = p.quantity <= 5 ? `Low: ${p.quantity}` : `${p.quantity} in stock`;
                                const img = p.icon ? `/storage/${p.icon}` : `https://placehold.co/200x200/f3f4f6/10b981?text=1&text=${encodeURIComponent(p.product_name.charAt(0))}`;

                                html += `
                                <div class="col">
                                    <div class="product-card p-3 text-center h-100 d-flex flex-column" 
                                         data-id="${p.product_id}" 
                                         data-name="${p.product_name}" 
                                         data-price="${p.price}" 
                                         data-stock="${p.quantity}">
                                        <div class="product-img mb-3" style="background-image: url('${img}');"></div>
                                        <h6 class="fw-bold mb-1">${p.product_name}</h6>
                                        <p class="text-success fw-bold mb-2">₹${parseFloat(p.price).toFixed(2)}</p>
                                        <span class="badge ${badgeClass} mb-3">${badgeText}</span>
                                        <button class="btn btn-success btn-sm mt-auto">Add to Cart</button>
                                    </div>
                                </div>`;
                            });
                        }
                        $('#productGrid').html(html);
                    }
                });
            }

            // Add to cart from product card
            $(document).on('click', '.product-card', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price'));
                const stock = parseFloat($(this).data('stock'));
                addToCart(id, name, price, stock, 1);
            });

            function addToCart(id, name, price, stock, qty = 1) {
                if (stock < qty) {
                    alert('Not enough stock!');
                    return;
                }
                const existing = cart.find(item => item.id === id);
                if (existing) {
                    if (existing.quantity + qty > stock) {
                        alert('Stock limit reached!');
                        return;
                    }
                    existing.quantity += qty;
                } else {
                    cart.push({ id, name, price, quantity: qty, stock });
                }
                renderCart();
            }

            function renderCart() {
                let html = '';
                let subtotal = 0;

                if (cart.length === 0) {
                    $('#emptyCart').show();
                    $('#cartBody').html('');
                } else {
                    $('#emptyCart').hide();
                    cart.forEach((item, index) => {
                        const lineTotal = item.price * item.quantity;
                        subtotal += lineTotal;
                        html += `
                        <tr>
                            <td class="align-middle">
                                <div class="fw-bold">${item.name}</div>
                                <small class="text-muted">₹${item.price.toFixed(2)} × ${item.quantity}</small>
                            </div>
                            </td>
                            <td class="align-middle text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" onclick="updateQty(${index}, -1)">-</button>
                                    <span class="btn btn-light">${item.quantity}</span>
                                    <button class="btn btn-outline-secondary" onclick="updateQty(${index}, 1)">+</button>
                                </div>
                            </td>
                            <td class="align-middle text-end fw-bold">₹${lineTotal.toFixed(2)}</td>
                            <td class="align-middle text-center">
                                <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">×</button>
                            </td>
                        </tr>`;
                    });
                    $('#cartBody').html(html);
                }
                updateTotals(subtotal);
            }

            window.updateQty = function(index, change) {
                const item = cart[index];
                const newQty = item.quantity + change;
                if (newQty < 1) return;
                if (newQty > item.stock) {
            alert('Stock limit reached!');
                    return;
                }
                item.quantity = newQty;
                renderCart();
            };

            window.removeFromCart = function(index) {
                cart.splice(index, 1);
                renderCart();
            };

            function updateTotals(subtotal) {
                const autoGst = subtotal * GST_RATE;
                const gstInput = parseFloat($('#gstInput').val()) || 0;
                const gst = gstInput || autoGst;
                const tax = parseFloat($('#taxInput').val()) || 0;
                const discount = parseFloat($('#discount').val()) || 0;
                const grandTotal = subtotal + gst + tax - discount;

                $('#subtotal').text('₹' + subtotal.toFixed(2));
                $('#gstInput').val(gst.toFixed(2));
                $('#gstAmount').text('₹' + gst.toFixed(2));
                $('#taxAmount').text('₹' + tax.toFixed(2));
                $('#discountAmount').text('-₹' + discount.toFixed(2));
                $('#grandTotal').text('₹' + grandTotal.toFixed(2));
            }

            $('#gstInput, #taxInput, #discount').on('input', function() {
                const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
                updateTotals(subtotal);
            });

            // Customer Search
            $('#customerSearch').on('keyup', function() {
                const term = $(this).val().trim();
                if (term.length < 2) {
                    $('#customerDropdown').hide();
                    return;
                }
                $.ajax({
                    url: '{{ route("store.sales.customers.search") }}',
                    data: { term },
                    success: function(customers) {
                        let html = '';
                        if (customers.length === 0) {
                            html = '<div class="p-3 text-muted small">No customer found</div>';
                        } else {
                            customers.forEach(c => {
                                html += `<div class="p-3 border-bottom cursor-pointer hover-bg-light" onclick="selectCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}', '${c.phone}')">
                                            <strong>${c.name}</strong><br>
                                            <small class="text-muted">${c.phone}</small>
                                         </div>`;
                            });
                        }
                        $('#customerDropdown').html(html).show();
                    }
                });
            });

            // Hide dropdown on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#customerSearch, #customerDropdown').length) {
                    $('#customerDropdown').hide();
                }
            });

            window.selectCustomer = function(id, name, phone) {
                selectedCustomerId = id;
                $('#customerName').text(name);
                $('#customerPhone').text(phone);
                $('#selectedCustomer').removeClass('d-none');
                $('#customerSearch').val('');
                $('#customerDropdown').hide();
            };

            // Add New Customer
            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route("store.sales.customers.store") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            $('#addCustomerModal').modal('hide');
                            selectCustomer(res.customer.id, res.customer.name, res.customer.phone);
                            alert('Customer added successfully!');
                            $('#addCustomerForm')[0].reset();
                            $('#imagePreview').hide();
                        }
                    },
                    error: function() {
                        alert('Error saving customer');
                    }
                });
            });

            // Checkout
            $('#payBtn').on('click', function() {
                if (cart.length === 0) {
                    alert('Cart is empty!');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).text('Processing...');

                const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
                const gst = parseFloat($('#gstInput').val()) || 0;
                const tax = parseFloat($('#taxInput').val()) || 0;
                const discount = parseFloat($('#discount').val()) || 0;
                const total = subtotal + gst + tax - discount;

                $.ajax({
                    url: '{{ route("store.sales.checkout") }}',
                    method: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        cart: JSON.stringify(cart),
                        customer_id: selectedCustomerId || null,
                        subtotal: subtotal,
                        gst_amount: gst,
                        tax_amount: tax,
                        discount_amount: discount,
                        total_amount: total
                    },
                    success: function(res) {
                        alert('Sale successful! Invoice: ' + (res.invoice || 'Generated'));
                        cart = [];
                        selectedCustomerId = null;
                        $('#selectedCustomer').addClass('d-none');
                        renderCart();
                        loadProducts();
                    },
                    error: function(xhr) {
                        alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('PAY NOW');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>