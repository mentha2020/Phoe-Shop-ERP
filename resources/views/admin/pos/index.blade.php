@extends('layouts.pos')

@section('content')
<script>
function posApp() {
    return {
        products: @json($products),
        filteredProducts: [],
        cart: [],
        searchQuery: '',
        selectedCategory: '',
        selectedBranch: '{{ $branchId }}',
        selectedCustomer: '',
        discountAmount: 0,
        showPaymentModal: false,
        showCart: false,
        processing: false,
        paymentMethod: 'cash',
        paidAmount: 0,
        dueDate: '',
        paymentMethods: [
            { value: 'cash', label: 'Cash', icon: 'bi bi-cash' },
            { value: 'card', label: 'Credit/Debit Card', icon: 'bi bi-credit-card' },
            { value: 'transfer', label: 'Bank Transfer', icon: 'bi bi-bank' },
            { value: 'e_wallet', label: 'E-Wallet', icon: 'bi bi-wallet2' },
            { value: 'credit', label: 'Credit Sale', icon: 'bi bi-person-badge' },
        ],

        init() {
            this.filteredProducts = [...this.products];
            this.processing = false;
            window.addEventListener('pageshow', () => {
                this.processing = false;
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.processing) {
                    this.processing = false;
                }
            });
        },

        searchProducts() {
            let filtered = [...this.products];
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(p =>
                    p.name.toLowerCase().includes(q) ||
                    (p.sku && p.sku.toLowerCase().includes(q)) ||
                    (p.barcode && p.barcode.toLowerCase().includes(q))
                );
            }
            if (this.selectedCategory) {
                filtered = filtered.filter(p => p.category_id == this.selectedCategory);
            }
            this.filteredProducts = filtered;
        },

        addToCart(product) {
            const existing = this.cart.find(item => item.product_id === product.id);
            if (existing) {
                if (existing.quantity < product.available_quantity) {
                    existing.quantity++;
                }
            } else {
                this.cart.push({
                    product_id: product.id,
                    product_variant_id: null,
                    product_name: product.name,
                    product_sku: product.sku,
                    unit_price: parseFloat(product.selling_price),
                    quantity: 1,
                    max_quantity: product.available_quantity,
                    discount_amount: 0,
                });
            }
        },

        updateQuantity(index, delta) {
            const item = this.cart[index];
            const newQty = item.quantity + delta;
            if (newQty >= 1 && newQty <= item.max_quantity) {
                item.quantity = newQty;
            }
        },

        setQuantity(index, value) {
            const item = this.cart[index];
            const qty = parseInt(value);
            if (qty >= 1 && qty <= item.max_quantity) {
                item.quantity = qty;
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm('Clear all items from cart?')) {
                this.cart = [];
                this.discountAmount = 0;
            }
        },

        get cartSubtotal() {
            return this.cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
        },

        get cartTotal() {
            return this.cartSubtotal - this.discountAmount;
        },

        get change() {
            return Math.max(0, this.paidAmount - this.cartTotal);
        },

        openPaymentModal() {
            this.paidAmount = this.cartTotal;
            this.paymentMethod = 'cash';
            this.dueDate = '';
            this.showPaymentModal = true;
        },

        get isCreditSale() {
            return this.paymentMethod === 'credit';
        },

        get dueAmount() {
            return Math.max(0, this.cartTotal - this.paidAmount);
        },

        processPayment() {
            if (this.paymentMethod === 'credit') {
                if (!this.selectedCustomer) {
                    alert('Please select a customer for credit sales.');
                    return;
                }
                if (!this.dueDate) {
                    alert('Please set a due date for credit sales.');
                    return;
                }
                if (this.paidAmount >= this.cartTotal) {
                    alert('Credit sale requires payment less than total. Use Cash/Card for full payment.');
                    return;
                }
            } else {
                if (this.paidAmount < this.cartTotal) {
                    alert('Insufficient payment amount.');
                    return;
                }
            }

            this.processing = true;
            this.showPaymentModal = false;

            const timeout = setTimeout(() => {
                if (this.processing) {
                    this.processing = false;
                    alert('Sale processing timed out. Please try again.');
                }
            }, 30000);

            fetch('{{ route("admin.pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    branch_id: this.selectedBranch,
                    customer_id: this.selectedCustomer || null,
                    items: this.cart.map(item => ({
                        product_id: item.product_id,
                        product_variant_id: item.product_variant_id,
                        quantity: item.quantity,
                        unit_price: item.unit_price,
                        discount_amount: item.discount_amount,
                    })),
                    discount_amount: this.discountAmount,
                    tax_amount: 0,
                    shipping_amount: 0,
                    payment_method: this.paymentMethod,
                    paid_amount: this.paidAmount,
                    due_date: this.isCreditSale ? this.dueDate : null,
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().catch(() => { throw new Error('Server error (HTTP ' + response.status + ')'); }).then(err => { throw new Error(err.message || 'Server error'); });
                }
                return response.json();
            })
            .then(data => {
                clearTimeout(timeout);
                if (data.errors) {
                    alert('Validation error: ' + Object.values(data.errors).flat().join(', '));
                    this.processing = false;
                    return;
                }
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            })
            .catch(error => {
                clearTimeout(timeout);
                alert('Error processing sale: ' + error.message);
                this.processing = false;
            });
        }
    }
}
</script>
<div class="pos-layout" x-data="posApp()" x-init="init()">
    <!-- Left: Products -->
    <div class="pos-products p-3">
        <!-- Top Bar -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary" title="Dashboard">
                <i class="bi bi-speedometer2"></i>
            </a>
            <div class="flex-grow-1">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control form-control-lg" placeholder="Search products by name, SKU, or barcode..."
                           x-model="searchQuery" @input.debounce.300ms="searchProducts()">
                </div>
            </div>
            <div style="width: 200px;">
                <select class="form-select form-select-lg" x-model="selectedCategory" @change="searchProducts()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 200px;">
                <select class="form-select form-select-lg" x-model="selectedBranch" @change="searchProducts()">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="row g-2" style="max-height: calc(100vh - 140px); overflow-y: auto;">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card pos-product-card h-100" @click="addToCart(product)">
                        <div class="card-body p-2 text-center d-flex flex-column">
                            <div class="mb-2" style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-phone" style="font-size: 2.5rem; color: #4f46e5;" x-show="!product.image_url"></i>
                                <img :src="product.image_url" x-show="product.image_url" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                            </div>
                            <h6 class="card-title mb-1 small fw-semibold" style="line-height: 1.2; min-height: 2.4em; overflow: hidden;" x-text="product.name"></h6>
                            <small class="text-muted" x-text="product.sku"></small>
                            <div class="mt-auto pt-1">
                                <span class="badge fs-6" style="background: #4f46e5; color: #fff;" x-text="'Rs. ' + parseFloat(product.selling_price).toFixed(2)"></span>
                                <div class="mt-1">
                                    <small :class="product.available_quantity > 0 ? 'text-success' : 'text-danger'"
                                           x-text="'Stock: ' + product.available_quantity"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="pos-cart" :class="{ 'show': showCart }">
        <div class="p-3 border-bottom pos-cart-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Cart</h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: 150px;" x-model="selectedCustomer">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-outline-danger" @click="clearCart()" x-show="cart.length > 0">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-grow-1 overflow-auto p-2" style="max-height: calc(100vh - 320px);">
            <div x-show="cart.length === 0" class="text-center text-muted py-5">
                <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                <p class="mt-2">Cart is empty</p>
                <small>Click products to add them</small>
            </div>

            <template x-for="(item, index) in cart" :key="item.product_id + '-' + index">
                <div class="pos-cart-item p-2 d-flex align-items-center gap-2">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small" x-text="item.product_name"></div>
                        <small class="text-muted" x-text="'Rs. ' + parseFloat(item.unit_price).toFixed(2) + ' each'"></small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary" style="width: 28px; height: 28px;"
                                @click="updateQuantity(index, -1)">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" class="form-control form-control-sm text-center" style="width: 50px;"
                               :value="item.quantity" @change="setQuantity(index, $event.target.value)">
                        <button class="btn btn-sm btn-outline-secondary" style="width: 28px; height: 28px;"
                                @click="updateQuantity(index, 1)">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="text-end" style="width: 80px;">
                        <div class="fw-bold" x-text="'Rs. ' + (item.unit_price * item.quantity).toFixed(2)"></div>
                        <button class="btn btn-sm text-danger p-0" @click="removeFromCart(index)">
                            <i class="bi bi-x-lg"></i> Remove
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Cart Summary -->
        <div class="border-top p-3 pos-cart-summary">
            <div class="d-flex justify-content-between mb-1">
                <span>Subtotal (<span x-text="cart.reduce((sum, item) => sum + item.quantity, 0)"></span> items)</span>
                <span x-text="'Rs. ' + cartSubtotal.toFixed(2)"></span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Discount</span>
                <div class="d-flex align-items-center gap-1">
                    <span>Rs. </span>
                    <input type="number" class="form-control form-control-sm text-end" style="width: 80px;"
                           x-model.number="discountAmount" min="0" step="0.01">
                </div>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                <span>Total</span>
                <span class="text-primary" x-text="'Rs. ' + cartTotal.toFixed(2)"></span>
            </div>

            <button class="btn btn-primary btn-lg w-100" @click="openPaymentModal()"
                    :disabled="cart.length === 0">
                <i class="bi bi-credit-card me-2"></i>Pay Now
            </button>
        </div>
    </div>

    <!-- Mobile Cart Toggle -->
    <button class="btn btn-primary position-fixed bottom-0 end-0 m-3 d-lg-none"
            style="border-radius: 50%; width: 60px; height: 60px; z-index: 1040;"
            @click="showCart = !showCart" x-show="cart.length > 0">
        <i class="bi bi-cart3"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              x-text="cart.reduce((sum, item) => sum + item.quantity, 0)"></span>
    </button>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;"
         @click.self="showPaymentModal = false">
        <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
        <div class="modal-content" style="max-width: 800px; width: 90%; border-radius: 0.75rem; box-shadow: 0 20px 60px rgba(0,0,0,0.3);"
             @click.stop>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>Payment</h5>
                    <button type="button" class="btn-close btn-close-white" @click="showPaymentModal = false"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Payment Methods -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Payment Method</h6>
                            <div class="d-grid gap-2">
                                <template x-for="method in paymentMethods" :key="method.value">
                                    <button class="btn text-start p-3"
                                            :class="paymentMethod === method.value ? 'btn-primary' : 'btn-outline-primary'"
                                            @click="paymentMethod = method.value">
                                        <i :class="method.icon" class="me-2"></i>
                                        <span x-text="method.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Amount:</span>
                                        <span class="fw-bold fs-5" x-text="'Rs. ' + cartTotal.toFixed(2)"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Discount:</span>
                                        <span x-text="'-Rs. ' + discountAmount.toFixed(2)"></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Amount Due:</span>
                                        <span class="text-danger" x-text="'Rs. ' + cartTotal.toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Amount Paid</label>
                                <input type="number" class="form-control form-control-lg" x-model.number="paidAmount"
                                       min="0" step="0.01"
                                       :placeholder="isCreditSale ? 'Partial payment' : 'Min: Rs. ' + cartTotal.toFixed(2)"
                                       :max="isCreditSale ? cartTotal : undefined">
                            </div>

                            <!-- Credit Sale: Due Amount + Due Date -->
                            <div x-show="isCreditSale" class="mb-3">
                                <div class="alert alert-warning d-flex align-items-center mb-2" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <span>Remaining balance will be charged to customer account</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold text-danger">Due Amount:</span>
                                    <span class="fw-bold text-danger" x-text="'Rs. ' + dueAmount.toFixed(2)"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Due Date</label>
                                    <input type="date" class="form-control" x-model="dueDate"
                                           :min="new Date().toISOString().split('T')[0]">
                                </div>
                            </div>

                            <div class="mb-3" x-show="!isCreditSale && paidAmount >= cartTotal">
                                <label class="form-label">Change</label>
                                <input type="text" class="form-control form-control-lg text-success fw-bold"
                                       :value="'Rs. ' + change.toFixed(2)" readonly>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                <button class="btn btn-outline-secondary" @click="paidAmount = cartTotal"
                                        x-text="'Exact: Rs. ' + cartTotal.toFixed(2)"></button>
                                <button class="btn btn-outline-secondary" @click="paidAmount = Math.ceil(cartTotal)"
                                        x-text="'Round: Rs. ' + Math.ceil(cartTotal).toFixed(2)"></button>
                                <template x-for="quick in [10, 20, 50, 100]" :key="quick">
                                    <button class="btn btn-outline-secondary" @click="paidAmount = quick"
                                            x-show="quick >= cartTotal"
                                            x-text="'Rs. ' + quick"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" @click="showPaymentModal = false">Cancel</button>
                    <button type="button" class="btn btn-primary btn-lg" @click="processPayment()"
                            :disabled="isCreditSale ? (!selectedCustomer || !dueDate || dueAmount <= 0) : (paidAmount < cartTotal)">
                        <i class="bi bi-check-circle me-2"></i>Complete Sale
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>


</div>

@endsection

@push('scripts')
<style>
@media print {
    .pos-layout > *:not(:last-child) { display: none !important; }
    .pos-cart { width: 100% !important; border: none !important; }
}
</style>
@endpush
