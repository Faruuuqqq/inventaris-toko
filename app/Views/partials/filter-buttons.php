<?php
/**
 * Filter Action Buttons Partial
 *
 * Usage: <?= view('partials/filter-buttons', ['filterFn' => 'loadSales', 'resetFn' => 'resetFilters']) ?>
 *
 * Parameters:
 * - filterFn: JavaScript function name for filter action (default: 'loadData')
 * - resetFn: JavaScript function name for reset action (default: 'resetFilters')
 * - exportFn: JavaScript function name for export action (default: 'exportData')
 * - showExport: Whether to show export button (default: true)
 * - filterText: Text for filter button (default: 'Filter')
 * - resetText: Text for reset button (default: 'Reset')
 * - exportText: Text for export button (default: 'Export')
 */

$filterFn = $filterFn ?? 'loadData';
$resetFn = $resetFn ?? 'resetFilters';
$exportFn = $exportFn ?? 'exportData';
$showExport = $showExport ?? true;
$filterText = $filterText ?? 'Filter';
$resetText = $resetText ?? 'Reset';
$exportText = $exportText ?? 'Export';
?>
<div class="flex gap-2 mt-4">
    <button onclick="<?= $filterFn ?>()" class="btn btn-primary">
        <?= icon('Search', 'w-4 h-4') ?>
        <?= $filterText ?>
    </button>
    <button onclick="<?= $resetFn ?>()" class="btn btn-outline">
        <?= $resetText ?>
    </button>
    <?php if ($showExport): ?>
    <button onclick="<?= $exportFn ?>()" class="btn btn-outline ml-auto">
        <?= icon('Download', 'w-4 h-4') ?>
        <?= $exportText ?>
    </button>
    <?php endif; ?>
</div>
