<table class="w-full caption-bottom text-sm">
    <?php if (isset($caption)): ?>
        <caption class="mt-4 text-sm text-muted-foreground"><?= $caption ?></caption>
    <?php endif; ?>

    <?php if (isset($header)): ?>
        <thead class="[&_tr]:border-b">
            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                <?= $header ?>
            </tr>
        </thead>
    <?php endif; ?>

    <?php if (isset($body)): ?>
        <tbody class="[&_tr:last-child]:border-0">
            <?= $body ?>
        </tbody>
    <?php endif; ?>

    <?php if (isset($footer)): ?>
        <tfoot class="border-t bg-muted/50 font-medium [&>tr]:last:border-b-0">
            <?= $footer ?>
        </tfoot>
    <?php endif; ?>
</table>