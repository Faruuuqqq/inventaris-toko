<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('ShoppingCart', 'h-8 w-8 text-primary') ?>
            Detail Purchase Order
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Lihat detail pesanan pembelian</p>
    </div>
    <a href="<?= base_url('transactions/purchases') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Main Content (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- PO Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                    Informasi Purchase Order
                </h2>
            </div>

            <div class="p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">No. PO</p>
                            <p class="text-sm font-semibold text-foreground font-mono"><?= $purchaseOrder['nomor_po'] ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Tanggal PO</p>
                            <p class="text-sm font-semibold text-foreground"><?= format_date($purchaseOrder['tanggal_po']) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Estimasi Pengiriman</p>
                            <p class="text-sm font-semibold text-foreground"><?= format_date($purchaseOrder['estimasi_tanggal']) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Supplier</p>
                            <p class="text-sm font-semibold text-foreground"><?= $purchaseOrder['supplier']['name'] ?></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Gudang Penerima</p>
                            <p class="text-sm font-semibold text-foreground"><?= $purchaseOrder['warehouse']['nama_warehouse'] ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Status</p>
                            <div class="mt-1">
                                <?= status_badge($purchaseOrder['status']) ?>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Catatan</p>
                            <p class="text-sm text-foreground"><?= $purchaseOrder['keterangan'] ?: '<span class="text-muted-foreground">-</span>' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Package', 'h-5 w-5 text-primary') ?>
                    Daftar Produk
                </h2>
            </div>

            <div class="p-6">
                <div class="relative w-full overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 border-b border-border/50">
                            <tr>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Qty Order</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-28">Harga Beli</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-28">Subtotal</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Diterima</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/50">
                            <?php foreach ($purchaseOrder['details'] as $detail): ?>
                                <tr class="hover:bg-muted/50 transition">
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-medium text-foreground"><?= $detail['nama_produk'] ?></p>
                                            <p class="text-xs text-muted-foreground"><?= $detail['kode_produk'] ?></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium"><?= $detail['jumlah'] ?></td>
                                    <td class="px-4 py-3 text-right">Rp <?= number_format($detail['harga_beli'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-3 text-right font-semibold text-primary">Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center justify-center h-8 w-12 rounded-lg bg-success/10 text-success font-medium text-sm">
                                            <?= $detail['jumlah_diterima'] ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center justify-center h-8 w-12 rounded-lg bg-warning/10 text-warning font-medium text-sm">
                                            <?= $detail['jumlah'] - $detail['jumlah_diterima'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-muted/30 border-t border-border/50">
                            <tr class="font-bold">
                                <td colspan="3" class="px-4 py-3 text-right">Total:</td>
                                <td class="px-4 py-3 text-right text-primary text-base">Rp <?= number_format($purchaseOrder['total_bayar'], 0, ',', '.') ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar (1/3) -->
    <div class="space-y-6">
        <!-- Summary Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden sticky top-24">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground">Ringkasan</h2>
            </div>

            <div class="p-6 space-y-4">
                <!-- Total Amount -->
                <div class="rounded-lg bg-primary/10 p-4 border border-primary/20">
                    <p class="text-xs font-medium text-primary uppercase mb-1">Total Amount</p>
                    <p class="text-2xl font-bold text-primary">Rp <?= number_format($purchaseOrder['total_bayar'], 0, ',', '.') ?></p>
                </div>

                <!-- Received Value -->
                <?php 
                $totalReceived = 0;
                foreach ($purchaseOrder['details'] as $detail) {
                    $totalReceived += $detail['jumlah_diterima'] * $detail['harga_beli'];
                }
                $totalRemaining = $purchaseOrder['total_bayar'] - $totalReceived;
                ?>

                <div class="rounded-lg bg-success/10 p-4 border border-success/20">
                    <p class="text-xs font-medium text-success uppercase mb-1">Nilai Diterima</p>
                    <p class="text-lg font-bold text-success">Rp <?= number_format($totalReceived, 0, ',', '.') ?></p>
                </div>

                <!-- Remaining Value -->
                <div class="rounded-lg bg-warning/10 p-4 border border-warning/20">
                    <p class="text-xs font-medium text-warning uppercase mb-1">Nilai Sisa</p>
                    <p class="text-lg font-bold text-warning">Rp <?= number_format($totalRemaining, 0, ',', '.') ?></p>
                </div>

                <div class="border-t border-border/50 pt-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">Status Penerimaan</span>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full 
                            <?php 
                            if ($purchaseOrder['status'] === 'Diterima Semua') {
                                echo 'bg-success/10 text-success';
                            } elseif ($purchaseOrder['status'] === 'Diterima Sebagian') {
                                echo 'bg-warning/10 text-warning';
                            } else {
                                echo 'bg-muted/50 text-muted-foreground';
                            }
                            ?>
                        ">
                            <?php 
                            if ($purchaseOrder['status'] === 'Diterima Semua') {
                                echo 'Lengkap';
                            } elseif ($purchaseOrder['status'] === 'Diterima Sebagian') {
                                echo 'Sebagian';
                            } else {
                                echo 'Belum Diterima';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground">Aksi</h2>
            </div>

            <div class="p-6 space-y-3">
                <?php if ($purchaseOrder['status'] === 'Dipesan'): ?>
                    <a href="<?= base_url('transactions/purchases/edit/' . $purchaseOrder['id_po']) ?>" class="w-full h-10 inline-flex items-center justify-center gap-2 bg-warning text-white font-medium text-sm rounded-lg hover:bg-warning/90 transition">
                        <?= icon('Edit', 'h-4 w-4') ?>
                        Edit PO
                    </a>
                <?php endif; ?>

                <?php if ($purchaseOrder['status'] !== 'Diterima Semua' && $purchaseOrder['status'] !== 'Dibatalkan'): ?>
                    <a href="<?= base_url('transactions/purchases/receive/' . $purchaseOrder['id_po']) ?>" class="w-full h-10 inline-flex items-center justify-center gap-2 bg-success text-white font-medium text-sm rounded-lg hover:bg-success/90 transition">
                        <?= icon('PackageCheck', 'h-4 w-4') ?>
                        Terima Stock
                    </a>
                <?php endif; ?>

                <a href="<?= base_url('info/stockcard?id_produk=all') ?>" class="w-full h-10 inline-flex items-center justify-center gap-2 border border-border/50 text-foreground font-medium text-sm rounded-lg hover:bg-muted transition">
                    <?= icon('BarChart3', 'h-4 w-4') ?>
                    Lihat Stock Card
                </a>

                <a href="#" target="_blank" class="w-full h-10 inline-flex items-center justify-center gap-2 border border-border/50 text-foreground font-medium text-sm rounded-lg hover:bg-muted transition">
                    <?= icon('Printer', 'h-4 w-4') ?>
                    Cetak
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>