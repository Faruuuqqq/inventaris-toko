<?php
/**
 * Stat Card Component Partial
 *
 * Usage:
 * <?= view('partials/stat-card', [
 *     'label' => 'Total Produk',
 *     'value' => $totalProducts,
 *     'icon' => 'Package',
 *     'color' => 'primary'
 * ]) ?>
 *
 * Parameters:
 * - label: Stat label (required)
 * - value: Stat value (required)
 * - icon: Lucide icon name (optional)
 * - color: Icon color class - primary, success, warning, destructive (default: 'primary')
 * - format: Value format - 'number', 'currency', 'none' (default: 'none')
 * - change: Percentage change value (optional)
 * - changeType: 'up' or 'down' (optional)
 */

$label = $label ?? 'Label';
$value = $value ?? 0;
$icon = $icon ?? '';
$color = $color ?? 'primary';
$format = $format ?? 'none';
$change = $change ?? null;
$changeType = $changeType ?? 'up';

// Format value
$displayValue = $value;
if ($format === 'currency') {
    $displayValue = 'Rp ' . number_format((float)$value, 0, ',', '.');
} elseif ($format === 'number') {
    $displayValue = number_format((float)$value, 0, ',', '.');
}

// Color classes
$colorClasses = [
    'primary' => 'text-primary',
    'success' => 'text-success',
    'warning' => 'text-warning',
    'destructive' => 'text-destructive',
    'muted' => 'text-muted-foreground'
];
$colorClass = $colorClasses[$color] ?? $colorClasses['primary'];
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-4">
        <div class="flex items-center justify-between">
            <p class="text-sm text-muted-foreground"><?= esc($label) ?></p>
            <?php if ($icon): ?>
                <?= icon($icon, 'h-4 w-4 ' . $colorClass) ?>
            <?php endif; ?>
        </div>
        <p class="text-2xl font-bold <?= $colorClass ?>"><?= $displayValue ?></p>
        <?php if ($change !== null): ?>
            <p class="text-xs <?= $changeType === 'up' ? 'text-success' : 'text-destructive' ?> flex items-center gap-1 mt-1">
                <?php if ($changeType === 'up'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6"/></svg>
                <?php else: ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                <?php endif; ?>
                <?= $change ?>%
            </p>
        <?php endif; ?>
    </div>
</div>
