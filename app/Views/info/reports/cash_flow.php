<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('DollarSign', 'h-8 w-8 text-primary') ?>
            Arus Kas (Cash Flow)
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Analisis inflow dan outflow kas untuk periode tertentu</p>
    </div>
    <a href="<?= base_url('/info/reports') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Filter Form -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Filter', 'h-5 w-5 text-primary') ?>
            Filter Periode
        </h2>
    </div>
    <div class="p-6">
        <form method="get" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="space-y-2 flex-1">
                <label for="start_date" class="text-sm font-medium text-foreground">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="<?= $startDate ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div class="space-y-2 flex-1">
                <label for="end_date" class="text-sm font-medium text-foreground">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="<?= $endDate ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition whitespace-nowrap">
                    <?= icon('Filter', 'h-5 w-5') ?>
                    Tampilkan
                </button>
                <a href="<?= base_url('/info/reports/cash-flow') ?>" class="inline-flex items-center justify-center gap-2 h-10 px-4 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
                    <?= icon('RefreshCw', 'h-5 w-5') ?>
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid gap-6 md:grid-cols-3 mb-8">
    <!-- Total Cash Inflows -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Inflows</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency(array_sum($cashInflows)) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-success/10">
                    <?= icon('TrendingUp', 'h-6 w-6 text-success') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Cash Outflows -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Outflows</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency(array_sum($cashOutflows)) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-destructive/10">
                    <?= icon('TrendingDown', 'h-6 w-6 text-destructive') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Cash Flow -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Net Cash Flow</p>
                    <p class="text-2xl font-bold <?= $netCashFlow >= 0 ? 'text-success' : 'text-destructive' ?> mt-2">
                        <?= format_currency($netCashFlow) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg <?= $netCashFlow >= 0 ? 'bg-success/10' : 'bg-destructive/10' ?>">
                    <?= icon('DollarSign', 'h-6 w-6 ' . ($netCashFlow >= 0 ? 'text-success' : 'text-destructive')) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cash Inflows & Outflows -->
<div class="grid gap-6 lg:grid-cols-2 mb-8">
    <!-- Cash Inflows -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-success/5">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('TrendingUp', 'h-5 w-5 text-success') ?>
                Cash Inflows
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($cashInflows as $type => $amount): ?>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-foreground"><?= $type ?></span>
                        <span class="font-semibold text-foreground"><?= format_currency($amount) ?></span>
                    </div>
                    <div class="h-2 bg-muted rounded-full overflow-hidden">
                        <div class="h-full bg-success" style="width: <?= array_sum($cashInflows) > 0 ? ($amount/array_sum($cashInflows)*100) : 0 ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="border-t border-border/50 pt-4 mt-4">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-foreground">Total Inflows</span>
                    <span class="text-lg font-bold text-success"><?= format_currency(array_sum($cashInflows)) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Outflows -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-destructive/5">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('TrendingDown', 'h-5 w-5 text-destructive') ?>
                Cash Outflows
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($cashOutflows as $type => $amount): ?>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-foreground"><?= $type ?></span>
                        <span class="font-semibold text-foreground"><?= format_currency($amount) ?></span>
                    </div>
                    <div class="h-2 bg-muted rounded-full overflow-hidden">
                        <div class="h-full bg-destructive" style="width: <?= array_sum($cashOutflows) > 0 ? ($amount/array_sum($cashOutflows)*100) : 0 ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="border-t border-border/50 pt-4 mt-4">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-foreground">Total Outflows</span>
                    <span class="text-lg font-bold text-destructive"><?= format_currency(array_sum($cashOutflows)) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cash Flow Summary -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('FileText', 'h-5 w-5 text-primary') ?>
            Ringkasan Arus Kas
        </h2>
    </div>
    <div class="p-6 space-y-6">
        <!-- Summary Grid -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="space-y-1 p-4 rounded-lg bg-success/10 border border-success/20">
                <p class="text-xs text-muted-foreground font-semibold">Total Inflows</p>
                <p class="text-2xl font-bold text-success"><?= format_currency(array_sum($cashInflows)) ?></p>
            </div>
            <div class="space-y-1 p-4 rounded-lg bg-destructive/10 border border-destructive/20">
                <p class="text-xs text-muted-foreground font-semibold">Total Outflows</p>
                <p class="text-2xl font-bold text-destructive"><?= format_currency(array_sum($cashOutflows)) ?></p>
            </div>
            <div class="space-y-1 p-4 rounded-lg <?= $netCashFlow >= 0 ? 'bg-primary/10 border border-primary/20' : 'bg-destructive/10 border border-destructive/20' ?>">
                <p class="text-xs text-muted-foreground font-semibold">Net Cash Flow</p>
                <p class="text-2xl font-bold <?= $netCashFlow >= 0 ? 'text-primary' : 'text-destructive' ?>">
                    <?= format_currency($netCashFlow) ?>
                </p>
            </div>
        </div>

        <!-- Period Info & Status -->
        <div class="border-t border-border/50 pt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-sm text-muted-foreground">
                        Periode: <span class="font-semibold text-foreground"><?= format_date($startDate) ?> - <?= format_date($endDate) ?></span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-muted-foreground">Status Arus Kas:</span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold <?= $netCashFlow >= 0 ? 'bg-success/20 text-success' : 'bg-destructive/20 text-destructive' ?>">
                        <?= icon($netCashFlow >= 0 ? 'TrendingUp' : 'TrendingDown', 'h-4 w-4') ?>
                        <?= $netCashFlow >= 0 ? 'Positive' : 'Negative' ?>
                    </span>
                </div>
            </div>

            <!-- Inflow vs Outflow Ratio -->
            <div class="mt-6 space-y-2">
                <p class="text-sm font-medium text-foreground">Perbandingan Inflow vs Outflow</p>
                <div class="h-3 bg-muted rounded-full overflow-hidden flex">
                    <?php
                    $total = array_sum($cashInflows) + array_sum($cashOutflows);
                    $inflowPercentage = $total > 0 ? (array_sum($cashInflows)/$total*100) : 50;
                    ?>
                    <div class="h-full bg-success transition-all" style="width: <?= $inflowPercentage ?>%"></div>
                    <div class="h-full bg-destructive transition-all" style="width: <?= 100 - $inflowPercentage ?>%"></div>
                </div>
                <div class="flex justify-between text-xs text-muted-foreground mt-2">
                    <span>Inflows: <?= round($inflowPercentage, 1) ?>%</span>
                    <span>Outflows: <?= round(100 - $inflowPercentage, 1) ?>%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>