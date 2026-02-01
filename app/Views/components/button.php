<?php
/**
 * Modern Button Component
 * Versatile button with multiple variants and sizes
 * 
 * Usage:
 * <?= view('components/button', [
 *     'text' => 'Click Me',
 *     'variant' => 'primary',  // primary, secondary, destructive, outline, ghost, link
 *     'size' => 'md',  // sm, md, lg, icon
 *     'disabled' => false,
 *     'loading' => false,
 *     'icon' => 'Plus'
 * ]) ?>
 */

$text = $text ?? '';
$variant = $variant ?? 'primary';
$size = $size ?? 'md';
$disabled = $disabled ?? false;
$loading = $loading ?? false;
$icon = $icon ?? null;
$iconPosition = $iconPosition ?? 'left';
$type = $type ?? 'button';
$class = $class ?? '';
$attributes = $attributes ?? '';

$variants = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'destructive' => 'btn-destructive',
    'outline' => 'btn-outline',
    'ghost' => 'btn-ghost',
    'link' => 'btn-link',
];

$sizes = [
    'sm' => 'btn-sm',
    'md' => '',
    'lg' => 'btn-lg',
    'icon' => 'btn-icon',
];

$variantClass = $variants[$variant] ?? 'btn-primary';
$sizeClass = $sizes[$size] ?? '';

$buttonClass = trim("btn {$variantClass} {$sizeClass} {$class}");

if ($disabled || $loading) {
    $attributes .= ' disabled';
}
?>

<button type="<?= $type ?>" 
        class="<?= $buttonClass ?>" 
        <?= $attributes ?>>
    <?php if ($loading): ?>
        <span class="animate-spin">
            <?= icon('Loader', 'h-4 w-4') ?>
        </span>
    <?php elseif ($icon && $iconPosition === 'left'): ?>
        <span><?= icon($icon, 'h-4 w-4') ?></span>
    <?php endif; ?>
    
    <?php if ($text): ?>
        <span><?= $text ?></span>
    <?php endif; ?>
    
    <?php if ($icon && $iconPosition === 'right'): ?>
        <span><?= icon($icon, 'h-4 w-4') ?></span>
    <?php endif; ?>
</button>
