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
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8" stroke-width="2"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35"/>
        </svg>
        <?= $filterText ?>
    </button>
    <button onclick="<?= $resetFn ?>()" class="btn btn-outline">
        <?= $resetText ?>
    </button>
    <?php if ($showExport): ?>
    <button onclick="<?= $exportFn ?>()" class="btn btn-outline ml-auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4 M7 10l5 5 5-5 M12 15V3"/>
        </svg>
        <?= $exportText ?>
    </button>
    <?php endif; ?>
</div>
