<?php
/**
 * Select Component - Enhanced select input
 * 
 * Usage:
 * <?= view('components/select', [
 *     'name' => 'status',
 *     'label' => 'Status',
 *     'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
 *     'value' => 'active'
 * ]) ?>
 */

$name = $name ?? '';
$label = $label ?? '';
$options = $options ?? [];
$value = $value ?? old($name) ?? '';
$required = $required ?? false;
$disabled = $disabled ?? false;
$error = $error ?? '';
$placeholder = $placeholder ?? '-- Select --';
$hint = $hint ?? '';
$class = $class ?? '';

$selectClass = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50';

if ($error) {
    $selectClass .= ' border-destructive';
}
?>

<div class="space-y-2 <?= $class ?>">
    <?php if ($label): ?>
        <label for="<?= $name ?>" class="text-sm font-medium leading-none">
            <?= esc($label) ?>
            <?php if ($required): ?>
                <span style="color: var(--destructive);">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    
    <select
        name="<?= $name ?>"
        id="<?= $name ?>"
        class="<?= $selectClass ?>"
        <?= $required ? 'required' : '' ?>
        <?= $disabled ? 'disabled' : '' ?>>
        
        <option value=""><?= esc($placeholder) ?></option>
        
        <?php foreach ($options as $optionValue => $optionLabel): ?>
            <option value="<?= esc($optionValue) ?>" <?= $value == $optionValue ? 'selected' : '' ?>>
                <?= esc($optionLabel) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <?php if ($error): ?>
        <p class="text-sm text-destructive"><?= esc($error) ?></p>
    <?php elseif ($hint): ?>
        <p class="text-sm text-muted-foreground"><?= esc($hint) ?></p>
    <?php endif; ?>
</div>
