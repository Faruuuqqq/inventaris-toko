<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Header Alert Warning -->
<div class="mb-6 flex items-center gap-3 rounded-lg border border-warning/50 bg-warning/10 p-4">
    <?= icon('AlertCircle', 'h-5 w-5 text-warning') ?>
    <p class="text-sm text-warning">
        Penjualan kredit akan menambah piutang customer. Pastikan data customer sudah benar dan limit kredit mencukupi.
    </p>
</div>

<!-- Credit Sales Form - Split Layout -->
<div class="grid gap-6 lg:grid-cols-3" x-data="creditSalesForm()" x-init="initData()">
    
    <!-- LEFT: Form Section (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Header Card -->
        <div class="rounded-lg border bg-surface text-foreground shadow-sm">
            <div class="p-6 border-b border-border/50">
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <?= icon('CreditCard', 'h-6 w-6 text-primary') ?>
                    Penjualan Kredit (PK)
                </h2>
            </div>

            <!-- Header Fields -->
            <div class="p-6 space-y-6">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Invoice Number -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">No. Faktur</label>
                        <input type="text" value="Auto (PK-xxxx)" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground">
                    </div>

                    <!-- Date -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Tanggal</label>
                        <input type="date" value="<?= date('Y-m-d') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>

                    <!-- Customer -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Customer *</label>
                        <select x-model="selectedCustomer" @change="onCustomerChange()" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <option value="">Pilih customer</option>
                            <template x-for="c in customers" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Due Date -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Jatuh Tempo *</label>
                        <input type="date" :value="defaultDueDate" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>
                </div>

                <!-- Second Row -->
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Salesperson -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Salesperson</label>
                        <select class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <option value="">Pilih salesperson</option>
                            <template x-for="s in salespersons" :key="s.id">
                                <option :value="s.id" x-text="s.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Warehouse -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Gudang *</label>
                        <select class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <option value="">Pilih gudang</option>
                            <template x-for="w in warehouses" :key="w.id">
                                <option :value="w.id" x-text="w.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Info Box -->
        <div x-show="selectedCustomer" class="rounded-lg border border-primary/50 bg-primary/5 p-4 space-y-3">
            <h4 class="font-semibold flex items-center gap-2 text-foreground">
                <?= icon('Wallet', 'h-5 w-5 text-primary') ?>
                Informasi Kredit Customer
            </h4>
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div class="rounded-lg bg-surface border border-border/50 p-3">
                    <p class="text-xs text-muted-foreground mb-1">Limit Kredit</p>
                    <p class="text-lg font-bold text-primary" x-text="'Rp ' + formatNumber(customerCreditLimit)"></p>
                </div>
                <div class="rounded-lg bg-surface border border-border/50 p-3">
                    <p class="text-xs text-muted-foreground mb-1">Piutang Saat Ini</p>
                    <p class="text-lg font-bold text-warning" x-text="'Rp ' + formatNumber(customerReceivable)"></p>
                </div>
                <div class="rounded-lg bg-surface border border-border/50 p-3">
                    <p class="text-xs text-muted-foreground mb-1">Sisa Limit</p>
                    <p class="text-lg font-bold" :class="customerRemainingLimit >= 0 ? 'text-success' : 'text-destructive'" x-text="'Rp ' + formatNumber(customerRemainingLimit)"></p>
                </div>
            </div>
        </div>

        <!-- Add Product Section -->
        <div class="rounded-lg border bg-surface text-foreground shadow-sm p-6 space-y-4">
            <h3 class="text-lg font-semibold">Tambah Produk</h3>
            <div class="grid gap-4 md:grid-cols-5">
                <div class="md:col-span-2">
                    <select x-model="tempItem.product_id" @change="onProductChange()" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih produk</option>
                        <template x-for="p in products" :key="p.id">
                            <option :value="p.id" x-text="p.name + ' - Rp ' + formatNumber(p.price_sell)"></option>
                        </template>
                    </select>
                </div>
                <input type="number" x-model.number="tempItem.quantity" placeholder="Qty" min="1" class="h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <input type="number" x-model.number="tempItem.discount" placeholder="Diskon" min="0" class="h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <button @click="addItem()" type="button" class="h-10 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition flex items-center justify-center gap-2">
                    <?= icon('Plus', 'h-4 w-4') ?>
                    <span class="hidden sm:inline">Tambah</span>
                </button>
            </div>
        </div>

        <!-- Items List -->
        <div class="rounded-lg border bg-surface text-foreground shadow-sm overflow-hidden">
            <div class="relative w-full overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-4 text-left font-medium text-muted-foreground">No</th>
                            <th class="h-12 px-4 text-left font-medium text-muted-foreground">Produk</th>
                            <th class="h-12 px-4 text-right font-medium text-muted-foreground">Qty</th>
                            <th class="h-12 px-4 text-right font-medium text-muted-foreground">Harga</th>
                            <th class="h-12 px-4 text-right font-medium text-muted-foreground">Diskon</th>
                            <th class="h-12 px-4 text-right font-medium text-muted-foreground">Subtotal</th>
                            <th class="h-12 px-4 text-center font-medium text-muted-foreground w-[50px]"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <template x-if="cart.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                    Belum ada produk ditambahkan
                                </td>
                            </tr>
                        </template>
                        <template x-for="(item, index) in cart" :key="index">
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-4 py-3" x-text="index + 1"></td>
                                <td class="px-4 py-3" x-text="item.name"></td>
                                <td class="px-4 py-3 text-right" x-text="item.quantity"></td>
                                <td class="px-4 py-3 text-right" x-text="'Rp ' + formatNumber(item.price)"></td>
                                <td class="px-4 py-3 text-right" x-text="'Rp ' + formatNumber(item.discount)"></td>
                                <td class="px-4 py-3 text-right font-semibold" x-text="'Rp ' + formatNumber((item.price * item.quantity) - item.discount)"></td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="removeItem(index)" type="button" class="text-destructive hover:text-destructive/80 transition">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notes -->
        <div class="rounded-lg border bg-surface text-foreground shadow-sm p-6">
            <label class="block text-sm font-medium mb-2">Catatan Tambahan</label>
            <textarea x-model="notes" placeholder="Masukkan catatan transaksi..." class="w-full h-24 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
        </div>
    </div>

    <!-- RIGHT: Summary Section (1/3) -->
    <div class="space-y-6">
        <div class="rounded-lg border bg-surface text-foreground shadow-sm sticky top-24">
            <div class="p-6 border-b border-border/50">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <?= icon('Calculator', 'h-5 w-5 text-primary') ?>
                    Ringkasan Kredit
                </h3>
            </div>

            <div class="p-6 space-y-4">
                <!-- Subtotal -->
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Subtotal</span>
                    <span class="font-medium" x-text="'Rp ' + formatNumber(subtotal())"></span>
                </div>

                <!-- Total Discount -->
                <div class="flex justify-between text-sm" x-show="totalDiscount() > 0">
                    <span class="text-muted-foreground">Diskon Total</span>
                    <span class="font-medium text-destructive" x-text="'- Rp ' + formatNumber(totalDiscount())"></span>
                </div>

                <!-- Grand Total -->
                <div class="border-t border-border/50 pt-4">
                    <div class="flex justify-between items-baseline">
                        <span class="font-semibold text-muted-foreground">Total Piutang</span>
                        <span class="text-2xl font-bold text-warning" x-text="'Rp ' + formatNumber(grandTotal())"></span>
                    </div>
                </div>

                <!-- Down Payment -->
                <div class="space-y-2 pt-2">
                    <label class="text-sm font-medium">Uang Muka (DP)</label>
                    <input type="number" x-model.number="downPayment" @input="updateSummary()" placeholder="0" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-right text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <!-- Remaining Debt -->
                <div class="rounded-lg bg-destructive/10 border border-destructive/30 p-3">
                    <p class="text-xs text-muted-foreground mb-1">Sisa Piutang</p>
                    <p class="text-2xl font-bold text-destructive" x-text="'Rp ' + formatNumber(Math.max(0, grandTotal() - downPayment))"></p>
                </div>

                <!-- Credit Limit Warning -->
                <div x-show="creditLimitExceeded()" class="rounded-lg bg-destructive/10 border border-destructive/30 p-3 flex items-start gap-2">
                    <?= icon('AlertCircle', 'h-5 w-5 text-destructive flex-shrink-0 mt-0.5') ?>
                    <p class="text-xs font-medium text-destructive">Total melebihi limit kredit customer!</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-4">
                    <a href="<?= base_url('transactions/sales') ?>" class="flex-1 h-10 border border-border/50 rounded-lg font-medium text-foreground hover:bg-muted transition flex items-center justify-center">
                        Batal
                    </a>
                    <button @click="submitForm()" :disabled="!isFormValid()" type="button" class="flex-1 h-10 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition">
                        Simpan
                    </button>
                </div>

                <button type="button" class="w-full h-10 border border-border/50 rounded-lg font-medium text-foreground hover:bg-muted transition flex items-center justify-center gap-2">
                    <?= icon('Printer', 'h-4 w-4') ?>
                    Cetak Faktur
                </button>
            </div>
        </div>
    </div>
</div>

<form id="credit-sales-form" action="<?= base_url('transactions/sales/storeCredit') ?>" method="POST" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="items" :value="JSON.stringify(cart)">
    <input type="hidden" name="down_payment" x-model="downPayment">
    <input type="hidden" name="notes" x-model="notes">
</form>

<script>
    function creditSalesForm() {
        return {
            // Data
            customers: [],
            products: [],
            salespersons: [],
            warehouses: [],
            cart: [],

            // Form State
            selectedCustomer: '',
            downPayment: 0,
            notes: '',
            tempItem: {
                product_id: '',
                quantity: 1,
                discount: 0,
                name: '',
                price: 0
            },

            // Customer Data
            customerCreditLimit: 0,
            customerReceivable: 0,
            get customerRemainingLimit() {
                return this.customerCreditLimit - this.customerReceivable;
            },

            // Default due date (30 days from now)
            get defaultDueDate() {
                const date = new Date();
                date.setDate(date.getDate() + 30);
                return date.toISOString().split('T')[0];
            },

            // Initialize
            async initData() {
                try {
                    const [customersRes, productsRes, salespersonsRes, warehousesRes] = await Promise.all([
                        fetch('<?= base_url('master/customers/getList') ?>'),
                        fetch('<?= base_url('transactions/sales/getProducts') ?>'),
                        fetch('<?= base_url('master/salespersons/getList') ?>'),
                        fetch('<?= base_url('master/warehouses/getList') ?>')
                    ]);

                    this.customers = await customersRes.json();
                    this.products = await productsRes.json();
                    this.salespersons = await salespersonsRes.json();
                    this.warehouses = await warehousesRes.json();
                } catch (e) {
                    console.error("Failed to load data", e);
                }
            },

            // Event Handlers
            onCustomerChange() {
                const customer = this.customers.find(c => c.id == this.selectedCustomer);
                if (customer) {
                    this.customerCreditLimit = parseFloat(customer.credit_limit) || 0;
                    this.customerReceivable = parseFloat(customer.receivable_balance) || 0;
                } else {
                    this.customerCreditLimit = 0;
                    this.customerReceivable = 0;
                }
            },

            onProductChange() {
                const product = this.products.find(p => p.id == this.tempItem.product_id);
                if (product) {
                    this.tempItem.name = product.name;
                    this.tempItem.price = parseFloat(product.price_sell);
                }
            },

            // Cart Management
            addItem() {
                if (!this.tempItem.product_id || this.tempItem.quantity <= 0) {
                    alert('Pilih produk dan masukkan quantity');
                    return;
                }

                this.cart.push({
                    product_id: this.tempItem.product_id,
                    name: this.tempItem.name,
                    price: this.tempItem.price,
                    quantity: this.tempItem.quantity,
                    discount: this.tempItem.discount
                });

                // Reset
                this.tempItem = {
                    product_id: '',
                    quantity: 1,
                    discount: 0,
                    name: '',
                    price: 0
                };
            },

            removeItem(index) {
                this.cart.splice(index, 1);
            },

            // Calculations
            subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            totalDiscount() {
                return this.cart.reduce((sum, item) => sum + item.discount, 0);
            },

            grandTotal() {
                return this.subtotal() - this.totalDiscount();
            },

            creditLimitExceeded() {
                if (!this.selectedCustomer) return false;
                return this.grandTotal() > this.customerRemainingLimit;
            },

            isFormValid() {
                return this.selectedCustomer && this.cart.length > 0 && !this.creditLimitExceeded();
            },

            // Update Summary
            updateSummary() {
                // Recalculate everything
                this.$nextTick(() => {
                    // Alpine will handle reactivity
                });
            },

            // Submit
            submitForm() {
                if (!this.isFormValid()) {
                    alert('Periksa kembali data form Anda');
                    return;
                }

                document.getElementById('credit-sales-form').submit();
            },

            // Utilities
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            }
        }
    }
</script>

<?= $this->endSection() ?>
