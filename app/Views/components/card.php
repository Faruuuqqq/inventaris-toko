<?php
/**
 * Modern Card Component
 * Professional design with optional header, icon, and action buttons
 * 
 * Usage:
 * <?= view('components/card', [
 *     'title' => 'Card Title',
 *     'description' => 'Optional description',
 *     'icon' => 'Package',
 *     'class' => 'custom-class',
 *     'action' => '<button>Action</button>',
 *     'content' => 'Card content here'
 * ]) ?>
 */

$title = $title ?? '';
$description = $description ?? '';
$icon = $icon ?? null;
$class = $class ?? '';
$action = $action ?? null;
$content = $content ?? ($slot ?? '');
$header = $header ?? null;
$footer = $footer ?? null;
?>

<div class="card <?= $class ?>">
    <?php if ($header || $title || $icon || $action): ?>
        <div class="card-header">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    <?php if ($icon): ?>
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent text-accent-foreground">
                            <?= icon($icon, 'h-5 w-5') ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <?php if ($title): ?>
                            <h3 class="card-title"><?= $title ?></h3>
                        <?php endif; ?>
                        <?php if ($description): ?>
                            <p class="text-xs text-muted-foreground mt-1"><?= $description ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($action): ?>
                    <div class="flex items-center gap-2">
                        <?= $action ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($header): ?>
                <?= $header ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($content): ?>
        <div class="card-content">
            <?= $content ?>
        </div>
    <?php endif; ?>

    <?php if ($footer): ?>
        <div class="border-t border-border px-6 py-4">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>
