<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle ?? '']) ?>

    <!-- Add User Button -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold">Daftar Pengguna</h3>
        <?php if (session()->get('role') === 'OWNER'): ?>
            <button onclick="openModal()" class="btn btn-primary">
                <?= icon('Plus', 'h-4 w-4 mr-2') ?>
                Tambah Pengguna
            </button>
        <?php endif; ?>
    </div>

    <!-- Users Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted-foreground">
                                Belum ada pengguna
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="font-medium"><?= esc($user['username']) ?></td>
                                <td><?= esc($user['fullname']) ?></td>
                                <td><?= esc($user['email'] ?? '-') ?></td>
                                <td>
                                    <span class="badge badge-secondary"><?= esc($user['role']) ?></span>
                                </td>
                                <td><?= format_date($user['created_at'] ?? '') ?></td>
                                <td class="text-right">
                                    <?php if (session()->get('role') === 'OWNER' && $user['id'] != session()->get('user_id')): ?>
                                        <div class="flex gap-1 justify-end">
                                            <button onclick="editUser(<?= $user['id'] ?>)" class="btn btn-ghost btn-icon btn-sm" title="Edit">
                                                <?= icon('Edit', 'h-4 w-4') ?>
                                            </button>
                                            <button onclick="deleteUser(<?= $user['id'] ?>)" class="btn btn-ghost btn-icon btn-sm text-destructive" title="Hapus">
                                                <?= icon('Trash2', 'h-4 w-4') ?>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="modal hidden">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full max-w-lg">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-xl font-semibold" id="modalTitle">Tambah Pengguna</h3>
        </div>
        <div class="p-6 pt-0">
            <form id="userForm" method="POST" action="/master/users/store">
                <?= csrf_field() ?>
                <input type="hidden" id="userId" name="id">

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Username</label>
                        <input type="text" id="username" name="username" class="form-input" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Nama Lengkap</label>
                        <input type="text" id="fullname" name="fullname" class="form-input" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Role</label>
                        <select id="role" name="role" class="form-input" required>
                            <option value="ADMIN">ADMIN</option>
                            <option value="GUDANG">GUDANG</option>
                            <option value="SALES">SALES</option>
                            <?php if (session()->get('role') === 'OWNER'): ?>
                                <option value="OWNER">OWNER</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Password</label>
                        <input type="password" id="password" name="password" class="form-input">
                        <p class="text-xs text-muted-foreground">Kosongkan jika tidak ingin mengubah password</p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal()" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const usersData = <?= json_encode($users) ?>;

    function openModal(userId = null) {
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userForm');
        const title = document.getElementById('modalTitle');
        const passwordInput = document.getElementById('password');

        form.reset();
        document.getElementById('userId').value = '';

        if (userId) {
            const user = usersData.find(u => u.id === userId);
            if (user) {
                title.textContent = 'Edit Pengguna';
                document.getElementById('userId').value = user.id;
                document.getElementById('username').value = user.username;
                document.getElementById('email').value = user.email || '';
                document.getElementById('fullname').value = user.fullname;
                document.getElementById('role').value = user.role;
                form.action = `/master/users/update/${user.id}`;
                passwordInput.required = false;
                passwordInput.placeholder = 'Kosongkan jika tidak ingin mengubah';
            }
        } else {
            title.textContent = 'Tambah Pengguna';
            form.action = '/master/users/store';
            passwordInput.required = true;
            passwordInput.placeholder = '';
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function editUser(id) {
        openModal(id);
    }

    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/master/users/delete/${id}`;

            const csrfInput = document.querySelector('input[name="csrf_test_name"]');
            if (csrfInput) {
                const csrfClone = csrfInput.cloneNode(true);
                form.appendChild(csrfClone);
            }

            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modal on outside click
    document.getElementById('userModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<?= $this->endSection() ?>
