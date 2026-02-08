<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('ShoppingCart', 'h-8 w-8 text-primary') ?>
            <?= $title ?? 'Buat Purchase Order' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Buat pesanan pembelian baru ke supplier</p>
    </div>
    <a href="<?= base_url('transactions/purchases') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Purchase Form -->
<form method="post" action="<?= base_url('transactions/purchases/store') ?>" x-data="purchaseOrderForm()" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Header Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Informasi Purchase Order
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- PO Number and Date -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. PO</label>
                    <input type="text" name="nomor_po" value="<?= old('nomor_po', $nomor_po) ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tanggal *</label>
                    <input type="date" name="tanggal_po" value="<?= old('tanggal_po', date('Y-m-d')) ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Est. Pengiriman *</label>
                    <input type="date" name="estimasi_tanggal" value="<?= old('estimasi_tanggal') ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Status</label>
                    <input type="text" value="Dipesan" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground">
                    <input type="hidden" name="status" value="Dipesan">
                </div>
            </div>

            <!-- Supplier and Warehouse -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Supplier *</label>
                    <select name="id_supplier" x-model="form.id_supplier" @change="updatePrices()" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier->id ?>" <?= selected($supplier->id, old('id_supplier')) ?>>
                                <?= esc($supplier->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Gudang Penerima *</label>
                    <select name="id_warehouse" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Gudang</option>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse->id ?>" <?= selected($warehouse->id, old('id_warehouse')) ?>>
                                <?= esc($warehouse->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan (Opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="Masukkan catatan atau spesifikasi khusus..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"><?= old('keterangan') ?></textarea>
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
            <div class="relative w-full overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-24">Qty</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-32">Harga Beli</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-32">Subtotal</th>
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
                                        <?php foreach ($products as $product_option): ?>
                                            <option value="<?= $product_option->id ?>" data-price="<?= $product_option->price_buy ?>">
                                                <?= esc($product_option->name) ?> (<?= esc($product_option->sku) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                 </td>
                                <!-- Quantity -->
                                <td class="px-4 py-3">
                                    <input type="number" x-model.number="product.qty" @change="updateProductPrice(index)" min="1" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-right text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>
                                <!-- Purchase Price -->
                                <td class="px-4 py-3 text-right">
                                    <input type="number" x-model.number="product.harga_beli" @change="updateProductPrice(index)" step="0.01" min="0" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-right text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 font-mono">
                                </td>
                                <!-- Subtotal -->
                                <td class="px-4 py-3 text-right font-semibold text-foreground">
                                    <span x-text="'Rp ' + (product.qty * product.harga_beli).toLocaleString('id-ID', {minimumFractionDigits: 0})"></span>
                                </td>
                                <!-- Notes -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="product.keterangan" placeholder="Catatan..." class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>
                                <!-- Remove Button -->
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeProduct(index)" class="text-destructive hover:text-destructive/80 transition">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <template x-if="form.products.length === 0">
                <div class="flex flex-col items-center justify-center py-12 text-muted-foreground">
                    <?= icon('Package', 'h-12 w-12 mb-3 opacity-50') ?>
                    <p class="text-sm">Belum ada produk. Klik "Tambah Produk" untuk memulai</p>
                </div>
            </template>
        </div>

        <!-- Summary Section -->
        <div class="p-6 border-t border-border/50 bg-muted/20">
            <div class="grid gap-4 md:grid-cols-3 text-right">
                <div>
                    <p class="text-sm text-muted-foreground mb-1">Total Qty</p>
                    <p class="text-2xl font-bold text-foreground" x-text="form.products.reduce((s, p) => s + (p.qty || 0), 0)"></p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground mb-1">Total Beli</p>
                    <p class="text-2xl font-bold text-primary" x-text="'Rp ' + form.products.reduce((s, p) => s + ((p.qty || 0) * (p.harga_beli || 0)), 0).toLocaleString('id-ID', {minimumFractionDigits: 0})"></p>
                </div>
                <div class="text-right">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition">
                        <?= icon('Save', 'h-5 w-5') ?>
                        Simpan PO
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function purchaseOrderForm() {
        return {
            form: {
                id_supplier: '<?= old('id_supplier') ?>',
                products: <?= json_encode(old('products') ?? [['id_produk' => '', 'qty' => 1, 'harga_beli' => 0, 'keterangan' => '']]) ?>
            },

            addProduct() {
                this.form.products.push({
                    id_produk: '',
                    qty: 1,
                    harga_beli: 0,
                    keterangan: ''
                });
            },

            removeProduct(index) {
                this.form.products.splice(index, 1);
            },

            updateProductPrice(index) {
                // Price update logic here
            },

            updatePrices() {
                // Update supplier prices
            }
        };
    }
</script>

<?= $this->endSection() ?>
