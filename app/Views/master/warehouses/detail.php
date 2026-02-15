<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="{ isEditDialogOpen: false, isEditSubmitting: false, editErrors: {}, editingWarehouse: <?= json_encode($gudang) ?> }">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
                <?= icon('Package', 'h-8 w-8 text-primary') ?>
                Detail Gudang
            </h1>
            <p class="text-sm text-muted-foreground mt-1">Informasi detail gudang penyimpanan</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= base_url('master/warehouses') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                <?= icon('ChevronLeft', 'h-5 w-5') ?>
                Kembali
            </a>
            <?php if (is_admin()): ?>
            <button @click="isEditDialogOpen = true" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                <?= icon('Edit', 'h-5 w-5') ?>
                Edit
            </button>
            <?php endif; ?>
        </div>
    </div>

<!-- Main Content -->
<div class="rounded-xl border border-border/50 bg-surface overflow-hidden">
    <!-- Header Section -->
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Building', 'h-5 w-5 text-primary') ?>
            Informasi Gudang
        </h2>
    </div>

    <!-- Content Section -->
    <div class="p-6 space-y-6">
        <!-- Warehouse Name -->
        <div>
            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Gudang</p>
            <p class="text-2xl font-bold text-foreground mt-2"><?= esc($gudang->name) ?></p>
        </div>

        <!-- Warehouse Code & Address -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Kode Gudang</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= esc($gudang->code) ?></p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status</p>
                <div class="mt-1">
                    <?php if ($gudang->is_active): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-success/10 text-success">
                        ✓ Aktif
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-muted/50 text-muted-foreground">
                        Tidak Aktif
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="md:col-span-2">
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Alamat Lengkap</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= esc($gudang->address ?? '-') ?></p>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Dibuat Pada</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($gudang->created_at): ?>
                        <?= date('d M Y H:i', strtotime($gudang->created_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Diperbarui</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($gudang->updated_at): ?>
                        <?= date('d M Y H:i', strtotime($gudang->updated_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Edit Modal -->
    <div x-show="isEditDialogOpen" class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center" @click.away="isEditDialogOpen = false" style="display: none;">
        <div class="bg-surface rounded-xl shadow-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 p-6 border-b border-border/50 bg-muted/30 flex items-center justify-between">
                <h2 class="text-lg font-bold text-foreground">Edit Gudang</h2>
                <button @click="isEditDialogOpen = false" class="text-muted-foreground hover:text-foreground transition">
                    <?= icon('X', 'h-5 w-5') ?>
                </button>
            </div>

            <!-- Modal Content -->
            <form @submit.prevent="async (e) => {
                e.preventDefault();
                const form = e.target;
                editErrors = {};
                isEditSubmitting = true;

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok || response.status === 200) {
                        ModalManager.success('Gudang berhasil diperbarui', () => {
                            isEditDialogOpen = false;
                            window.location.reload();
                        });
                    } else if (response.status === 422) {
                        const data = await response.json();
                        if (data.errors) editErrors = data.errors;
                        ModalManager.error(data.message || 'Terjadi kesalahan validasi.');
                    } else {
                        const data = await response.json();
                        ModalManager.error(data.message || 'Gagal memperbarui gudang.');
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    ModalManager.error('Terjadi kesalahan: ' + error.message);
                } finally {
                    isEditSubmitting = false;
                }
            }" :action="`<?= base_url('master/warehouses') ?>/${editingWarehouse.id}`" class="p-6 space-y-4">

                <!-- Name Field -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nama Gudang</label>
                    <input type="text" name="name" x-model="editingWarehouse.name" class="w-full px-4 py-2 rounded-lg border border-border bg-surface text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50" required>
                    <span x-show="editErrors.name" x-text="editErrors.name" class="text-xs text-destructive mt-1 block"></span>
                </div>

                <!-- Code Field -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kode Gudang</label>
                    <input type="text" name="code" x-model="editingWarehouse.code" class="w-full px-4 py-2 rounded-lg border border-border bg-surface text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50" required>
                    <span x-show="editErrors.code" x-text="editErrors.code" class="text-xs text-destructive mt-1 block"></span>
                </div>

                <!-- Address Field -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Alamat Lengkap</label>
                    <textarea name="address" x-model="editingWarehouse.address" rows="3" class="w-full px-4 py-2 rounded-lg border border-border bg-surface text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"></textarea>
                    <span x-show="editErrors.address" x-text="editErrors.address" class="text-xs text-destructive mt-1 block"></span>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="button" @click="isEditDialogOpen = false" class="flex-1 h-10 px-4 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                        Batal
                    </button>
                    <button type="submit" :disabled="isEditSubmitting" x-text="isEditSubmitting ? 'Menyimpan...' : 'Update Gudang'" class="flex-1 h-10 px-4 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
