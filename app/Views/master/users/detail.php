<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('UserPlus', 'h-8 w-8 text-primary') ?>
            Detail Pengguna
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi detail pengguna sistem</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= base_url('master/users') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            <?= icon('ChevronLeft', 'h-5 w-5') ?>
            Kembali
        </a>
        <?php if (is_admin()): ?>
        <a href="<?= base_url('master/users/edit/' . $pengguna->id) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
            <?= icon('Edit', 'h-5 w-5') ?>
            Edit
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: User Details (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-xl border border-border/50 bg-surface overflow-hidden">
            <!-- Header Section -->
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('User', 'h-5 w-5 text-primary') ?>
                    Informasi Pengguna
                </h2>
            </div>

            <!-- Content Section -->
            <div class="p-6 space-y-6">
                <!-- Full Name -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Lengkap</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= esc($pengguna->fullname) ?></p>
                </div>

                <!-- Username & Email -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Username</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= esc($pengguna->username) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Email</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= esc($pengguna->email) ?></p>
                    </div>
                </div>

                <!-- Role & Status -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Role</p>
                        <div class="mt-1">
                            <?php
                            $roleColors = [
                                'OWNER' => 'bg-red/10 text-red',
                                'ADMIN' => 'bg-primary/10 text-primary',
                                'GUDANG' => 'bg-warning/10 text-warning',
                                'SALES' => 'bg-success/10 text-success',
                            ];
                            $roleColor = $roleColors[$pengguna->role] ?? 'bg-muted/10 text-muted-foreground';
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $roleColor ?>">
                                <?= esc($pengguna->role) ?>
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status</p>
                        <div class="mt-1">
                            <?php if ($pengguna->is_active): ?>
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
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Dibuat Pada</p>
                        <p class="text-sm font-medium text-foreground mt-1">
                            <?php if ($pengguna->created_at): ?>
                                <?= date('d M Y H:i', strtotime($pengguna->created_at)) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Login Terakhir</p>
                        <p class="text-sm font-medium text-foreground mt-1">
                            <?php if ($pengguna->last_login): ?>
                                <?= date('d M Y H:i', strtotime($pengguna->last_login)) ?>
                            <?php else: ?>
                                Belum pernah login
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Actions -->
    <div class="space-y-6">
        <div class="rounded-xl border border-border/50 bg-surface p-6">
            <h3 class="text-sm font-semibold text-foreground mb-4">Aksi</h3>
            
            <div class="space-y-2">
                <a href="<?= base_url('master/users') ?>" class="w-full h-10 rounded-lg border border-border/50 text-foreground font-medium flex items-center justify-center hover:bg-muted transition">
                    <?= icon('ArrowLeft', 'h-4 w-4 mr-2') ?>
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
