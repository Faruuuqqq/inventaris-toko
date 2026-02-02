<?php
/**
 * Textarea Component - Multi-line text input
 * 
 * Usage:
 * <?= view('components/textarea', [
 *     'name' => 'notes',
 *     'label' => 'Notes',
 *     'placeholder' => 'Enter notes here...',
 *     'rows' => 5
 * ]) ?>
 */

$name = $name ?? '';
$label = $label ?? '';
$placeholder = $placeholder ?? '';
$value = $value ?? old($name) ?? '';
$rows = $rows ?? 4;
$required = $required ?? false;
$disabled = $disabled ?? false;
$error = $error ?? '';
$hint = $hint ?? '';
$class = $class ?? '';

$textareaClass = 'flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50';

if ($error) {
    $textareaClass .= ' border-destructive';
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
    
    <textarea
        name="<?= $name ?>"
        id="<?= $name ?>"
        rows="<?= $rows ?>"
        class="<?= $textareaClass ?>"
        placeholder="<?= esc($placeholder) ?>"
        <?= $required ? 'required' : '' ?>
        <?= $disabled ? 'disabled' : '' ?>><?= esc($value) ?></textarea>
    
    <?php if ($error): ?>
        <p class="text-sm text-destructive"><?= esc($error) ?></p>
    <?php elseif ($hint): ?>
        <p class="text-sm text-muted-foreground"><?= esc($hint) ?></p>
    <?php endif; ?>
</div>
