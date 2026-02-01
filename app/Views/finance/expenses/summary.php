<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('BarChart3', 'h-8 w-8 text-primary') ?>
            Ringkasan Biaya
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Laporan pengeluaran berdasarkan kategori' ?></p>
    </div>
    <a href="<?= base_url('finance/expenses') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Filter Card -->
<div class="mb-6 rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Filter', 'h-5 w-5 text-primary') ?>
            Filter Periode
        </h2>
    </div>

    <div class="p-6">
        <form action="<?= base_url('finance/expenses/summary') ?>" method="get" class="grid gap-4 md:grid-cols-4 items-end">
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= $startDate ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= $endDate ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>

            <button type="submit" class="h-10 inline-flex items-center justify-center gap-2 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary/90 transition">
                <?= icon('Filter', 'h-4 w-4') ?>
                Filter
            </button>

            <a href="<?= base_url('finance/expenses/summary') ?>" class="h-10 inline-flex items-center justify-center gap-2 border border-border/50 text-foreground font-medium text-sm rounded-lg hover:bg-muted transition">
                <?= icon('RotateCcw', 'h-4 w-4') ?>
                Reset
            </a>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid gap-6 md:grid-cols-3 mb-6">
    <!-- Total Expenses Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 bg-primary/10 border-b border-primary/20">
            <p class="text-xs font-medium text-primary uppercase mb-2">Total Biaya</p>
            <p class="text-3xl font-bold text-primary">Rp <?= number_format($total, 0, ',', '.') ?></p>
        </div>
        <div class="p-4 bg-muted/50 text-center">
            <p class="text-xs text-muted-foreground">
                <?= format_date($startDate) ?> hingga <?= format_date($endDate) ?>
            </p>
        </div>
    </div>

    <!-- Category Count Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 bg-success/10 border-b border-success/20">
            <p class="text-xs font-medium text-success uppercase mb-2">Kategori Digunakan</p>
            <p class="text-3xl font-bold text-success"><?= count($byCategory) ?></p>
        </div>
        <div class="p-4 bg-muted/50 text-center">
            <p class="text-xs text-muted-foreground">
                Dari total <?= count($categories) ?> kategori tersedia
            </p>
        </div>
    </div>

    <!-- Transaction Count Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 bg-warning/10 border-b border-warning/20">
            <p class="text-xs font-medium text-warning uppercase mb-2">Total Transaksi</p>
            <p class="text-3xl font-bold text-warning"><?= array_sum(array_column($byCategory, 'count')) ?></p>
        </div>
        <div class="p-4 bg-muted/50 text-center">
            <p class="text-xs text-muted-foreground">
                Jumlah pencatatan biaya
            </p>
        </div>
    </div>
</div>

<!-- Category Breakdown Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Layers', 'h-5 w-5 text-primary') ?>
            Biaya per Kategori
        </h2>
    </div>

    <div class="p-6">
        <?php if (empty($byCategory)): ?>
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-12 text-muted-foreground">
                <?= icon('BarChart3', 'h-12 w-12 mb-3 opacity-50') ?>
                <p class="text-sm">Tidak ada data biaya untuk periode ini.</p>
                <a href="<?= base_url('finance/expenses') ?>" class="mt-4 text-primary hover:underline text-sm font-medium">
                    Tambah biaya baru
                </a>
            </div>
        <?php else: ?>
            <div class="relative w-full overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/50 border-b border-border/50">
                        <tr>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Kategori</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-20">Transaksi</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-32">Total</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground flex-1">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <?php foreach ($byCategory as $item): ?>
                            <?php $percentage = $total > 0 ? ($item->total / $total) * 100 : 0; ?>
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-primary/10 text-primary font-medium text-sm">
                                        <?= $categories[$item->category] ?? $item->category ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium"><?= $item->count ?></td>
                                <td class="px-4 py-3 text-right font-bold text-primary">Rp <?= number_format($item->total, 0, ',', '.') ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <!-- Progress Bar -->
                                        <div class="w-full max-w-xs bg-muted/50 rounded-full h-2">
                                            <div class="bg-primary rounded-full h-2 transition-all" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                        <!-- Percentage Text -->
                                        <span class="text-sm font-semibold text-foreground w-12 text-right"><?= number_format($percentage, 1) ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-muted/30 border-t border-border/50">
                        <tr class="font-bold">
                            <td class="px-4 py-3">Total</td>
                            <td class="px-4 py-3 text-right"><?= array_sum(array_column($byCategory, 'count')) ?></td>
                            <td class="px-4 py-3 text-right text-primary text-base">Rp <?= number_format($total, 0, ',', '.') ?></td>
                            <td class="px-4 py-3 text-right">100.0%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
