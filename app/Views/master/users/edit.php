<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-foreground">Edit Pengguna</h2>
        <p class="mt-1 text-muted-foreground">Perbarui data pengguna sistem</p>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl border border-border/50 bg-surface p-6">
        <form action="<?= base_url('master/users/' . $pengguna->id) ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <?= method_field('PUT') ?>
            
            <!-- Row 1: Username & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="username">Username *</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        required 
                        value="<?= esc($pengguna->username) ?>"
                        placeholder="Contoh: admin123"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="email">Email *</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        value="<?= esc($pengguna->email) ?>"
                        placeholder="Contoh: admin@toko.com"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
            </div>

            <!-- Row 2: Fullname & Role -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="fullname">Nama Lengkap *</label>
                    <input 
                        type="text" 
                        name="fullname" 
                        id="fullname" 
                        required 
                        value="<?= esc($pengguna->fullname) ?>"
                        placeholder="Contoh: Adminisrator"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="role">Role *</label>
                    <select 
                        name="role" 
                        id="role" 
                        required 
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                        <option value="">-- Pilih Role --</option>
                        <option value="OWNER" <?= $pengguna->role === 'OWNER' ? 'selected' : '' ?>>Owner</option>
                        <option value="ADMIN" <?= $pengguna->role === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                        <option value="GUDANG" <?= $pengguna->role === 'GUDANG' ? 'selected' : '' ?>>Gudang</option>
                        <option value="SALES" <?= $pengguna->role === 'SALES' ? 'selected' : '' ?>>Sales</option>
                    </select>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-foreground" for="password">Password (Biarkan kosong jika tidak ingin mengubah)</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="Masukkan password baru (opsional)"
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                >
            </div>

            <!-- Form Footer -->
            <div class="flex gap-3 justify-end pt-4 border-t border-border/50">
                <a href="<?= base_url('master/users') ?>" class="inline-flex items-center justify-center rounded-lg border border-border bg-muted/30 text-foreground hover:bg-muted transition h-10 px-6 gap-2 text-sm font-semibold">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-6 gap-2 text-sm font-semibold">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
