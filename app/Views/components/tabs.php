<?php
/**
 * Tabs Component - Tab navigation and content switching
 * Alpine.js powered for smooth tab switching
 * 
 * Usage:
 * <?= view('components/tabs', [
 *     'tabs' => [
 *         ['id' => 'tab1', 'label' => 'Overview', 'content' => '...'],
 *         ['id' => 'tab2', 'label' => 'Details', 'content' => '...']
 *     ],
 *     'default' => 'tab1'
 * ]) ?>
 */

$tabs = $tabs ?? [];
$default = $default ?? (isset($tabs[0]) ? $tabs[0]['id'] : '');
$class = $class ?? '';
?>

<div x-data="{ active: '<?= esc($default) ?>' }" class="<?= $class ?>">
    <!-- Tab Navigation -->
    <div class="flex gap-1 border-b border-border bg-muted/30 rounded-t-lg overflow-auto">
        <?php foreach ($tabs as $tab): ?>
            <button
                @click="active = '<?= esc($tab['id']) ?>'"
                :class="active === '<?= esc($tab['id']) ?>' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-foreground'"
                class="px-4 py-3 font-medium text-sm whitespace-nowrap transition-colors border-b-2 border-transparent">
                <?php if (isset($tab['icon'])): ?>
                    <span class="inline-flex items-center gap-2">
                        <?= icon($tab['icon'], 'h-4 w-4') ?>
                        <span><?= esc($tab['label']) ?></span>
                    </span>
                <?php else: ?>
                    <?= esc($tab['label']) ?>
                <?php endif; ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <!-- Tab Content -->
    <div class="rounded-b-lg border border-t-0 border-border bg-card">
        <?php foreach ($tabs as $tab): ?>
            <div x-show="active === '<?= esc($tab['id']) ?>'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="p-6">
                <?= $tab['content'] ?? '' ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
