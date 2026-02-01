<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h3 class="page-title"><?= $title ?></h3>
            <p class="text-muted"><?= $subtitle ?? '' ?></p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('/finance/expenses') ?>" class="btn btn-outline-secondary">
                <i data-lucide="arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= base_url('/finance/expenses/summary') ?>" method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Biaya</h5>
                    <h2><?= format_currency($total) ?></h2>
                    <p class="mb-0">Periode: <?= format_date($startDate) ?> - <?= format_date($endDate) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Jumlah Kategori</h5>
                    <h2><?= count($byCategory) ?></h2>
                    <p class="mb-0">Kategori biaya terpakai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- By Category Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Biaya per Kategori</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($byCategory)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($byCategory as $item): ?>
                                <?php $percentage = $total > 0 ? ($item->total / $total) * 100 : 0; ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary"><?= $categories[$item->category] ?? $item->category ?></span>
                                    </td>
                                    <td class="text-center"><?= $item->count ?></td>
                                    <td class="text-end fw-bold"><?= format_currency($item->total) ?></td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width: 100px; height: 8px;">
                                                <div class="progress-bar" style="width: <?= $percentage ?>%"></div>
                                            </div>
                                            <span><?= number_format($percentage, 1) ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th class="text-center"><?= array_sum(array_column($byCategory, 'count')) ?></th>
                            <th class="text-end"><?= format_currency($total) ?></th>
                            <th class="text-end">100%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
