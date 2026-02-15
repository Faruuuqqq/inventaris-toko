<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-foreground">Tambah Gudang</h2>
        <p class="mt-1 text-muted-foreground">Tambahkan data gudang penyimpanan baru</p>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl border border-border/50 bg-surface p-6">
        <form action="<?= base_url('master/warehouses') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>
            
            <!-- Row 1: Code & Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="code">Kode Gudang *</label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code" 
                        required 
                        value="<?= old('code') ?>"
                        placeholder="Contoh: GD001"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="name">Nama Gudang *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        value="<?= old('name') ?>"
                        placeholder="Contoh: Gudang Pusat"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
            </div>

            <!-- Address -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-foreground" for="address">Alamat Gudang</label>
                <textarea 
                    name="address" 
                    id="address" 
                    placeholder="Contoh: Jl. Warehousing No. 100, Jakarta Utara"
                    rows="3"
                    class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all resize-none"
                ><?= old('address') ?></textarea>
            </div>

            <!-- Form Footer -->
            <div class="flex gap-3 justify-end pt-4 border-t border-border/50">
                <a href="<?= base_url('master/warehouses') ?>" class="inline-flex items-center justify-center rounded-lg border border-border bg-muted/30 text-foreground hover:bg-muted transition h-10 px-6 gap-2 text-sm font-semibold">
                    <?= icon('X', 'h-5 w-5') ?>
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-6 gap-2 text-sm font-semibold">
                    <?= icon('Plus', 'h-5 w-5') ?>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
