<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('TrendingDown', 'h-8 w-8 text-primary') ?>
            Laporan Laba & Rugi
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Analisis profit & loss statement untuk periode tertentu</p>
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
                <a href="<?= base_url('/info/reports/profit-loss') ?>" class="inline-flex items-center justify-center gap-2 h-10 px-4 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
                    <?= icon('RefreshCw', 'h-5 w-5') ?>
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Revenue Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Revenue</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($revenue) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-success/10">
                    <?= icon('TrendingUp', 'h-6 w-6 text-success') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- COGS Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Cost of Goods Sold</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($cogs) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-destructive/10">
                    <?= icon('TrendingDown', 'h-6 w-6 text-destructive') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Returns Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Returns</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency($returns) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-warning/10">
                    <?= icon('RotateCcw', 'h-6 w-6 text-warning') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Gross Profit Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Gross Profit</p>
                    <p class="text-2xl font-bold <?= $grossProfit >= 0 ? 'text-success' : 'text-destructive' ?> mt-2">
                        <?= format_currency($grossProfit) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg <?= $grossProfit >= 0 ? 'bg-success/10' : 'bg-destructive/10' ?>">
                    <?= icon('DollarSign', 'h-6 w-6 ' . ($grossProfit >= 0 ? 'text-success' : 'text-destructive')) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue & Costs Breakdown -->
<div class="grid gap-6 lg:grid-cols-2 mb-8">
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('ShoppingCart', 'h-5 w-5 text-primary') ?>
                Revenue & Costs
            </h2>
        </div>
        <div class="p-6 space-y-6">
            <!-- Revenue -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-foreground">Revenue</span>
                    <span class="font-semibold text-foreground"><?= format_currency($revenue) ?></span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full bg-success" style="width: <?= $revenue > 0 ? 100 : 0 ?>%"></div>
                </div>
            </div>

            <!-- COGS -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-foreground">Cost of Goods Sold</span>
                    <span class="font-semibold text-foreground"><?= format_currency($cogs) ?></span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full bg-destructive" style="width: <?= $revenue > 0 ? ($cogs/$revenue*100) : 0 ?>%"></div>
                </div>
            </div>

            <!-- Returns -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-foreground">Returns</span>
                    <span class="font-semibold text-foreground"><?= format_currency($returns) ?></span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full bg-warning" style="width: <?= $revenue > 0 ? ($returns/$revenue*100) : 0 ?>%"></div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-border/50"></div>

            <!-- Gross Profit -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-foreground font-semibold">Gross Profit</span>
                    <span class="font-bold text-lg <?= $grossProfit >= 0 ? 'text-success' : 'text-destructive' ?>">
                        <?= format_currency($grossProfit) ?>
                    </span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full <?= $grossProfit >= 0 ? 'bg-success' : 'bg-destructive' ?>" style="width: <?= $revenue > 0 ? abs($grossProfit/$revenue*100) : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses & Net Profit -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('DollarSign', 'h-5 w-5 text-primary') ?>
                Expenses & Net Profit
            </h2>
        </div>
        <div class="p-6 space-y-6">
            <!-- Gross Profit -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-foreground">Gross Profit</span>
                    <span class="font-semibold text-foreground"><?= format_currency($grossProfit) ?></span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full bg-success" style="width: <?= $grossProfit >= 0 ? 100 : 0 ?>%"></div>
                </div>
            </div>

            <!-- Expenses -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-foreground">Expenses</span>
                    <span class="font-semibold text-foreground"><?= format_currency($expenses) ?></span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full bg-destructive" style="width: <?= $grossProfit > 0 ? ($expenses/$grossProfit*100) : 0 ?>%"></div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-border/50"></div>

            <!-- Net Profit -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-foreground font-semibold">Net Profit</span>
                    <span class="font-bold text-lg <?= $netProfit >= 0 ? 'text-success' : 'text-destructive' ?>">
                        <?= format_currency($netProfit) ?>
                    </span>
                </div>
                <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full <?= $netProfit >= 0 ? 'bg-success' : 'bg-destructive' ?>" style="width: <?= $grossProfit > 0 ? abs($netProfit/$grossProfit*100) : 0 ?>%"></div>
                </div>
            </div>

            <!-- Metrics -->
            <div class="grid grid-cols-2 gap-4 pt-4">
                <div class="space-y-1 p-3 rounded-lg bg-muted/30">
                    <p class="text-xs text-muted-foreground font-semibold">Profit Margin</p>
                    <p class="text-lg font-bold <?= $revenue > 0 && ($netProfit/$revenue*100) >= 0 ? 'text-success' : 'text-destructive' ?>">
                        <?= $revenue > 0 ? round($netProfit/$revenue*100, 2) : 0 ?>%
                    </p>
                </div>
                <div class="space-y-1 p-3 rounded-lg bg-muted/30">
                    <p class="text-xs text-muted-foreground font-semibold">Gross Margin</p>
                    <p class="text-lg font-bold <?= $revenue > 0 && ($grossProfit/$revenue*100) >= 0 ? 'text-success' : 'text-destructive' ?>">
                        <?= $revenue > 0 ? round($grossProfit/$revenue*100, 2) : 0 ?>%
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>