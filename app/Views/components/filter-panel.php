<?php
/**
 * Filter Panel Component - Reusable filter section
 * 
 * Usage:
 * <?= view('components/filter-panel', [
 *     'title' => 'Filter Results',
 *     'fields' => [
 *         ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['pending', 'completed']],
 *         ['name' => 'date_from', 'label' => 'From Date', 'type' => 'date']
 *     ],
 *     'action' => '/search'
 * ]) ?>
 */

$title = $title ?? 'Filters';
$fields = $fields ?? [];
$action = $action ?? '#';
$method = $method ?? 'GET';
$class = $class ?? '';
$submitText = $submitText ?? 'Apply Filters';
$resetText = $resetText ?? 'Reset';
?>

<div class="rounded-lg border border-border bg-card shadow-sm overflow-hidden <?= $class ?>">
    <div class="px-6 py-4 border-b border-border/50 bg-muted/30">
        <h3 class="font-semibold text-foreground"><?= esc($title) ?></h3>
    </div>
    
    <form method="<?= $method ?>" action="<?= esc($action) ?>" class="p-6 space-y-4">
        <?php foreach ($fields as $field): ?>
            <div class="space-y-2">
                <?php if (isset($field['label'])): ?>
                    <label for="<?= esc($field['name']) ?>" class="text-sm font-medium text-foreground">
                        <?= esc($field['label']) ?>
                    </label>
                <?php endif; ?>
                
                <?php if ($field['type'] === 'select'): ?>
                    <select name="<?= esc($field['name']) ?>" 
                            id="<?= esc($field['name']) ?>"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        <option value="">-- Pilih --</option>
                        <?php foreach ($field['options'] ?? [] as $value => $label): ?>
                            <option value="<?= esc(is_numeric($value) ? $label : $value) ?>">
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                
                <?php elseif ($field['type'] === 'text'): ?>
                    <input type="text" 
                           name="<?= esc($field['name']) ?>"
                           id="<?= esc($field['name']) ?>"
                           class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                           placeholder="<?= esc($field['placeholder'] ?? '') ?>">
                
                <?php elseif ($field['type'] === 'date'): ?>
                    <input type="date" 
                           name="<?= esc($field['name']) ?>"
                           id="<?= esc($field['name']) ?>"
                           class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                
                <?php elseif ($field['type'] === 'number'): ?>
                    <input type="number" 
                           name="<?= esc($field['name']) ?>"
                           id="<?= esc($field['name']) ?>"
                           class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                           placeholder="<?= esc($field['placeholder'] ?? '') ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <div class="flex gap-3 pt-4">
            <button type="submit" class="flex-1 h-10 rounded-lg bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity">
                <?= esc($submitText) ?>
            </button>
            <button type="reset" class="flex-1 h-10 rounded-lg border border-border text-foreground font-medium hover:bg-muted transition-colors">
                <?= esc($resetText) ?>
            </button>
        </div>
    </form>
</div>
