<?php
/**
 * Date Range Filter Partial
 *
 * Usage: <?= view('partials/filter-date-range', ['startId' => 'startDate', 'endId' => 'endDate']) ?>
 *
 * Parameters:
 * - startId: ID for start date input (default: 'startDate')
 * - endId: ID for end date input (default: 'endDate')
 * - startLabel: Label for start date (default: 'Tanggal Mulai')
 * - endLabel: Label for end date (default: 'Tanggal Akhir')
 * - startName: Name attribute for start date (optional)
 * - endName: Name attribute for end date (optional)
 */

$startId = $startId ?? 'startDate';
$endId = $endId ?? 'endDate';
$startLabel = $startLabel ?? 'Tanggal Mulai';
$endLabel = $endLabel ?? 'Tanggal Akhir';
$startName = $startName ?? $startId;
$endName = $endName ?? $endId;
?>
<div class="space-y-2">
    <label for="<?= $startId ?>"><?= $startLabel ?></label>
    <input type="date" id="<?= $startId ?>" name="<?= $startName ?>" class="form-input">
</div>
<div class="space-y-2">
    <label for="<?= $endId ?>"><?= $endLabel ?></label>
    <input type="date" id="<?= $endId ?>" name="<?= $endName ?>" class="form-input">
</div>
