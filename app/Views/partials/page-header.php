<?php
/**
 * Page Header Partial
 *
 * Usage: <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle]) ?>
 *
 * Parameters:
 * - title: Page title (required)
 * - subtitle: Page subtitle (optional)
 */

$title = $title ?? 'Page Title';
$subtitle = $subtitle ?? '';
?>
<div class="row mb-4">
    <div class="col">
        <h3 class="page-title"><?= esc($title) ?></h3>
        <?php if ($subtitle): ?>
            <p class="text-muted"><?= esc($subtitle) ?></p>
        <?php endif; ?>
    </div>
</div>
