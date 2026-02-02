<?php
/**
 * Page Header Component - Consistent page title and actions
 * 
 * Usage:
 * <?= view('components/page-header', [
 *     'title' => 'Page Title',
 *     'subtitle' => 'Optional subtitle',
 *     'icon' => 'Package',
 *     'actions' => [
 *         ['text' => 'Add New', 'url' => '/create', 'icon' => 'Plus']
 *     ]
 * ]) ?>
 */

$title = $title ?? '';
$subtitle = $subtitle ?? null;
$icon = $icon ?? null;
$actions = $actions ?? [];
$class = $class ?? '';
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 <?= $class ?>">
    <div class="flex items-start gap-4">
        <?php if ($icon): ?>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
                <?= icon($icon, 'h-6 w-6') ?>
            </div>
        <?php endif; ?>
        
        <div>
            <h1 class="text-3xl font-bold text-foreground"><?= esc($title) ?></h1>
            <?php if ($subtitle): ?>
                <p class="mt-1 text-sm text-muted-foreground"><?= esc($subtitle) ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($actions)): ?>
        <div class="flex flex-wrap gap-3">
            <?php foreach ($actions as $action): ?>
                <a href="<?= esc($action['url'] ?? '#') ?>" 
                   class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-lg <?= $action['variant'] ?? 'bg-primary text-primary-foreground' ?> hover:opacity-90 transition-opacity">
                    <?php if (isset($action['icon'])): ?>
                        <span><?= icon($action['icon'], 'h-4 w-4') ?></span>
                    <?php endif; ?>
                    <span><?= esc($action['text'] ?? '') ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
