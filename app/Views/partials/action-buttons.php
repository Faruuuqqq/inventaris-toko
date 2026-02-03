<?php
/**
 * Action Buttons Partial
 *
 * Displays edit and delete action buttons
 *
 * @param int|string $id Record ID
 * @param string $editUrl URL for edit action (optional)
 * @param string $deleteUrl URL for delete action (optional)
 * @param string $editModal Modal ID to open for edit (optional)
 * @param bool $showEdit Show edit button (default: true)
 * @param bool $showDelete Show delete button (default: true)
 * @param bool $showView Show view button (default: false)
 * @param string $viewUrl URL for view action (optional)
 * @param array $data Data to pass to modal (optional)
 */
$id = $id ?? '';
$editUrl = $editUrl ?? '';
$deleteUrl = $deleteUrl ?? '';
$editModal = $editModal ?? '';
$showEdit = $showEdit ?? true;
$showDelete = $showDelete ?? true;
$showView = $showView ?? false;
$viewUrl = $viewUrl ?? '';
$data = $data ?? [];
$dataJson = !empty($data) ? htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8') : '';
?>
<div class="flex gap-1">
    <?php if ($showView && $viewUrl): ?>
    <a href="<?= $viewUrl ?>" class="btn btn-ghost btn-icon btn-sm" title="Lihat">
        <?= icon('Eye', 'h-4 w-4') ?>
    </a>
    <?php endif; ?>

    <?php if ($showEdit): ?>
        <?php if ($editUrl): ?>
        <a href="<?= $editUrl ?>" class="btn btn-ghost btn-icon btn-sm" title="Edit">
            <?= icon('Edit', 'h-4 w-4') ?>
        </a>
        <?php elseif ($editModal): ?>
        <button
            class="btn btn-ghost btn-icon btn-sm"
            title="Edit"
            onclick="openEditModal('<?= $editModal ?>', '<?= $id ?>'<?= $dataJson ? ', ' . $dataJson : '' ?>)"
        >
            <?= icon('Edit', 'h-4 w-4') ?>
        </button>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($showDelete && $deleteUrl): ?>
    <button type="button" 
            class="btn btn-ghost btn-icon btn-sm text-destructive" 
            title="Hapus"
            onclick="ModalManager.submitDelete('<?= $deleteUrl ?>', '<?= addslashes($data['name'] ?? $data['item_name'] ?? 'item ini') ?>', () => { location.reload(); })">
        <?= icon('Trash2', 'h-4 w-4') ?>
    </button>
    <?php endif; ?>
</div>
