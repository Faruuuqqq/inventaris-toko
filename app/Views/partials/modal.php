<?php
/**
 * Modal Partial
 *
 * Reusable modal component
 *
 * @param string $id Modal ID
 * @param string $title Modal title
 * @param string $content Modal body content (rendered from section)
 * @param string $formAction Form action URL (optional)
 * @param string $formMethod Form method (default: post)
 * @param string $submitText Submit button text (default: Simpan)
 * @param string $size Modal size: sm, md, lg, xl (default: md)
 */
$id = $id ?? 'modal';
$title = $title ?? '';
$formAction = $formAction ?? '';
$formMethod = $formMethod ?? 'post';
$submitText = $submitText ?? 'Simpan';
$size = $size ?? 'md';

$sizeClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
?>
<div id="<?= $id ?>" class="modal hidden">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full <?= $sizeClass ?>">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-xl font-semibold"><?= esc($title) ?></h3>
        </div>
        <div class="p-6 pt-0">
            <?php if ($formAction): ?>
            <form action="<?= $formAction ?>" method="<?= $formMethod ?>" class="space-y-4">
                <?= csrf_field() ?>
            <?php endif; ?>

                <?= $slot ?? '' ?>

            <?php if ($formAction): ?>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('<?= $id ?>').classList.add('hidden')">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <?= esc($submitText) ?>
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
