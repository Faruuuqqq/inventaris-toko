<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="{ isEditDialogOpen: false, isEditSubmitting: false, editErrors: {}, editingSalesperson: <?= json_encode($sales) ?> }">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
         <div>
             <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
                 <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                 </svg>
                 Detail Sales
             </h1>
             <p class="text-sm text-muted-foreground mt-1">Informasi detail salesperson</p>
         </div>
         <div class="flex gap-3">
             <a href="<?= base_url('master/salespersons') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                 <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                 </svg>
                 Kembali
             </a>
             <?php if (is_admin()): ?>
             <button @click="isEditDialogOpen = true" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                 <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                 </svg>
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
            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Informasi Sales
        </h2>
    </div>

    <!-- Content Section -->
    <div class="p-6 space-y-6">
        <!-- Salesperson Name -->
        <div>
            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Sales</p>
            <p class="text-2xl font-bold text-foreground mt-2"><?= esc($sales->name) ?></p>
        </div>

        <!-- Phone & Status -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nomor Telepon</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= esc($sales->phone ?? '-') ?></p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status</p>
                <div class="mt-1">
                    <?php if ($sales->is_active): ?>
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
        </div>

        <!-- Timestamps -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Bergabung Sejak</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($sales->created_at): ?>
                        <?= date('d M Y H:i', strtotime($sales->created_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Diperbarui</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($sales->updated_at): ?>
                        <?= date('d M Y H:i', strtotime($sales->updated_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Edit Salesperson Modal -->
    <div 
        x-show="isEditDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-md rounded-xl border border-border/50 bg-surface shadow-xl"
            @click.away="isEditDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Modal Header -->
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground">Edit Salesperson</h2>
                <button 
                    @click="isEditDialogOpen = false"
                    class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
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
                        ModalManager.success('Salesperson berhasil diperbarui', () => {
                            isEditDialogOpen = false;
                            window.location.reload();
                        });
                    } else if (response.status === 422) {
                        const data = await response.json();
                        if (data.errors) editErrors = data.errors;
                        ModalManager.error(data.message || 'Terjadi kesalahan validasi.');
                    } else {
                        const data = await response.json();
                        ModalManager.error(data.message || 'Gagal memperbarui data.');
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    ModalManager.error('Terjadi kesalahan: ' + error.message);
                } finally {
                    isEditSubmitting = false;
                }
            }" :action="`<?= base_url('master/salespersons') ?>/${editingSalesperson.id}`" method="POST" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <!-- Nama -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_name">Nama Salesperson *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="edit_name" 
                        required 
                        x-model="editingSalesperson.name"
                        :class="{'border-destructive': editErrors.name}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                    >
                    <span x-show="editErrors.name" class="text-destructive text-xs mt-1" x-text="editErrors.name"></span>
                </div>

                <!-- Telepon -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_phone">No. Telepon</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="edit_phone" 
                        x-model="editingSalesperson.phone"
                        :class="{'border-destructive': editErrors.phone}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                    >
                    <span x-show="editErrors.phone" class="text-destructive text-xs mt-1" x-text="editErrors.phone"></span>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_email">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="edit_email" 
                        x-model="editingSalesperson.email"
                        :class="{'border-destructive': editErrors.email}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple/50 transition-all"
                    >
                    <span x-show="editErrors.email" class="text-destructive text-xs mt-1" x-text="editErrors.email"></span>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
                    <button 
                        type="button" 
                        @click="isEditDialogOpen = false" 
                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-6 text-sm font-semibold"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isEditSubmitting"
                        class="inline-flex items-center justify-center rounded-lg bg-purple text-white hover:bg-purple-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isEditSubmitting" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span x-show="isEditSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isEditSubmitting ? 'Menyimpan...' : 'Update Salesperson'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
 </div>

<?= $this->endSection() ?>
