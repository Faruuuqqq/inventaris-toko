<?php
/**
 * Stat Card Component - KPI Statistics Display
 * Modern statistics card with icon, label, value, and trend
 * 
 * Usage:
 * <?= view('components/stat-card', [
 *     'label' => 'Total Sales',
 *     'value' => 'Rp 5.000.000',
 *     'icon' => 'TrendingUp',
 *     'trend' => 12.5,  // positive or negative
 *     'color' => 'primary',  // primary, success, warning, destructive
 *     'subtitle' => 'vs last month'
 * ]) ?>
 */

$label = $label ?? '';
$value = $value ?? '0';
$icon = $icon ?? 'Package';
$trend = $trend ?? null;
$color = $color ?? 'primary';
$subtitle = $subtitle ?? null;
$class = $class ?? '';

$colorClasses = [
    'primary' => 'bg-primary/10 text-primary',
    'success' => 'bg-success/10 text-success',
    'warning' => 'bg-warning/10 text-warning',
    'destructive' => 'bg-destructive/10 text-destructive',
    'secondary' => 'bg-secondary/10 text-secondary',
];

$colorClass = $colorClasses[$color] ?? 'bg-primary/10 text-primary';
?>

<div class="rounded-lg border border-border bg-card p-6 shadow-sm hover:shadow-md transition-shadow <?= $class ?>">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-muted-foreground"><?= esc($label) ?></p>
            <p class="mt-3 text-3xl font-bold text-foreground"><?= $value ?></p>
            
            <?php if ($trend !== null || $subtitle): ?>
                <div class="mt-3 flex items-center gap-2">
                    <?php if ($trend !== null): ?>
                        <div class="flex items-center gap-1">
                            <?php if ($trend > 0): ?>
                                <svg class="h-4 w-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-11 11M3 17h8m0 0V7m0 10l11-11"/>
                                </svg>
                                <span class="text-xs font-semibold text-success"><?= abs($trend) ?>%</span>
                            <?php else: ?>
                                <svg class="h-4 w-4 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-11-11m8 11h-8m0 0V7m0 10l11-11"/>
                                </svg>
                                <span class="text-xs font-semibold text-destructive"><?= abs($trend) ?>%</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($subtitle): ?>
                        <span class="text-xs text-muted-foreground"><?= esc($subtitle) ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="flex h-12 w-12 items-center justify-center rounded-lg <?= $colorClass ?>">
            <?= icon($icon, 'h-6 w-6') ?>
        </div>
    </div>
</div>
