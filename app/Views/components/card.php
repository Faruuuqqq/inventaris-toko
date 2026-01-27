<div class="<?= $class ?? '' ?> rounded-lg border bg-card text-card-foreground shadow-sm">
    <?php if (isset($header)): ?>
        <div class="flex flex-col space-y-1.5 p-6">
            <?php if (isset($title)): ?>
                <h3 class="text-2xl font-semibold leading-none tracking-tight"><?= $title ?></h3>
            <?php endif; ?>
            <?php if (isset($description)): ?>
                <p class="text-sm text-muted-foreground"><?= $description ?></p>
            <?php endif; ?>
            <?= $header ?>
        </div>
    <?php endif; ?>

    <?php if (isset($content) || isset($slot)): ?>
        <div class="p-6 pt-0">
            <?= $content ?? $slot ?>
        </div>
    <?php endif; ?>

    <?php if (isset($footer)): ?>
        <div class="flex items-center p-6 pt-0">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>