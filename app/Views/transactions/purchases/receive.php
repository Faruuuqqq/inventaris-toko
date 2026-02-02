<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('PackageCheck', 'h-8 w-8 text-primary') ?>
            Terima Barang
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Konfirmasi penerimaan pesanan pembelian</p>
    </div>
    <a href="<?= base_url('transactions/purchases') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Receive Form -->
<form method="post" action="<?= base_url('transactions/purchases/processReceive/' . $purchaseOrder['id_po']) ?>" x-data="receiveForm()" class="space-y-6">
    <?= csrf_field() ?>

    <!-- PO Information Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Informasi Pesanan Pembelian
            </h2>
        </div>

        <div class="p-6 grid gap-6 md:grid-cols-2">
            <!-- Left Column: PO Details -->
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">No. PO</p>
                    <p class="text-lg font-bold text-foreground mt-1"><?= $purchaseOrder['nomor_po'] ?></p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Tanggal PO</p>
                    <p class="text-sm font-medium text-foreground mt-1"><?= format_date($purchaseOrder['tanggal_po']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Supplier</p>
                    <p class="text-sm font-medium text-foreground mt-1"><?= $purchaseOrder['supplier']['name'] ?></p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Gudang Penerima</p>
                    <p class="text-sm font-medium text-foreground mt-1"><?= $purchaseOrder['warehouse']['nama_warehouse'] ?></p>
                </div>
            </div>

            <!-- Right Column: Receive Date -->
            <div>
                <div class="space-y-2">
                    <label for="tanggal_terima" class="text-sm font-medium text-foreground">Tanggal Penerimaan *</label>
                    <input type="date" id="tanggal_terima" name="tanggal_terima" value="<?= date('Y-m-d') ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
            </div>
        </div>
    </div>

    <!-- Products to Receive Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Box', 'h-5 w-5 text-primary') ?>
                Produk yang Diterima
            </h2>
        </div>

        <div class="p-6 overflow-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 border-b border-border/50">
                    <tr>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Dipesan</th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Sudah Terima</th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Qty Terima</th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Baik</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-32">Gudang Baik</th>
                        <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Rusak</th>
                        <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-32">Gudang Rusak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    <?php foreach ($purchaseOrder['details'] as $index => $detail): ?>
                        <tr x-data="productRow()" class="hover:bg-muted/50 transition">
                            <input type="hidden" name="produk[<?= $index ?>][id_detail]" value="<?= $detail['id_detail'] ?>">
                            <input type="hidden" name="produk[<?= $index ?>][id_produk]" value="<?= $detail['id_produk'] ?>">
                            
                            <!-- Product Name -->
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-semibold text-foreground"><?= $detail['nama_produk'] ?></p>
                                    <p class="text-xs text-muted-foreground"><?= $detail['kode_produk'] ?></p>
                                </div>
                            </td>
                            
                            <!-- Ordered Qty -->
                            <td class="px-4 py-3 text-right font-medium text-foreground"><?= $detail['jumlah'] ?></td>
                            
                            <!-- Previously Received -->
                            <td class="px-4 py-3 text-right font-medium text-foreground"><?= $detail['jumlah_diterima'] ?></td>
                            
                            <!-- Quantity to Receive -->
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <input type="number" class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50" name="produk[<?= $index ?>][jumlah_diterima]" x-model.number="jumlahDiterima" @input="validateQuantity()" min="0" max="<?= $detail['jumlah'] - $detail['jumlah_diterima'] ?>" value="<?= max(0, $detail['jumlah'] - $detail['jumlah_diterima']) ?>" required>
                                    <p class="text-xs text-muted-foreground">Max: <?= $detail['jumlah'] - $detail['jumlah_diterima'] ?></p>
                                </div>
                            </td>
                            
                            <!-- Good Items -->
                            <td class="px-4 py-3">
                                <input type="number" class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50" name="produk[<?= $index ?>][jumlah_baik]" x-model.number="jumlahBaik" @input="validateDistribution()" min="0" value="<?= max(0, $detail['jumlah'] - $detail['jumlah_diterima']) ?>">
                            </td>
                            
                            <!-- Good Warehouse -->
                            <td class="px-4 py-3">
                                <select class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" name="produk[<?= $index ?>][id_warehouse_baik]" :disabled="jumlahBaik == 0">
                                    <option value="">Pilih</option>
                                    <?php foreach ($warehouses_good as $warehouse): ?>
                                        <option value="<?= $warehouse['id_warehouse'] ?>" <?= $purchaseOrder['id_warehouse'] == $warehouse['id_warehouse'] ? 'selected' : '' ?>>
                                            <?= $warehouse['nama_warehouse'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            
                            <!-- Damaged Items -->
                            <td class="px-4 py-3">
                                <input type="number" class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50" name="produk[<?= $index ?>][jumlah_rusak]" x-model.number="jumlahRusak" @input="validateDistribution()" min="0" value="0">
                            </td>
                            
                            <!-- Damaged Warehouse -->
                            <td class="px-4 py-3">
                                <select class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" name="produk[<?= $index ?>][id_warehouse_rusak]" :disabled="jumlahRusak == 0">
                                    <option value="">Pilih</option>
                                    <?php foreach ($warehouses_damaged as $warehouse): ?>
                                        <option value="<?= $warehouse['id_warehouse'] ?>">
                                            <?= $warehouse['nama_warehouse'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between gap-3">
        <a href="<?= base_url('transactions/purchases') ?>" class="inline-flex items-center justify-center h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-success text-white font-medium rounded-lg hover:bg-success/90 transition">
            <?= icon('Check', 'h-5 w-5') ?>
            Konfirmasi Penerimaan
        </button>
    </div>
</form>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('receiveForm', () => ({
        // Main form logic here if needed
    }));
    
    Alpine.data('productRow', () => ({
        jumlahDiterima: 0,
        jumlahBaik: 0,
        jumlahRusak: 0,
        
        init() {
            this.jumlahDiterima = parseInt(this.$el.querySelector('input[name*="jumlah_diterima"]').value) || 0;
            this.jumlahBaik = parseInt(this.$el.querySelector('input[name*="jumlah_baik"]').value) || 0;
            this.jumlahRusak = parseInt(this.$el.querySelector('input[name*="jumlah_rusak"]').value) || 0;
        },
        
        validateQuantity() {
            const maxQty = parseInt(this.$el.querySelector('input[name*="jumlah_diterima"]').max) || 0;
            if (this.jumlahDiterima > maxQty) {
                this.jumlahDiterima = maxQty;
            }
            this.validateDistribution();
        },
        
        validateDistribution() {
            const total = this.jumlahBaik + this.jumlahRusak;
            if (total > this.jumlahDiterima) {
                const excess = total - this.jumlahDiterima;
                if (this.jumlahRusak >= excess) {
                    this.jumlahRusak -= excess;
                } else {
                    this.jumlahBaik -= (excess - this.jumlahRusak);
                    this.jumlahRusak = 0;
                }
            }
        }
    }));
});
</script>

<?= $this->endSection() ?>
