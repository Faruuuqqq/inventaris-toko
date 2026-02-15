<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('DollarSign', 'h-8 w-8 text-destructive') ?>
            <?= $title ?? 'Saldo Utang' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Kelola utang kepada supplier' ?></p>
    </div>
    <a href="<?= base_url('/finance') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ChevronLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Total Summary Card -->
<div class="mb-8 rounded-lg border bg-gradient-to-br from-destructive/5 to-destructive/2 shadow-sm overflow-hidden">
    <div class="p-6 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-muted-foreground">Total Utang Seluruhnya</p>
            <p class="text-3xl font-bold text-foreground mt-2"><?= format_currency($totalPayable ?? 0) ?></p>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-destructive/10">
            <?= icon('DollarSign', 'h-8 w-8 text-destructive') ?>
        </div>
    </div>
</div>

<!-- Suppliers Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Daftar Supplier</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Nama Supplier</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Total Utang</th>
                    <th class="px-6 py-3 text-center font-semibold text-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <?= icon('FileText', 'h-12 w-12 text-muted-foreground/50') ?>
                                <p class="text-sm font-medium text-muted-foreground">Tidak ada data utang</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-foreground"><?= esc($supplier->name ?? '') ?></td>
                        <td class="px-6 py-4 text-right font-medium text-destructive"><?= format_currency($supplier->debt_balance ?? 0) ?></td>
                        <td class="px-6 py-4 text-center">
                            <a href="<?= base_url('/finance/payments/payable?supplier_id=' . esc($supplier->id ?? '')) ?>" class="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-lg bg-primary text-white font-medium text-sm hover:bg-primary/90 transition">
                                <?= icon('Plus', 'h-4 w-4') ?>
                                Bayar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
