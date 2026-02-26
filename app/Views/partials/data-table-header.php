<?php
/**
 * Data Table Header Partial
 *
 * Displays a header with title, subtitle and optional add button
 *
 * @param string $title     Main title
 * @param string $subtitle  Subtitle/description
 * @param string $addButton Button text (optional)
 * @param string $modalId   Modal ID to open on click (optional)
 */
$title ??= '';
$subtitle ??= '';
$addButton ??= '';
$modalId ??= '';
$addUrl ??= '';
?>
<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="text-xl font-semibold"><?= esc($title) ?></h3>
        <?php if ($subtitle): ?>
        <p class="text-sm text-muted-foreground"><?= esc($subtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php if ($addButton): ?>
    <button
        class="btn btn-primary"
        <?php if ($modalId): ?>
        x-on:click="$dispatch('open-modal', '<?= esc($modalId, 'js') ?>')"
        <?php elseif ($addUrl): ?>
        x-on:click="window.location.href='<?= esc($addUrl, 'js') ?>'"
        <?php endif; ?>
    >
        <?= icon('Plus', 'h-4 w-4') ?>
        <?= esc($addButton) ?>
    </button>
    <?php endif; ?>
</div>
