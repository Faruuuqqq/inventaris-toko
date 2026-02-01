<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('RotateCcw', 'h-8 w-8 text-primary') ?>
            Detail Purchase Return
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Lihat detail pengembalian pembelian</p>
    </div>
    <a href="<?= base_url('transactions/purchase-returns') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Main Content (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Return Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                    Informasi Purchase Return
                </h2>
            </div>

            <div class="p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">No. Return</p>
                            <p class="text-sm font-semibold text-foreground font-mono"><?= $purchaseReturn['nomor_retur'] ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Tanggal Return</p>
                            <p class="text-sm font-semibold text-foreground"><?= format_date($purchaseReturn['tanggal_retur']) ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Supplier</p>
                            <p class="text-sm font-semibold text-foreground"><?= $purchaseReturn['supplier']['name'] ?></p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Gudang Asal</p>
                            <p class="text-sm font-semibold text-foreground"><?= $purchaseReturn['warehouse']['nama_warehouse'] ?></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Status</p>
                            <div class="mt-1">
                                <?= status_badge($purchaseReturn['status']) ?>
                            </div>
                        </div>

                        <?php if ($purchaseReturn['tanggal_proses']): ?>
                            <div>
                                <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Tanggal Proses</p>
                                <p class="text-sm font-semibold text-foreground"><?= format_date($purchaseReturn['tanggal_proses']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($purchaseReturn['approval_notes']): ?>
                            <div>
                                <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Catatan Persetujuan</p>
                                <p class="text-sm text-foreground"><?= $purchaseReturn['approval_notes'] ?></p>
                            </div>
                        <?php endif; ?>

                        <div>
                            <p class="text-xs font-medium text-muted-foreground uppercase mb-1">Catatan Return</p>
                            <p class="text-sm text-foreground"><?= $purchaseReturn['keterangan'] ?: '<span class="text-muted-foreground">-</span>' ?></p>
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
                    Daftar Produk yang Dikembalikan
                </h2>
            </div>

            <div class="p-6">
                <div class="relative w-full overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 border-b border-border/50">
                            <tr>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Qty</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-32">Alasan</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground flex-1">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/50">
                            <?php foreach ($purchaseReturn['details'] as $detail): ?>
                                <tr class="hover:bg-muted/50 transition">
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-medium text-foreground"><?= $detail['nama_produk'] ?></p>
                                            <p class="text-xs text-muted-foreground"><?= $detail['kode_produk'] ?></p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium"><?= $detail['jumlah'] ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-warning/10 text-warning font-medium text-sm">
                                            <?= $detail['alasan'] ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-muted-foreground text-sm"><?= $detail['keterangan'] ?: '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-muted/30 border-t border-border/50">
                            <tr class="font-bold">
                                <td colspan="2" class="px-4 py-3 text-right">Total Dikembalikan:</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-primary/10 text-primary font-medium text-sm">
                                        <?= count($purchaseReturn['details']) ?> produk
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar (1/3) -->
    <div class="space-y-6">
        <!-- Status Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden sticky top-24">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground">Status</h2>
            </div>

            <div class="p-6 space-y-4">
                <!-- Status Badge -->
                <div class="rounded-lg bg-primary/10 p-4 border border-primary/20">
                    <p class="text-xs font-medium text-primary uppercase mb-2">Status Return</p>
                    <?= status_badge($purchaseReturn['status']) ?>
                </div>

                <!-- Refund Info -->
                <?php if ($purchaseReturn['total_refund'] > 0): ?>
                    <div class="rounded-lg bg-success/10 p-4 border border-success/20">
                        <p class="text-xs font-medium text-success uppercase mb-1">Jumlah Retur</p>
                        <p class="text-lg font-bold text-success">Rp <?= number_format($purchaseReturn['total_refund'], 0, ',', '.') ?></p>
                    </div>
                <?php endif; ?>

                <!-- Metadata -->
                <div class="border-t border-border/50 pt-4 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Dibuat pada</span>
                        <span class="font-medium"><?= format_datetime($purchaseReturn['created_at']) ?></span>
                    </div>

                    <?php if ($purchaseReturn['approved_by']): ?>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-muted-foreground">Disetujui oleh</span>
                            <span class="font-medium">User #<?= $purchaseReturn['approved_by'] ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground">Aksi</h2>
            </div>

            <div class="p-6 space-y-3">
                <?php if ($purchaseReturn['status'] === 'Menunggu Persetujuan'): ?>
                    <a href="<?= base_url('transactions/purchase-returns/approve/' . $purchaseReturn['id_retur_pembelian']) ?>" class="w-full h-10 inline-flex items-center justify-center gap-2 bg-success text-white font-medium text-sm rounded-lg hover:bg-success/90 transition">
                        <?= icon('Check', 'h-4 w-4') ?>
                        Setujui Return
                    </a>
                    <a href="<?= base_url('transactions/purchase-returns/edit/' . $purchaseReturn['id_retur_pembelian']) ?>" class="w-full h-10 inline-flex items-center justify-center gap-2 bg-warning text-white font-medium text-sm rounded-lg hover:bg-warning/90 transition">
                        <?= icon('Edit', 'h-4 w-4') ?>
                        Edit Return
                    </a>
                <?php endif; ?>

                <a href="<?= base_url('info/stockcard?id_produk=all') ?>" class="w-full h-10 inline-flex items-center justify-center gap-2 border border-border/50 text-foreground font-medium text-sm rounded-lg hover:bg-muted transition">
                    <?= icon('BarChart3', 'h-4 w-4') ?>
                    Lihat Stock Card
                </a>

                <a href="#" class="w-full h-10 inline-flex items-center justify-center gap-2 border border-border/50 text-foreground font-medium text-sm rounded-lg hover:bg-muted transition">
                    <?= icon('Printer', 'h-4 w-4') ?>
                    Cetak
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>