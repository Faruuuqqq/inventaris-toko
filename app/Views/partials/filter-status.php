<?php
/**
 * Filter Status Select Partial
 *
 * Usage:
 * <?= view('partials/filter-status', [
 *     'id' => 'statusFilter',
 *     'type' => 'payment'
 * ]) ?>
 *
 * Parameters:
 * - id: Element ID (default: 'statusFilter')
 * - type: Status type - 'payment', 'order', 'return', 'expense' (default: 'payment')
 * - label: Custom label (optional)
 * - selected: Currently selected value (optional)
 */

$id = $id ?? 'statusFilter';
$name = $name ?? $id;
$type = $type ?? 'payment';
$label = $label ?? 'Status';
$selected = $selected ?? '';

// Define status options by type
$statusOptions = [
    'payment' => [
        'PAID' => 'Lunas',
        'UNPAID' => 'Belum Lunas',
        'PARTIAL' => 'Sebagian'
    ],
    'order' => [
        'Dipesan' => 'Dipesan',
        'Sebagian' => 'Sebagian Diterima',
        'Diterima Semua' => 'Diterima Semua',
        'Dibatalkan' => 'Dibatalkan'
    ],
    'return' => [
        'Pending' => 'Pending',
        'Disetujui' => 'Disetujui',
        'Ditolak' => 'Ditolak'
    ],
    'expense' => [
        'OPERASIONAL' => 'Operasional',
        'TRANSPORTASI' => 'Transportasi',
        'LISTRIK' => 'Listrik & Air',
        'TELEPON' => 'Telepon & Internet',
        'GAJI' => 'Gaji Karyawan',
        'SEWA' => 'Sewa',
        'PERBAIKAN' => 'Perbaikan',
        'ATK' => 'ATK',
        'LAINNYA' => 'Lainnya'
    ]
];

$options = $statusOptions[$type] ?? $statusOptions['payment'];
?>
<div class="space-y-2">
    <label for="<?= $id ?>"><?= $label ?></label>
    <select id="<?= $id ?>" name="<?= $name ?>" class="form-input">
        <option value="">Semua</option>
        <?php foreach ($options as $value => $text): ?>
            <option value="<?= $value ?>" <?= ($value == $selected) ? 'selected' : '' ?>><?= $text ?></option>
        <?php endforeach; ?>
    </select>
</div>
