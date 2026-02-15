<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('FileEdit', 'h-8 w-8 text-primary') ?>
            Edit Pesanan Pembelian
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Ubah detail pesanan pembelian ke supplier</p>
    </div>
    <a href="<?= base_url('transactions/purchases/detail/' . $purchaseOrder->id_po) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Edit Form -->
<form method="post" action="<?= base_url('transactions/purchases/update/' . $purchaseOrder->id_po) ?>" x-data="purchaseOrderForm()" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Header Information Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Informasi Pesanan
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Purchase Order Number, Date, Estimated Date -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. PO</label>
                    <input type="text" value="<?= $purchaseOrder->nomor_po ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                    <input type="hidden" name="nomor_po" value="<?= $purchaseOrder->nomor_po ?>">
                </div>

                <div class="space-y-2">
                    <label for="tanggal_po" class="text-sm font-medium text-foreground">Tanggal PO *</label>
                    <input type="date" name="tanggal_po" id="tanggal_po" value="<?= old('tanggal_po', $purchaseOrder->tanggal_po) ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="space-y-2">
                    <label for="estimasi_tanggal" class="text-sm font-medium text-foreground">Estimasi Terima *</label>
                    <input type="date" name="estimasi_tanggal" id="estimasi_tanggal" value="<?= old('estimasi_tanggal', $purchaseOrder->estimasi_tanggal) ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
            </div>

            <!-- Supplier and Warehouse -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="space-y-2">
                    <label for="id_supplier" class="text-sm font-medium text-foreground">Supplier *</label>
                    <select name="id_supplier" id="id_supplier" x-model="form.id_supplier" @change="updatePrices()" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier->id ?>" <?= selected($supplier->id, old('id_supplier', $purchaseOrder->id_supplier)) ?>>
                                <?= esc($supplier->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="id_warehouse" class="text-sm font-medium text-foreground">Gudang Penerima *</label>
                    <select name="id_warehouse" id="id_warehouse" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Gudang</option>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse->id ?>" <?= selected($warehouse->id, old('id_warehouse', $purchaseOrder->id_warehouse)) ?>>
                                <?= esc($warehouse->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="status" class="text-sm font-medium text-foreground">Status *</label>
                    <select name="status" id="status" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="Dipesan" <?= selected('Dipesan', old('status', $purchaseOrder->status)) ?>>Dipesan</option>
                        <option value="Dibatalkan" <?= selected('Dibatalkan', old('status', $purchaseOrder->status)) ?>>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label for="keterangan" class="text-sm font-medium text-foreground">Catatan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="2" placeholder="Masukkan catatan atau spesifikasi khusus..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"><?= old('keterangan', esc($purchaseOrder->keterangan)) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Package', 'h-5 w-5 text-primary') ?>
                Daftar Produk
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
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-24">Harga Beli</th>
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
                                            <option value="<?= $prod->id ?>" data-price="<?= $prod->price_buy ?>">
                                                <?= esc($prod->name) ?> (<?= esc($prod->sku) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <!-- Quantity -->
                                <td class="px-4 py-3">
                                    <input type="number" x-model.number="product.jumlah" @change="calculateSubtotal(index)" min="1" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Purchase Price -->
                                <td class="px-4 py-3">
                                    <input type="number" x-model.number="product.harga_beli" @change="calculateSubtotal(index)" min="0" step="1" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Subtotal -->
                                <td class="px-4 py-3 text-right font-semibold text-foreground">
                                    <span x-text="'Rp ' + formatNumber(product.jumlah * product.harga_beli)"></span>
                                </td>

                                <!-- Notes -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="product.keterangan" placeholder="Catatan produk..." class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
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
                Ringkasan Pesanan
            </h2>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-foreground">Total Produk:</span>
                    <span class="font-semibold text-foreground" x-text="form.products.length"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-foreground">Total Harga:</span>
                    <span class="font-bold text-lg text-primary" x-text="'Rp ' + formatNumber(total)"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between gap-3">
        <a href="<?= base_url('transactions/purchases/detail/' . $purchaseOrder->id_po) ?>" class="inline-flex items-center justify-center h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
            <?= icon('Check', 'h-5 w-5') ?>
            Simpan Perubahan
        </button>
    </div>
</form>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('purchaseOrderForm', () => ({
        form: {
            id_supplier: '<?= $purchaseOrder["id_supplier"] ?>',
            products: [
                <?php foreach ($purchaseOrder->details as $detail): ?>
                    {
                        id_produk: '<?= $detail["id_produk"] ?>',
                        jumlah: <?= $detail["jumlah"] ?>,
                        harga_beli: <?= $detail["harga_beli"] ?>,
                        subtotal: <?= $detail["subtotal"] ?>,
                        keterangan: '<?= $detail["keterangan"] ?>'
                    },
                <?php endforeach; ?>
            ]
        },
        total: <?= $purchaseOrder->total_bayar ?>,
        
        init() {
            this.$watch('form.products', () => {
                this.calculateTotal();
            });
        },
        
        addProduct() {
            this.form.products.push({
                id_produk: '',
                jumlah: 1,
                harga_beli: 0,
                subtotal: 0,
                keterangan: ''
            });
        },
        
        removeProduct(index) {
            this.form.products.splice(index, 1);
        },
        
        updateProductPrice(index) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            
            this.form.products[index].harga_beli = price;
            this.calculateSubtotal(index);
        },
        
        calculateSubtotal(index) {
            const product = this.form.products[index];
            product.subtotal = product.jumlah * product.harga_beli;
            this.calculateTotal();
        },
        
        calculateTotal() {
            this.total = this.form.products.reduce((sum, product) => sum + (product.subtotal || 0), 0);
        },
        
        updatePrices() {
            // This can be extended to update prices based on supplier
        },
        
        formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }
    }));
});
</script>

<?= $this->endSection() ?>
