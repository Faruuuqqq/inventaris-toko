<?php
/**
 * Filter Select Dropdown Partial
 *
 * Usage:
 * <?= view('partials/filter-select', [
 *     'id' => 'customerFilter',
 *     'label' => 'Customer',
 *     'placeholder' => 'Semua Customer',
 *     'options' => $customers,
 *     'valueKey' => 'id',
 *     'labelKey' => 'name'
 * ]) ?>
 *
 * Parameters:
 * - id: Element ID (required)
 * - label: Label text (required)
 * - placeholder: Placeholder/default option text (default: 'Semua')
 * - options: Array of options (required)
 * - valueKey: Key for option value (default: 'id')
 * - labelKey: Key for option label (default: 'name')
 * - name: Name attribute (default: same as id)
 * - selected: Currently selected value (optional)
 */

$id = $id ?? '';
$name = $name ?? $id;
$label = $label ?? 'Select';
$placeholder = $placeholder ?? 'Semua';
$options = $options ?? [];
$valueKey = $valueKey ?? 'id';
$labelKey = $labelKey ?? 'name';
$selected = $selected ?? '';
?>
<div class="space-y-2">
    <label for="<?= $id ?>"><?= $label ?></label>
    <select id="<?= $id ?>" name="<?= $name ?>" class="form-input">
        <option value=""><?= $placeholder ?></option>
        <?php foreach ($options as $option): ?>
            <?php
            $value = $option->$valueKey ?? $option[$valueKey] ?? '';
            $text = $option->$labelKey ?? $option[$labelKey] ?? '';
            $isSelected = ($value == $selected) ? 'selected' : '';
            ?>
            <option value="<?= $value ?>" <?= $isSelected ?>><?= esc($text) ?></option>
        <?php endforeach; ?>
    </select>
</div>
