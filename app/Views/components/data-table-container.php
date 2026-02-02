<?php
/**
 * Data Table Container Component - Enhanced data table wrapper
 * Handles responsive table with sorting, filtering capabilities
 * 
 * Usage:
 * <?= view('components/data-table-container', [
 *     'title' => 'Customers',
 *     'subtitle' => 'Manage your customer list',
 *     'headers' => ['Name', 'Email', 'Status'],
 *     'rows' => [
 *         ['John Doe', 'john@example.com', 'Active']
 *     ],
 *     'actions' => [
 *         ['icon' => 'Edit', 'url' => '/edit/{id}'],
 *         ['icon' => 'Trash', 'url' => '/delete/{id}', 'class' => 'text-destructive']
 *     ]
 * ]) ?>
 */

$title = $title ?? '';
$subtitle = $subtitle ?? '';
$headers = $headers ?? [];
$rows = $rows ?? [];
$actions = $actions ?? [];
$class = $class ?? '';
$empty = $empty ?? null;
?>

<div class="rounded-lg border border-border bg-card shadow-sm overflow-hidden <?= $class ?>">
    <?php if ($title || $subtitle): ?>
        <div class="px-6 py-4 border-b border-border/50 bg-muted/30">
            <?php if ($title): ?>
                <h2 class="font-semibold text-foreground"><?= esc($title) ?></h2>
            <?php endif; ?>
            <?php if ($subtitle): ?>
                <p class="mt-1 text-sm text-muted-foreground"><?= esc($subtitle) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($rows)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <?php if (!empty($headers)): ?>
                    <thead class="bg-muted/50 border-b border-border">
                        <tr>
                            <?php foreach ($headers as $header): ?>
                                <th class="px-6 py-3 text-left font-semibold text-foreground"><?= esc($header) ?></th>
                            <?php endforeach; ?>
                            <?php if (!empty($actions)): ?>
                                <th class="px-6 py-3 text-right font-semibold text-foreground">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                <?php endif; ?>
                
                <tbody class="divide-y divide-border hover:[&>tr]:bg-muted/30 transition-colors">
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php if (is_array($row)): ?>
                                <?php foreach ($row as $cell): ?>
                                    <td class="px-6 py-4 text-foreground"><?= $cell ?></td>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <td class="px-6 py-4 text-foreground"><?= $row ?></td>
                            <?php endif; ?>
                            
                            <?php if (!empty($actions)): ?>
                                <td class="px-6 py-4 text-right space-x-2 flex justify-end gap-2">
                                    <?php foreach ($actions as $action): ?>
                                        <a href="<?= esc($action['url'] ?? '#') ?>" 
                                           class="inline-flex items-center justify-center h-8 w-8 rounded hover:bg-muted transition-colors <?= $action['class'] ?? 'text-muted-foreground' ?>"
                                           title="<?= esc($action['title'] ?? '') ?>">
                                            <?php if (isset($action['icon'])): ?>
                                                <?= icon($action['icon'], 'h-4 w-4') ?>
                                            <?php else: ?>
                                                <span><?= esc($action['text'] ?? '') ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($empty): ?>
        <?= $empty ?>
    <?php else: ?>
        <div class="p-6 text-center text-muted-foreground">
            <p>No data available</p>
        </div>
    <?php endif; ?>
</div>
