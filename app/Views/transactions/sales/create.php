<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('ArrowRightFromLine', 'h-8 w-8 text-primary') ?>
            Buat Penjualan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Catat penjualan barang ke pelanggan</p>
    </div>
    <a href="<?= base_url('transactions/sales') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Sales Form -->
<form method="post" action="<?= base_url('transactions/sales/store') ?>" x-data="salesForm()" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Header Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Informasi Penjualan
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Invoice Number, Date, Payment Type -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. Invoice</label>
                    <input type="text" value="<?= $invoice_number ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tanggal Penjualan *</label>
                    <input type="date" name="tanggal_penjualan" value="<?= old('tanggal_penjualan', date('Y-m-d')) ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tipe Pembayaran *</label>
                    <select name="tipe_pembayaran" x-model="form.tipe_pembayaran" @change="updatePaymentType()" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Tipe</option>
                        <option value="CASH" <?= selected('CASH', old('tipe_pembayaran')) ?>>Tunai (CASH)</option>
                        <option value="CREDIT" <?= selected('CREDIT', old('tipe_pembayaran')) ?>>Kredit</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Gudang *</label>
                    <select name="id_warehouse" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Gudang</option>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse['id'] ?>" <?= selected($warehouse['id'], old('id_warehouse')) ?>>
                                <?= $warehouse['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Customer and Salesperson -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Pelanggan *</label>
                    <select name="id_customer" x-model="form.id_customer" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Pelanggan</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>" data-credit="<?= $customer['credit_limit'] ?>">
                                <?= $customer['name'] ?> (<?= $customer['phone'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Salesman (Opsional)</label>
                    <select name="id_salesperson" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Tanpa Salesman</option>
                        <?php foreach ($salespersons as $person): ?>
                            <option value="<?= $person['id'] ?>" <?= selected($person['id'], old('id_salesperson')) ?>>
                                <?= $person['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Credit Limit Warning -->
            <div x-show="form.tipe_pembayaran === 'CREDIT'" class="p-4 rounded-lg bg-warning/10 border border-warning/20 flex items-start gap-3">
                <svg class="h-5 w-5 text-warning flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-warning">
                    <p class="font-semibold">Perhatian Kredit</p>
                    <p class="text-xs mt-1">Pastikan total penjualan tidak melebihi batas kredit pelanggan</p>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan (Opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Catatan tambahan untuk penjualan ini..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"><?= old('catatan') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Package', 'h-5 w-5 text-primary') ?>
                Produk Penjualan
            </h2>
            <button type="button" @click="addProduct()" class="inline-flex items-center justify-center gap-2 h-9 px-4 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary/90 transition">
                <?= icon('Plus', 'h-4 w-4') ?>
                Tambah Produk
            </button>
        </div>

        <div class="p-6">
            <div x-show="form.products.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground">
                <?= icon('Package', 'h-12 w-12 mb-3 opacity-50') ?>
                <p class="text-sm">Belum ada produk. Klik "Tambah Produk" untuk memulai</p>
            </div>

            <div x-show="form.products.length > 0" class="relative w-full overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Qty</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-24">Harga</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-28">Subtotal</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground flex-1">Catatan</th>
                            <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <template x-for="(product, index) in form.products" :key="index">
                            <tr class="hover:bg-muted/50 transition">
                                <!-- Product Selection -->
                                <td class="px-4 py-3">
                                    <select x-model="product.id_produk" @change="updateProductPrice(index)" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                        <option value="">Pilih Produk</option>
                                        <?php foreach ($products as $prod): ?>
                                            <option value="<?= $prod['id'] ?>" data-price="<?= $prod['price_sell'] ?>" data-stock="<?= $prod['stock'] ?>">
                                                <?= $prod['name'] ?> (<?= $prod['sku'] ?>) - Stok: <?= $prod['stock'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <!-- Quantity -->
                                <td class="px-4 py-3">
                                    <input type="number" x-model.number="product.qty" @change="calculateSubtotal(index)" min="1" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Price -->
                                <td class="px-4 py-3">
                                    <input type="number" x-model.number="product.price" @change="calculateSubtotal(index)" min="0" step="100" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Subtotal -->
                                <td class="px-4 py-3 text-right font-semibold text-foreground">
                                    <span x-text="'Rp ' + formatNumber(product.qty * product.price)"></span>
                                </td>

                                <!-- Notes -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="product.catatan" placeholder="Catatan produk..." class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Delete Button -->
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeProduct(index)" class="inline-flex items-center justify-center h-9 w-9 rounded-lg hover:bg-destructive/10 text-destructive transition">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('CalculatorIcon', 'h-5 w-5 text-primary') ?>
                Ringkasan Penjualan
            </h2>
        </div>

        <div class="p-6 space-y-4">
            <!-- Summary Grid -->
            <div class="grid gap-4 md:grid-cols-4">
                <!-- Total Items -->
                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-medium">Total Item</p>
                    <p class="text-2xl font-bold text-foreground mt-1" x-text="form.products.length"></p>
                </div>

                <!-- Subtotal -->
                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-medium">Subtotal</p>
                    <p class="text-2xl font-bold text-foreground mt-1" x-text="'Rp ' + formatNumber(calculateSubtotal())"></p>
                </div>

                <!-- Tax (if applicable) -->
                <div class="space-y-2">
                    <label class="text-xs text-muted-foreground font-medium">Pajak (%) (Opsional)</label>
                    <input type="number" name="tax_percent" x-model.number="form.tax_percent" @change="calculateTotal()" min="0" max="100" step="0.1" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <!-- Total -->
                <div class="p-4 rounded-lg bg-primary/10 border border-primary/20">
                    <p class="text-xs text-primary font-semibold">Total</p>
                    <p class="text-2xl font-bold text-primary mt-1" x-text="'Rp ' + formatNumber(form.total)"></p>
                </div>
            </div>

            <!-- Payment Details (if CREDIT) -->
            <div x-show="form.tipe_pembayaran === 'CREDIT'" class="p-4 rounded-lg bg-muted/30 border border-border/50 space-y-4">
                <h3 class="font-semibold text-foreground text-sm">Rincian Pembayaran Kredit</h3>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Jumlah Dibayar Sekarang (Rp)</label>
                        <input type="number" name="jumlah_dibayar" x-model.number="form.paid_amount" min="0" step="100" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-foreground">Sisa Piutang</label>
                        <input type="text" :value="'Rp ' + formatNumber(form.total - form.paid_amount)" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 justify-end">
        <a href="<?= base_url('transactions/sales') ?>" class="h-10 px-6 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition">
            Batal
        </a>
        <button type="submit" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center gap-2">
            <?= icon('Save', 'h-5 w-5') ?>
            Simpan Penjualan
        </button>
    </div>
</form>

<script>
function salesForm() {
    return {
        form: {
            tipe_pembayaran: '',
            id_customer: '',
            tax_percent: 0,
            paid_amount: 0,
            total: 0,
            products: []
        },

        updatePaymentType() {
            if (this.form.tipe_pembayaran === 'CASH') {
                this.form.paid_amount = this.form.total;
            } else {
                this.form.paid_amount = 0;
            }
        },

        addProduct() {
            this.form.products.push({
                id_produk: '',
                qty: 1,
                price: 0,
                catatan: ''
            });
        },

        removeProduct(index) {
            this.form.products.splice(index, 1);
            this.calculateTotal();
        },

        updateProductPrice(index) {
            const select = event.target;
            const option = select.options[select.selectedIndex];
            const price = option.dataset.price || 0;
            this.form.products[index].price = parseInt(price);
            this.calculateSubtotal(index);
        },

        calculateSubtotal(index) {
            this.calculateTotal();
        },

        calculateSubtotal() {
            return this.form.products.reduce((total, product) => {
                return total + (product.qty * product.price);
            }, 0);
        },

        calculateTotal() {
            const subtotal = this.calculateSubtotal();
            const tax = (subtotal * this.form.tax_percent) / 100;
            this.form.total = subtotal + tax;

            if (this.form.tipe_pembayaran === 'CASH') {
                this.form.paid_amount = this.form.total;
            }
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num || 0);
        }
    };
}
</script>

<?= $this->endSection() ?>
