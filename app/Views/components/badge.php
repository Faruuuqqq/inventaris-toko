<?php
/**
 * Badge Component - Status Indicators
 * 
 * Usage:
 * <?= view('components/badge', [
 *     'text' => 'Paid',
 *     'variant' => 'success',  // success, destructive, warning, primary, secondary
 *     'icon' => 'CheckCircle',
 *     'animated' => true
 * ]) ?>
 */

$text = $text ?? '';
$variant = $variant ?? 'secondary';
$icon = $icon ?? null;
$animated = $animated ?? false;
$class = $class ?? '';

$variantClasses = [
    'success' => 'badge-success',
    'destructive' => 'badge-destructive',
    'warning' => 'badge-warning',
    'primary' => 'badge-primary',
    'secondary' => 'badge-secondary'
];

$badgeClass = $variantClasses[$variant] ?? 'badge-secondary';
?>

<span class="badge <?= $badgeClass ?> <?= $class ?>">
    <?php if ($icon): ?>
        <span class="flex items-center <?= $animated ? 'badge-dot' : '' ?>">
            <?= icon($icon, 'h-3 w-3') ?>
        </span>
    <?php endif; ?>
    <span><?= $text ?></span>
</span>
