@extends('layouts.pos')

@section('content')
<div class="pos-layout" x-data="posApp()" x-init="init()">
    <!-- Left: Products -->
    <div class="pos-products p-3">
        <!-- Top Bar -->
        <div class="d-flex align-items-center gap-3 mb-3">
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
                                <i class="bi bi-phone text-primary" style="font-size: 2.5rem;" x-show="!product.image_url"></i>
                                <img :src="product.image_url" x-show="product.image_url" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                            </div>
                            <h6 class="card-title mb-1 small fw-semibold" x-text="product.name" style="line-height: 1.2; min-height: 2.4em; overflow: hidden;"></h6>
                            <small class="text-muted" x-text="product.sku"></small>
                            <div class="mt-auto pt-1">
                                <span class="badge bg-primary fs-6" x-text="'$' + parseFloat(product.selling_price).toFixed(2)"></span>
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
        <div class="p-3 border-bottom bg-light">
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
                        <small class="text-muted" x-text="'$' + parseFloat(item.unit_price).toFixed(2) + ' each'"></small>
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
                        <div class="fw-bold" x-text="'$' + (item.unit_price * item.quantity).toFixed(2)"></div>
                        <button class="btn btn-sm text-danger p-0" @click="removeFromCart(index)">
                            <i class="bi bi-x-lg"></i> Remove
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Cart Summary -->
        <div class="border-top p-3 bg-light">
            <div class="d-flex justify-content-between mb-1">
                <span>Subtotal (<span x-text="cart.reduce((sum, item) => sum + item.quantity, 0)"></span> items)</span>
                <span x-text="'$' + cartSubtotal.toFixed(2)"></span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Discount</span>
                <div class="d-flex align-items-center gap-1">
                    <span>$</span>
                    <input type="number" class="form-control form-control-sm text-end" style="width: 80px;"
                           x-model.number="discountAmount" min="0" step="0.01">
                </div>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                <span>Total</span>
                <span class="text-primary" x-text="'$' + cartTotal.toFixed(2)"></span>
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
    <div class="modal fade" id="paymentModal" tabindex="-1" x-show="showPaymentModal"
         x-transition:enter="modal fade" x-transition:leave="modal fade">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
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
                                        <span class="fw-bold fs-5" x-text="'$' + cartTotal.toFixed(2)"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Discount:</span>
                                        <span x-text="'-$' + discountAmount.toFixed(2)"></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Amount Due:</span>
                                        <span class="text-danger" x-text="'$' + cartTotal.toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Amount Paid</label>
                                <input type="number" class="form-control form-control-lg" x-model.number="paidAmount"
                                       min="0" step="0.01" :placeholder="'Min: $' + cartTotal.toFixed(2)">
                            </div>

                            <div class="mb-3" x-show="paidAmount >= cartTotal">
                                <label class="form-label">Change</label>
                                <input type="text" class="form-control form-control-lg bg-success text-white"
                                       :value="'$' + change.toFixed(2)" readonly>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                <button class="btn btn-outline-secondary" @click="paidAmount = cartTotal"
                                        x-text="'Exact: $' + cartTotal.toFixed(2)"></button>
                                <button class="btn btn-outline-secondary" @click="paidAmount = Math.ceil(cartTotal)"
                                        x-text="'Round: $' + Math.ceil(cartTotal).toFixed(2)"></button>
                                <template x-for="quick in [10, 20, 50, 100]" :key="quick">
                                    <button class="btn btn-outline-secondary" @click="paidAmount = quick"
                                            x-show="quick >= cartTotal"
                                            x-text="'$' + quick"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showPaymentModal = false">Cancel</button>
                    <button type="button" class="btn btn-success btn-lg" @click="processPayment()"
                            :disabled="paidAmount < cartTotal">
                        <i class="bi bi-check-circle me-2"></i>Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Overlay -->
    <div x-show="processing" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
         style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="text-center text-white">
            <div class="spinner-border mb-3" style="width: 3rem; height: 3rem;"></div>
            <h5>Processing Sale...</h5>
        </div>
    </div>
</div>

@endsection

@push('scripts')
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
        paymentMethods: [
            { value: 'cash', label: 'Cash', icon: 'bi bi-cash' },
            { value: 'card', label: 'Credit/Debit Card', icon: 'bi bi-credit-card' },
            { value: 'transfer', label: 'Bank Transfer', icon: 'bi bi-bank' },
            { value: 'e_wallet', label: 'E-Wallet', icon: 'bi bi-wallet2' },
        ],

        init() {
            this.filteredProducts = [...this.products];
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
            this.showPaymentModal = true;
        },

        processPayment() {
            if (this.paidAmount < this.cartTotal) {
                alert('Insufficient payment amount.');
                return;
            }

            this.processing = true;
            this.showPaymentModal = false;

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
                })
            })
            .then(response => response.json())
            .then(data => {
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
                alert('Error processing sale: ' + error.message);
                this.processing = false;
            });
        }
    }
}
</script>
<style>
@media print {
    .pos-layout > *:not(:last-child) { display: none !important; }
    .pos-cart { width: 100% !important; border: none !important; }
}
</style>
@endpush
