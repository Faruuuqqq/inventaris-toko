<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="editManager()" class="max-w-2xl">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-foreground">Edit Sales</h2>
        <p class="mt-1 text-muted-foreground">Perbarui data salesperson</p>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl border border-border/50 bg-surface p-6">
        <form @submit.prevent="submitForm" action="<?= base_url('master/salespersons/' . $sales->id) ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            
            <!-- Name & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="name">Nama Sales *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        value="<?= esc($sales->name) ?>"
                        placeholder="Contoh: Budi Santoso"
                        :class="{'border-destructive': errors.name}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.name" class="text-destructive text-xs mt-1" x-text="errors.name"></span>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="phone">No. Telepon</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        value="<?= esc($sales->phone ?? '') ?>"
                        placeholder="Contoh: 081234567890"
                        :class="{'border-destructive': errors.phone}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.phone" class="text-destructive text-xs mt-1" x-text="errors.phone"></span>
                </div>
            </div>

            <!-- Form Footer -->
            <div class="flex gap-3 justify-end pt-4 border-t border-border/50">
                <a href="<?= base_url('master/salespersons') ?>" class="inline-flex items-center justify-center rounded-lg border border-border bg-muted/30 text-foreground hover:bg-muted transition h-10 px-6 gap-2 text-sm font-semibold">
                    <?= icon('X', 'h-5 w-5') ?>
                    Batal
                </a>
                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-6 gap-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <?= icon('Check', 'h-5 w-5') ?>
                    <span x-show="isSubmitting" class="inline-flex items-center gap-2">
                        <span class="animate-spin">⚙️</span>
                    </span>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editManager() {
    return {
        isSubmitting: false,
        errors: {},

        async submitForm(event) {
            event.preventDefault();
            const form = event.target;
            
            // Clear previous errors
            this.errors = {};
            this.isSubmitting = true;

            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok || response.status === 201) {
                    // Success
                    ModalManager.success('Data salesperson berhasil diperbarui', () => {
                        // Redirect back to list
                        window.location.href = `<?= base_url('master/salespersons') ?>`;
                    });
                } else if (response.status === 422) {
                    // Validation error
                    const data = await response.json();
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    ModalManager.error(data.message || 'Terjadi kesalahan validasi. Silakan periksa kembali data Anda.');
                } else {
                    // Other error
                    const data = await response.json();
                    ModalManager.error(data.message || 'Gagal menyimpan data. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                ModalManager.error('Terjadi kesalahan: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}
</script>

<?= $this->endSection() ?>
