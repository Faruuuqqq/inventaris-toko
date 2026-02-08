<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- POS Split Screen Layout -->
<div class="h-screen flex flex-col" x-data="posManager()" x-init="initData()">
    
    <!-- Header Bar -->
    <div class="bg-surface border-b border-border/50 p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <?= icon('ShoppingCart', 'h-6 w-6 text-primary') ?>
            <h1 class="text-2xl font-bold text-foreground">Penjualan Tunai - POS</h1>
        </div>
        <div class="text-sm text-muted-foreground">
            Tanggal: <span x-text="new Date().toLocaleDateString('id-ID')"></span>
        </div>
    </div>

    <!-- Main Content Grid: Left (Products) + Right (Cart) -->
    <div class="flex-1 flex overflow-hidden gap-4 p-4">
        
        <!-- LEFT PANEL: Product Selection (65%) -->
        <div class="flex-[2] flex flex-col overflow-hidden bg-surface rounded-lg border border-border/50">
            
            <!-- Search Bar -->
            <div class="p-4 border-b border-border/50 space-y-3">
                <input type="text" 
                       x-model="search" 
                       placeholder="Cari produk... (Tekan F2)" 
                       @keydown.f2="$el.focus()" 
                       class="w-full h-12 rounded-lg border border-border bg-background px-4 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                
                <!-- Category Filter Pills -->
                <div class="flex flex-wrap gap-2">
                    <template x-for="cat in categories" :key="cat">
                        <button @click="selectedCategory = cat"
                                :class="selectedCategory === cat ? 'bg-primary text-white' : 'bg-muted text-foreground border border-border/50'"
                                class="px-4 py-2 rounded-full text-xs font-medium transition-all">
                            <span x-text="cat === 'all' ? 'Semua Produk' : cat"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Product Grid (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)" 
                             class="bg-background border border-border/50 rounded-lg p-3 cursor-pointer hover:shadow-lg hover:border-primary/50 transition-all group">
                            
                            <!-- Product Image Placeholder -->
                            <div class="bg-muted h-24 rounded-lg flex items-center justify-center mb-2 group-hover:bg-muted/80 transition">
                                <svg class="h-10 w-10 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 0v10l8 4"/>
                                </svg>
                            </div>
                            
                            <!-- Product Info -->
                            <h3 class="font-semibold text-xs text-foreground truncate" x-text="product.name"></h3>
                            <p class="text-lg font-bold text-primary mt-1" x-text="'Rp ' + formatNumber(product.price_sell)"></p>
                            
                            <!-- Stock Badge -->
                            <div class="flex items-center justify-between mt-2">
                                <span class="inline-flex text-xs px-2 py-1 rounded-full" 
                                      :class="product.stock > 0 ? 'bg-success/15 text-success' : 'bg-destructive/15 text-destructive'"
                                      x-text="product.stock + ' stok'"></span>
                                <span class="text-primary group-hover:scale-110 transition">
                                    <?= icon('Plus', 'h-5 w-5') ?>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State -->
                <template x-if="filteredProducts.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-muted-foreground">
                        <?= icon('Package', 'h-12 w-12 mb-3 opacity-50') ?>
                        <p class="text-sm">Tidak ada produk</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- RIGHT PANEL: Cart Summary (35%) -->
        <div class="flex-1 flex flex-col bg-surface rounded-lg border border-border/50 overflow-hidden">
            
            <!-- Cart Header -->
            <div class="p-4 border-b border-border/50 flex items-center justify-between">
                <h2 class="font-bold text-foreground flex items-center gap-2">
                    <?= icon('ShoppingBag', 'h-5 w-5') ?>
                    <span x-text="'Keranjang (' + cart.length + ')'"></span>
                </h2>
                <button @click="clearCart()" x-show="cart.length > 0" class="text-xs text-destructive hover:text-destructive/80 font-medium">
                    Hapus Semua
                </button>
            </div>

            <!-- Cart Items (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="bg-background border border-border/50 rounded-lg p-3 space-y-2">
                        <!-- Item Name & Remove -->
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-sm text-foreground" x-text="item.name"></h4>
                                <p class="text-xs text-muted-foreground" x-text="'Rp ' + formatNumber(item.price_sell)"></p>
                            </div>
                            <button @click="removeFromCart(index)" class="text-destructive hover:text-destructive/80 transition">
                                <?= icon('Trash2', 'h-4 w-4') ?>
                            </button>
                        </div>

                        <!-- Quantity Controls & Line Total -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 bg-muted rounded-lg">
                                <button @click="updateQty(index, item.qty - 1)" class="px-2 py-1 text-primary hover:bg-background transition">
                                    −
                                </button>
                                <span class="w-8 text-center text-sm font-medium" x-text="item.qty"></span>
                                <button @click="updateQty(index, item.qty + 1)" class="px-2 py-1 text-primary hover:bg-background transition">
                                    +
                                </button>
                            </div>
                            <span class="font-bold text-sm text-foreground" x-text="'Rp ' + formatNumber(item.price_sell * item.qty)"></span>
                        </div>
                    </div>
                </template>

                <!-- Empty Cart State -->
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-muted-foreground">
                        <?= icon('ShoppingCart', 'h-12 w-12 mb-3 opacity-50') ?>
                        <p class="text-sm">Keranjang kosong</p>
                        <p class="text-xs mt-1">Pilih produk untuk mulai</p>
                    </div>
                </template>
            </div>

            <!-- Cart Summary (Sticky Bottom) -->
            <div class="border-t border-border/50 bg-background p-4 space-y-3">
                
                <!-- Summary Lines -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Subtotal</span>
                        <span x-text="'Rp ' + formatNumber(subtotal())"></span>
                    </div>
                    <div class="flex justify-between text-sm" x-show="totalDiscount() > 0">
                        <span class="text-muted-foreground">Diskon</span>
                        <span class="text-destructive" x-text="'- Rp ' + formatNumber(totalDiscount())"></span>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="border-t border-border/50 pt-3">
                    <div class="flex justify-between items-baseline">
                        <span class="font-semibold text-muted-foreground">Total Bayar</span>
                        <span class="text-3xl font-bold text-primary" x-text="'Rp ' + formatNumber(grandTotal())"></span>
                    </div>
                </div>

                <!-- Payment Input -->
                <div class="space-y-2">
                    <label class="text-xs font-medium text-muted-foreground">Bayar</label>
                    <input type="number" 
                           x-model.number="payAmount" 
                           @keydown.enter="checkout()"
                           placeholder="0" 
                           class="w-full h-12 rounded-lg border border-border bg-surface px-4 text-right text-lg font-bold text-primary focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <!-- Change Display -->
                <div class="bg-success/15 rounded-lg p-3 text-center" x-show="payAmount > 0">
                    <p class="text-xs text-muted-foreground mb-1">Kembalian</p>
                    <p class="text-2xl font-bold text-success" x-text="'Rp ' + formatNumber(changeAmount())"></p>
                </div>

                <!-- Action Buttons -->
                <form id="sales-form" action="<?= base_url('transactions/sales/storeCash') ?>" method="POST" @submit.prevent="submitForm()" class="space-y-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="items" :value="JSON.stringify(cartItems())">
                    <input type="hidden" name="customer_id" value="">
                    <input type="hidden" name="warehouse_id" value="">
                    <input type="hidden" name="payment_amount" :value="payAmount">

                    <button type="button" @click="clearCart()" x-show="cart.length > 0" class="w-full h-10 border border-border/50 rounded-lg text-sm font-medium text-foreground hover:bg-muted transition">
                        Batal
                    </button>

                    <button type="submit" :disabled="cart.length === 0" class="w-full h-12 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2">
                        <?= icon('Check', 'h-5 w-5') ?>
                        <span x-show="payAmount >= grandTotal()">Bayar Sekarang</span>
                        <span x-show="payAmount < grandTotal()">Kurang Bayar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function posManager() {
        return {
            // State
            products: [],
            cart: [],
            search: '',
            selectedCategory: 'all',
            payAmount: 0,
            categories: ['all'],

            // Initialize Data
            async initData() {
                try {
                    const res = await fetch('<?= base_url('transactions/sales/getProducts') ?>');
                    this.products = await res.json();
                    
                    // Extract unique categories
                    const cats = new Set(['all']);
                    this.products.forEach(p => {
                        if (p.category) cats.add(p.category);
                    });
                    this.categories = Array.from(cats);
                } catch (e) {
                    console.error("Failed to load products", e);
                }

                // Focus search on load for quick access
                setTimeout(() => {
                    document.querySelector('input[placeholder*="Cari"]')?.focus();
                }, 100);
            },

            // Computed Properties
            get filteredProducts() {
                return this.products.filter(p => {
                    const matchSearch = this.search === '' || 
                        p.name.toLowerCase().includes(this.search.toLowerCase());
                    const matchCategory = this.selectedCategory === 'all' || 
                        (p.category && p.category === this.selectedCategory);
                    return matchSearch && matchCategory;
                });
            },

            // Cart Management
            addToCart(product) {
                const existing = this.cart.find(item => item.id === product.id);
                if (existing) {
                    existing.qty++;
                } else {
                    this.cart.push({
                        ...product,
                        qty: 1
                    });
                }
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
            },

            updateQty(index, newQty) {
                if (newQty <= 0) {
                    this.removeFromCart(index);
                } else {
                    this.cart[index].qty = newQty;
                }
            },

            clearCart() {
                if (confirm('Yakin ingin mengosongkan keranjang?')) {
                    this.cart = [];
                    this.payAmount = 0;
                    this.search = '';
                }
            },

            // Calculations
            subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price_sell * item.qty), 0);
            },

            totalDiscount() {
                return 0; // Can be extended later
            },

            grandTotal() {
                return this.subtotal() - this.totalDiscount();
            },

            changeAmount() {
                return Math.max(0, this.payAmount - this.grandTotal());
            },

            // Submit
            cartItems() {
                return this.cart.map(item => ({
                    product_id: item.id,
                    name: item.name,
                    price: item.price_sell,
                    quantity: item.qty,
                    discount: 0
                }));
            },

            submitForm() {
                if (this.cart.length === 0) {
                    alert('Keranjang masih kosong!');
                    return;
                }
                
                // You can add additional validation here
                document.getElementById('sales-form').submit();
            },

            // Utilities
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }
        }
    }
</script>

<script>
    function salesForm() {
        return {
            items: [],
            products: [],
            payAmount: 0,
            tempItem: {
                product_id: '',
                name: '',
                price: 0,
                quantity: 1,
                discount: 0
            },
            
            async initData() {
                try {
                    const res = await fetch('<?= base_url('transactions/sales/getProducts') ?>');
                    this.products = await res.json();
                } catch (e) {
                    console.error("Failed to load products", e);
                }
            },

            fillPrice() {
                const product = this.products.find(p => p.id == this.tempItem.product_id);
                if (product) {
                    this.tempItem.name = product.name;
                    this.tempItem.price = parseFloat(product.price_sell);
                }
            },

            addItem() {
                if (!this.tempItem.product_id) {
                    alert('Pilih produk terlebih dahulu!');
                    return;
                }
                
                // Add item to list
                this.items.push({ ...this.tempItem });
                
                // Reset form fields
                this.tempItem.product_id = '';
                this.tempItem.quantity = 1;
                this.tempItem.price = 0;
                this.tempItem.discount = 0;
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },
            
            resetForm() {
                if(confirm('Yakin ingin mereset transaksi?')) {
                    this.items = [];
                    this.payAmount = 0;
                }
            },

            itemSubtotal(item) {
                return (item.price * item.quantity) - item.discount;
            },

            totalDiscount() {
                return this.items.reduce((acc, item) => acc + parseFloat(item.discount), 0);
            },

            grandTotalWithoutDiscount() {
                 return this.items.reduce((acc, item) => acc + parseFloat(item.price * item.quantity), 0);
            },

            grandTotal() {
                return this.items.reduce((acc, item) => {
                    return acc + ((item.price * item.quantity) - item.discount);
                }, 0);
            },
            
            changeAmount() {
                const total = this.grandTotal();
                if (this.payAmount <= 0) return 0;
                return Math.max(0, this.payAmount - total);
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            },
            
            formatRupiahSimple(number) {
                 return new Intl.NumberFormat('id-ID').format(number);
            },

            submitForm(e) {
                if (this.items.length === 0) {
                    alert('Keranjang masih kosong!');
                    return;
                }
                if (this.payAmount < this.grandTotal()) {
                    alert('Peringatan: Jumlah bayar kurang dari total belanja! (Hanya Peringatan)');
                    // allow submit for now or return
                }
                e.target.submit();
            }
        }
    }
</script>
<?= $this->endSection() ?>