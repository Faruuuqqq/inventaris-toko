<?php
/**
 * Simple Card Component for LAN ERP
 * Simplified from 9 to 4 parameters
 *
 * Usage:
 * <?= view('components/card', [
 *     'title' => 'Card Title',
 *     'icon' => 'Package',  // optional
 *     'class' => 'custom-class',  // optional
 *     'content' => 'Card content here'
 * ]) ?>
 */

$title ??= '';
$icon ??= null;
$class ??= '';
$content ??= '';
?>

<div class="card <?= $class ?>">
    <?php if ($title || $icon): ?>
        <div class="card-header">
            <div class="flex items-center gap-3">
                <?php if ($icon): ?>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent text-accent-foreground">
                        <?= icon($icon, 'h-5 w-5') ?>
                    </div>
                <?php endif; ?>
                <?php if ($title): ?>
                    <h3 class="card-title"><?= $title ?></h3>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($content): ?>
        <div class="card-content">
            <?= $content ?>
        </div>
    <?php endif; ?>
</div>

