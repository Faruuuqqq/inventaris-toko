<?php
/**
 * Input Component
 * 
 * Usage: <?= view('components/input', ['name' => 'email', 'type' => 'email', 'label' => 'Email']) ?>
 * 
 * @param string $name - Input name (required)
 * @param string $type - text, email, password, number, date, etc (default: text)
 * @param string $label - Label text
 * @param string $placeholder - Placeholder text
 * @param string $value - Default value
 * @param bool $required - Is required (default: false)
 * @param string $error - Error message to display
 * @param string $hint - Help text
 */

$name = $name ?? '';
$type = $type ?? 'text';
$label = $label ?? '';
$placeholder = $placeholder ?? '';
$value = $value ?? old($name) ?? '';
$required = $required ?? false;
$error = $error ?? '';
$hint = $hint ?? '';
$disabled = $disabled ?? false;
$class = $class ?? '';

$inputClass = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50';

if ($error) {
    $inputClass .= ' border-destructive';
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
    
    <input
        type="<?= $type ?>"
        name="<?= $name ?>"
        id="<?= $name ?>"
        class="<?= $inputClass ?>"
        placeholder="<?= esc($placeholder) ?>"
        value="<?= esc($value) ?>"
        <?= $required ? 'required' : '' ?>
        <?= $disabled ? 'disabled' : '' ?>
    >
    
    <?php if ($error): ?>
        <p class="text-sm text-destructive"><?= esc($error) ?></p>
    <?php elseif ($hint): ?>
        <p class="text-sm text-muted-foreground"><?= esc($hint) ?></p>
    <?php endif; ?>
</div>
