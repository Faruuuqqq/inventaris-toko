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
        <?php if (empty($customers)): ?>
            <div class="text-center py-8">
                <p class="text-muted">No customer analysis data found for the selected period.</p>
            </div>
        <?php else: ?>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-border">
                        <th class="px-4 py-3 font-semibold text-foreground">Customer Name</th>
                        <th class="px-4 py-3 font-semibold text-foreground">Phone</th>
                        <th class="px-4 py-3 font-semibold text-foreground">Email</th>
                        <th class="px-4 py-3 font-semibold text-foreground text-right">Total Orders</th>
                        <th class="px-4 py-3 font-semibold text-foreground text-right">Total Spent</th>
                        <th class="px-4 py-3 font-semibold text-foreground text-right">Avg Order Value</th>
                        <th class="px-4 py-3 font-semibold text-foreground">Last Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr class="border-b border-border hover:bg-muted/50">
                        <td class="px-4 py-3 text-foreground font-semibold"><?= esc($customer->name) ?></td>
                        <td class="px-4 py-3 text-foreground"><?= esc($customer->phone ?? '-') ?></td>
                        <td class="px-4 py-3 text-foreground"><?= esc($customer->email ?? '-') ?></td>
                        <td class="px-4 py-3 text-foreground text-right"><?= number_format($customer->total_orders, 0) ?></td>
                        <td class="px-4 py-3 text-foreground text-right">Rp <?= number_format($customer->total_spent, 0, ',', '.') ?></td>
                        <td class="px-4 py-3 text-foreground text-right">Rp <?= number_format($customer->avg_order_value, 0, ',', '.') ?></td>
                        <td class="px-4 py-3 text-foreground">
                            <?= $customer->last_order_date ? date('Y-m-d', strtotime($customer->last_order_date)) : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
