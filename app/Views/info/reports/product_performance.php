<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/design-system.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8" x-data="{ includeHidden: '<?= $includeHidden ?>' }">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground mb-2"><?= esc($title) ?></h1>
        <p class="text-sm text-muted">
            Period: <?= esc($startDate) ?> to <?= esc($endDate) ?>
        </p>
    </div>

    <div class="mb-6 flex gap-4 items-center">
        <a href="<?= site_url('info/reports') ?>" class="text-sm text-primary hover:underline">
            ← Back to Reports Dashboard
        </a>

        <?php if ($isOwner): ?>
        <div class="flex items-center gap-2 ml-auto">
            <label class="text-sm text-foreground">Include Hidden:</label>
            <input type="checkbox" x-model="includeHidden" class="rounded border-gray-300">
        </div>
        <?php endif; ?>
    </div>

    <div class="card p-6">
        <?php if (empty($products)): ?>
            <div class="text-center py-8">
                <p class="text-muted">No product performance data found for the selected period.</p>
            </div>
        <?php else: ?>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-border">
                        <th class="px-4 py-3 font-semibold text-foreground">SKU</th>
                        <th class="px-4 py-3 font-semibold text-foreground">Product Name</th>
                        <th class="px-4 py-3 font-semibold text-foreground">Category</th>
                        <th class="px-4 py-3 font-semibold text-foreground text-right">Total Sold</th>
                        <th class="px-4 py-3 font-semibold text-foreground text-right">Total Revenue</th>
                        <th class="px-4 py-3 font-semibold text-foreground text-right">Avg Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr class="border-b border-border hover:bg-muted/50">
                        <td class="px-4 py-3 text-foreground"><?= esc($product->sku) ?></td>
                        <td class="px-4 py-3 text-foreground"><?= esc($product->name) ?></td>
                        <td class="px-4 py-3 text-foreground"><?= esc($product->category_name ?? '-') ?></td>
                        <td class="px-4 py-3 text-foreground text-right"><?= number_format($product->total_sold, 0) ?></td>
                        <td class="px-4 py-3 text-foreground text-right">Rp <?= number_format($product->total_revenue, 0, ',', '.') ?></td>
                        <td class="px-4 py-3 text-foreground text-right">Rp <?= number_format($product->avg_price, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
