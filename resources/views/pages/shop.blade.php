@extends('layouts.app')

@section('title', 'Shop - PCH Winners Portal')

@section('content')
    @include('components.nav')
    <main class="flex-1 bg-gray-50"
          x-data="{
            cartOpen: false,
            checkoutOpen: false,
            checkoutStep: 'form',
            checkoutError: '',
            placeOrdering: false,
            products: {{ json_encode($products) }},
            paymentMethods: {{ json_encode($paymentMethods) }},
            selectedPayment: '',
            get cart() { return $store.cart; },
            get cartItems() { return $store.cart.items; },
            addToCart(id) {
                const product = this.products.find(p => p.id === id);
                if (!product) return;
                this.cart.add(product);
                const btn = document.getElementById('add-btn-' + id);
                if (btn) {
                    btn.textContent = 'Added';
                    btn.classList.remove('bg-[#1B2A4A]', 'hover:bg-[#243B6A]');
                    btn.classList.add('bg-green-600');
                    setTimeout(() => {
                        btn.textContent = 'Add to Cart';
                        btn.classList.remove('bg-green-600');
                        btn.classList.add('bg-[#1B2A4A]', 'hover:bg-[#243B6A]');
                    }, 1500);
                }
            },
            openCheckout() {
                if (this.cart.isEmpty) return;
                this.checkoutStep = 'form';
                this.checkoutError = '';
                this.selectedPayment = '';
                this.checkoutOpen = true;
            },
            async placeOrder(e) {
                e.preventDefault();
                if (this.placeOrdering) return;
                this.placeOrdering = true;

                const form = e.target;
                const items = this.cartItems.map(i => ({
                    product_id: i.product.id,
                    name: i.product.name,
                    price: i.product.price,
                    quantity: i.quantity,
                }));

                form.querySelector('[name=items]').value = JSON.stringify(items);

                try {
                    const data = new FormData(form);
                    const res = await fetch('{{ route("shop.order") }}', {
                        method: 'POST',
                        body: data,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.cart.clear();
                        this.checkoutStep = 'success';
                    } else {
                        this.checkoutError = json.message || 'Order could not be processed.';
                        this.checkoutStep = 'error';
                    }
                } catch {
                    this.checkoutError = 'A network error occurred. Please try again.';
                    this.checkoutStep = 'error';
                } finally {
                    this.placeOrdering = false;
                }
            },
            closeCheckout() {
                this.checkoutOpen = false;
                this.checkoutStep = 'form';
            },
            // Filtering
            currentCategory: 'All',
            searchQuery: '',
            get filteredProducts() {
                return this.products.filter(p => {
                    const matchSearch = this.searchQuery === '' ||
                        p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        (p.description || '').toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchCategory = this.currentCategory === 'All' || p.category === this.currentCategory;
                    return matchSearch && matchCategory;
                });
            },
            setCategory(cat) {
                this.currentCategory = cat;
            }
          }">
        <div class="bg-gradient-to-r from-[#1B2A4A] to-[#243B6A] text-white">
            <div class="max-w-7xl mx-auto px-4 py-12">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold">PCH Shop</h1>
                        <p class="text-white/60 mt-1">Exclusive merchandise for winners and fans</p>
                    </div>
                    <button @click="cartOpen = !cartOpen"
                            class="relative bg-white/10 hover:bg-white/20 transition-colors px-4 py-2.5 rounded-xl text-sm font-medium">
                        Cart
                        <span x-show="cart.count > 0"
                              x-text="cart.count"
                              x-cloak
                              class="absolute -top-2 -right-2 bg-[#D4AF37] text-[#1B2A4A] text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="md:w-56 shrink-0">
                    <input type="text" x-model="searchQuery" placeholder="Search products..."
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 outline-none mb-4">
                    <div class="flex md:flex-col gap-2 flex-wrap">
                        <template x-for="cat in ['All', 'Apparel', 'Accessories', 'Lifestyle', 'Games']" :key="cat">
                            <button @click="setCategory(cat)"
                                    :class="currentCategory === cat ? 'bg-[#1B2A4A] text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
                                <span x-text="cat"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <div class="flex-1">
                    <div x-show="filteredProducts.length > 0"
                         class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <div class="product-card bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all">
                                <div class="aspect-square bg-gray-100 flex items-center justify-center text-4xl overflow-hidden">
                                    <template x-if="product.image && (product.image.startsWith('/') || product.image.startsWith('http'))">
                                        <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!product.image || (!product.image.startsWith('/storage/') && !product.image.startsWith('http'))">
                                        <span x-text="product.image || '📦'"></span>
                                    </template>
                                </div>
                                <div class="p-4">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1" x-text="product.category"></p>
                                    <h3 class="font-semibold text-[#1B2A4A] text-sm leading-tight mb-1 truncate" x-text="product.name"></h3>
                                    <p class="text-xs text-gray-400 mb-3 line-clamp-2" x-text="product.description"></p>
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-[#1B2A4A] text-lg" x-text="'$' + product.price.toFixed(2)"></span>
                                        <button @click="addToCart(product.id)"
                                                :id="'add-btn-' + product.id"
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-[#1B2A4A] text-white hover:bg-[#243B6A]">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="filteredProducts.length === 0"
                         class="text-center py-20">
                        <p class="text-5xl mb-4">🔍</p>
                        <p class="text-gray-500 text-lg">No products found</p>
                        <button @click="searchQuery = ''; currentCategory = 'All'" class="text-[#D4AF37] text-sm mt-2 underline">Clear filters</button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="cartOpen" x-cloak
             @click.away="cartOpen = false"
             class="fixed inset-0 z-50 flex justify-end">
            <div class="absolute inset-0 bg-black/40" @click="cartOpen = false"></div>
            <div class="relative w-full max-w-md bg-white h-full shadow-2xl flex flex-col">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h2 class="font-bold text-lg text-[#1B2A4A]">Your Cart</h2>
                    <button @click="cartOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <template x-for="(item, idx) in cartItems" :key="idx">
                        <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3 mb-3">
                            <div class="w-14 h-14 bg-white rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center text-2xl">
                                <template x-if="item.product.image && (item.product.image.startsWith('/') || item.product.image.startsWith('http'))">
                                    <img :src="item.product.image" :alt="item.product.name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.product.image || (!item.product.image.startsWith('/storage/') && !item.product.image.startsWith('http'))">
                                    <span x-text="item.product.image || '📦'"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#1B2A4A] truncate" x-text="item.product.name"></p>
                                <p class="text-xs text-gray-400" x-text="'$' + item.product.price.toFixed(2) + ' each'"></p>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <button @click="cart.updateQty(idx, 1)" class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 text-gray-600 hover:bg-gray-300 text-xs">+</button>
                                <span class="text-sm font-medium min-w-[1.5rem] text-center" x-text="item.quantity"></span>
                                <button @click="cart.updateQty(idx, -1)" class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 text-gray-600 hover:bg-gray-300 text-xs">-</button>
                            </div>
                            <button @click="cart.remove(idx)" class="text-gray-400 hover:text-red-500 text-sm p-1">✕</button>
                        </div>
                    </template>
                    <div x-show="cart.isEmpty" class="text-center py-12">
                        <p class="text-5xl mb-4">🛒</p>
                        <p class="text-gray-500">Your cart is empty</p>
                        <button @click="cartOpen = false" class="mt-4 text-[#D4AF37] text-sm underline">Browse products</button>
                    </div>
                </div>
                <div x-show="!cart.isEmpty" class="border-t border-gray-200 p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total</span>
                        <span class="font-bold text-[#1B2A4A]" x-text="'$' + cart.total.toFixed(2)"></span>
                    </div>
                    <button @click="cartOpen = false; openCheckout()"
                            class="w-full py-3 bg-gradient-to-r from-[#D4AF37] to-[#C5A55A] text-[#1B2A4A] font-bold rounded-lg text-sm hover:from-[#C5A55A] hover:to-[#B8963E] transition-all">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>

        <div x-show="checkoutOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" @click="closeCheckout()"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 max-h-[90vh] overflow-y-auto">
                <button @click="closeCheckout()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div x-show="checkoutStep === 'form'">
                    <h2 class="font-bold text-lg text-[#1B2A4A] mb-4">Checkout</h2>
                        <form @submit.prevent="placeOrder($event)" class="space-y-3" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="items">
                        <div>
                            <label class="text-xs text-gray-500 font-medium block mb-1">Full Name</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium block mb-1">Email</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium block mb-1">Address</label>
                            <input type="text" name="address" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none">
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs text-gray-500 font-medium block mb-1">City</label>
                                <input type="text" name="city" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-medium block mb-1">State</label>
                                <input type="text" name="state" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-medium block mb-1">ZIP</label>
                                <input type="text" name="zip" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium block mb-1">Payment Method</label>
                            <select name="payment_method" required x-model="selectedPayment"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#D4AF37] outline-none">
                                <option value="">Select a payment method</option>
                                <template x-for="pm in paymentMethods" :key="pm.slug">
                                    <option :value="pm.slug" x-text="pm.name"></option>
                                </template>
                            </select>
                            <template x-if="selectedPayment && paymentMethods.find(pm => pm.slug === selectedPayment)?.instructions">
                                <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-600"
                                     x-html="paymentMethods.find(pm => pm.slug === selectedPayment)?.instructions">
                                </div>
                            </template>
                        </div>
                        <div x-show="selectedPayment">
                            <label class="text-xs text-gray-500 font-medium block mb-1">Payment Proof (optional)</label>
                            <input type="file" name="payment_proof" accept="image/*,application/pdf"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#1B2A4A] file:text-white hover:file:bg-[#243B6A]">
                        </div>
                        <div class="pt-2">
                            <p class="text-xs text-gray-400 mb-3">
                                Total: <span class="font-bold text-[#1B2A4A]" x-text="'$' + cart.total.toFixed(2)"></span>
                            </p>
                            <button type="submit" :disabled="placeOrdering"
                                    class="w-full py-3 bg-gradient-to-r from-[#D4AF37] to-[#C5A55A] text-[#1B2A4A] font-bold rounded-lg text-sm hover:from-[#C5A55A] hover:to-[#B8963E] transition-all disabled:opacity-50">
                                <span x-show="!placeOrdering">Place Order</span>
                                <span x-show="placeOrdering">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="checkoutStep === 'success'" class="text-center py-8">
                    <p class="text-6xl mb-4">🎉</p>
                    <h3 class="text-xl font-bold text-[#1B2A4A] mb-2">Order Confirmed!</h3>
                    <p class="text-gray-500 text-sm">Your order has been placed. You'll receive a confirmation email shortly.</p>
                    <button @click="closeCheckout()" class="mt-6 px-6 py-2.5 bg-[#1B2A4A] text-white rounded-lg text-sm font-medium hover:bg-[#243B6A]">Continue Shopping</button>
                </div>

                <div x-show="checkoutStep === 'error'" class="text-center py-8">
                    <p class="text-5xl mb-4">⚠️</p>
                    <p class="text-red-500 text-sm mb-4" x-text="checkoutError"></p>
                    <button @click="checkoutStep = 'form'" class="px-6 py-2.5 bg-[#1B2A4A] text-white rounded-lg text-sm font-medium hover:bg-[#243B6A]">Try Again</button>
                </div>
            </div>
        </div>
    </main>
    @include('components.footer')
@endsection
