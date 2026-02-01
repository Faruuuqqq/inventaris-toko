<?php
/**
 * Card Component Partial
 *
 * Usage:
 * <?= view('partials/card', [
 *     'title' => 'Card Title',
 *     'content' => '<p>Card content here</p>'
 * ]) ?>
 *
 * Parameters:
 * - title: Card title (optional)
 * - subtitle: Card subtitle (optional)
 * - content: Card body content (required for simple usage)
 * - class: Additional CSS classes (optional)
 * - headerClass: Additional header CSS classes (optional)
 * - bodyClass: Additional body CSS classes (optional)
 */

$title = $title ?? '';
$subtitle = $subtitle ?? '';
$content = $content ?? '';
$class = $class ?? '';
$headerClass = $headerClass ?? '';
$bodyClass = $bodyClass ?? '';
?>
<div class="rounded-lg border bg-card text-card-foreground shadow-sm <?= $class ?>">
    <?php if ($title): ?>
    <div class="p-6 <?= $headerClass ?>">
        <h3 class="text-xl font-semibold"><?= esc($title) ?></h3>
        <?php if ($subtitle): ?>
            <p class="text-sm text-muted-foreground"><?= esc($subtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="<?= $bodyClass ?: 'p-6' ?>">
        <?= $content ?>
    </div>
</div>
