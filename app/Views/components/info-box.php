<?php
/**
 * Info Box Component - Information display with icon
 * 
 * Usage:
 * <?= view('components/info-box', [
 *     'icon' => 'Info',
 *     'title' => 'Note',
 *     'content' => 'Important information here',
 *     'type' => 'info'  // info, success, warning, error
 * ]) ?>
 */

$icon = $icon ?? 'Info';
$title = $title ?? '';
$content = $content ?? '';
$type = $type ?? 'info';
$class = $class ?? '';

$typeClasses = [
    'info' => 'bg-primary/10 border-primary/30 text-primary',
    'success' => 'bg-success/10 border-success/30 text-success',
    'warning' => 'bg-warning/10 border-warning/30 text-warning',
    'error' => 'bg-destructive/10 border-destructive/30 text-destructive',
];

$typeClass = $typeClasses[$type] ?? $typeClasses['info'];
?>

<div class="rounded-lg border <?= $typeClass ?> p-4 <?= $class ?>">
    <div class="flex gap-3">
        <div class="flex-shrink-0 flex h-5 w-5 items-center justify-center">
            <?= icon($icon, 'h-5 w-5') ?>
        </div>
        <div class="flex-1">
            <?php if ($title): ?>
                <h4 class="font-semibold text-sm"><?= esc($title) ?></h4>
            <?php endif; ?>
            <?php if ($content): ?>
                <p class="text-sm mt-1 opacity-90"><?= esc($content) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
