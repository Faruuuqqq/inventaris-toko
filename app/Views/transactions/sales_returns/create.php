<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('RotateCcw', 'h-8 w-8 text-primary') ?>
            <?= $title ?? 'Buat Sales Return' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Proses pengembalian barang penjualan dari customer</p>
    </div>
    <a href="<?= base_url('transactions/sales-returns') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Sales Return Form -->
<form method="post" action="<?= base_url('transactions/sales-returns/store') ?>" x-data="salesReturnForm()" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Header Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Informasi Sales Return
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Return Number and Date -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. Return</label>
                    <input type="text" name="nomor_retur" value="<?= old('nomor_retur', $nomor_retur) ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tanggal Return *</label>
                    <input type="date" name="tanggal_retur" value="<?= old('tanggal_retur', date('Y-m-d')) ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Status</label>
                    <select name="status" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="Menunggu Persetujuan" <?= selected('Menunggu Persetujuan', old('status')) ?>>Menunggu Persetujuan</option>
                        <?php if (is_admin()): ?>
                            <option value="Disetujui" <?= selected('Disetujui', old('status')) ?>>Disetujui</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Gudang Asal *</label>
                    <select name="id_warehouse_asal" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Gudang</option>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse['id_warehouse'] ?>" <?= selected($warehouse['id_warehouse'], old('id_warehouse_asal')) ?>>
                                <?= $warehouse['nama_warehouse'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Customer and Sales Reference -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Customer *</label>
                    <select name="id_customer" x-model="form.id_customer" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Customer</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id_customer'] ?>" <?= selected($customer['id_customer'], old('id_customer')) ?>>
                                <?= $customer['nama_customer'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Referensi Penjualan (Opsional)</label>
                    <select name="id_penjualan" @change="loadSalesDetails()" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Penjualan</option>
                        <?php foreach ($salesList as $sale): ?>
                            <option value="<?= $sale['id_penjualan'] ?>" data-customer="<?= $sale['id_customer'] ?>">
                                <?= $sale['nomor_penjualan'] ?> - <?= format_date($sale['tanggal_penjualan']) ?> (<?= $sale['nama_customer'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan (Opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="Alasan pengembalian atau keterangan tambahan..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"><?= old('keterangan') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Package', 'h-5 w-5 text-primary') ?>
                Produk yang Dikembalikan
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
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Qty</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-28">Harga Satuan</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-28">Total Refund</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-32">Alasan</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground flex-1">Keterangan</th>
                            <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <template x-for="(product, index) in form.products" :key="index">
                            <tr class="hover:bg-muted/50 transition">
                                <!-- Product Selection -->
                                <td class="px-4 py-3">
                                    <select x-model="product.id_produk" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                        <option value="">Pilih Produk</option>
                                        <?php foreach ($products as $product_option): ?>
                                            <option value="<?= $product_option['id_produk'] ?>">
                                                <?= $product_option['nama_produk'] ?> (<?= $product_option['kode_produk'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <!-- Quantity -->
                                <td class="px-4 py-3">
                                    <input type="number" x-model.number="product.jumlah" @input="calculateRefund(index)" min="1" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Unit Price -->
                                <td class="px-4 py-3">
                                    <input type="number" x-model.number="product.harga_satuan" @input="calculateRefund(index)" min="0" step="0.01" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Total Refund -->
                                <td class="px-4 py-3 text-right font-semibold" x-text="'Rp ' + formatNumber(product.total_refund)"></td>

                                <!-- Reason -->
                                <td class="px-4 py-3">
                                    <select x-model="product.alasan" required class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                        <option value="">Pilih Alasan</option>
                                        <option value="Cacat">Cacat</option>
                                        <option value="Salah Ukuran">Salah Ukuran</option>
                                        <option value="Tidak Sesuai">Tidak Sesuai</option>
                                        <option value="Kadaluarsa">Kadaluarsa</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </td>

                                <!-- Notes -->
                                <td class="px-4 py-3">
                                    <input type="text" x-model="product.keterangan" placeholder="Keterangan..." class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                                </td>

                                <!-- Remove Button -->
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeProduct(index)" x-show="form.products.length > 1" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-destructive hover:bg-destructive/10 transition">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-muted/30 border-t border-border/50">
                        <tr class="font-bold">
                            <td colspan="3" class="px-4 py-3 text-right">Total Refund:</td>
                            <td class="px-4 py-3 text-right text-primary text-lg" x-text="'Rp ' + formatNumber(totalRefund)"></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Empty State -->
            <template x-if="form.products.length === 0">
                <div class="flex flex-col items-center justify-center py-8 text-muted-foreground">
                    <?= icon('Package', 'h-12 w-12 mb-3 opacity-50') ?>
                    <p class="text-sm">Belum ada produk. Klik "Tambah Produk" untuk memulai.</p>
                </div>
            </template>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 justify-end">
        <a href="<?= base_url('transactions/sales-returns') ?>" class="h-10 px-6 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition">
            Batal
        </a>
        <button type="submit" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center gap-2">
            <?= icon('Save', 'h-4 w-4') ?>
            Simpan Sales Return
        </button>
    </div>
</form>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('salesReturnForm', () => ({
            form: {
                id_customer: '',
                products: [{
                    id_produk: '',
                    jumlah: 1,
                    harga_satuan: 0,
                    total_refund: 0,
                    alasan: '',
                    keterangan: ''
                }]
            },
            totalRefund: 0,

            init() {
                this.$watch('form.products', () => {
                    this.calculateTotalRefund();
                });
            },

            addProduct() {
                this.form.products.push({
                    id_produk: '',
                    jumlah: 1,
                    harga_satuan: 0,
                    total_refund: 0,
                    alasan: '',
                    keterangan: ''
                });
            },

            removeProduct(index) {
                if (this.form.products.length > 1) {
                    this.form.products.splice(index, 1);
                } else {
                    alert('Minimal 1 produk harus ada');
                }
            },

            calculateRefund(index) {
                const product = this.form.products[index];
                product.total_refund = (product.jumlah || 0) * (product.harga_satuan || 0);
                this.calculateTotalRefund();
            },

            calculateTotalRefund() {
                this.totalRefund = this.form.products.reduce((sum, product) => sum + (product.total_refund || 0), 0);
            },

            loadSalesDetails() {
                const select = document.querySelector('select[name="id_penjualan"]');
                const selectedOption = select.options[select.selectedIndex];
                const customerId = selectedOption.dataset.customer;

                if (customerId && !this.form.id_customer) {
                    this.form.id_customer = customerId;
                }

                // Here you could load sales details via AJAX if needed
            },

            formatNumber(value) {
                return new Intl.NumberFormat('id-ID').format(Math.round(value || 0));
            }
        }));
    });
</script>

<?= $this->endSection() ?>