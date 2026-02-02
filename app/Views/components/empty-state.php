<?php
/**
 * Empty State Component - Display when no data available
 * 
 * Usage:
 * <?= view('components/empty-state', [
 *     'icon' => 'Package',
 *     'title' => 'No Data',
 *     'description' => 'Start by creating your first item',
 *     'action' => ['text' => 'Create New', 'url' => '/create']
 * ]) ?>
 */

$icon = $icon ?? 'Package';
$title = $title ?? 'No Data';
$description = $description ?? '';
$action = $action ?? null;
$class = $class ?? '';
?>

<div class="rounded-lg border border-dashed border-border bg-muted/30 py-12 px-6 text-center <?= $class ?>">
    <div class="flex justify-center mb-4">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-muted text-muted-foreground">
            <?= icon($icon, 'h-8 w-8') ?>
        </div>
    </div>
    
    <h3 class="text-lg font-semibold text-foreground"><?= esc($title) ?></h3>
    
    <?php if ($description): ?>
        <p class="mt-2 text-sm text-muted-foreground max-w-sm mx-auto"><?= esc($description) ?></p>
    <?php endif; ?>
    
    <?php if ($action): ?>
        <div class="mt-6">
            <a href="<?= esc($action['url'] ?? '#') ?>" 
               class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-lg bg-primary text-primary-foreground hover:opacity-90 transition-opacity">
                <?php if (isset($action['icon'])): ?>
                    <span><?= icon($action['icon'], 'h-4 w-4') ?></span>
                <?php endif; ?>
                <span><?= esc($action['text'] ?? 'Create') ?></span>
            </a>
        </div>
    <?php endif; ?>
</div>
