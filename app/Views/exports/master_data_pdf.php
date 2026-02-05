<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
        }

        .page-break {
            page-break-after: always;
        }

        /* Header Section */
        .pdf-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4285f4;
        }

        .company-logo {
            max-width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: <?= $config['headerFontSize'] ?>pt;
            font-weight: bold;
            color: #4285f4;
            margin-bottom: 3px;
        }

        .report-title {
            font-size: <?= $config['titleFontSize'] ?>pt;
            font-weight: bold;
            color: #333;
            margin-top: 8px;
            margin-bottom: 3px;
        }

        .report-meta {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }

        .meta-item {
            display: inline-block;
            margin-right: 20px;
        }

        /* Filters Section */
        .filters-section {
            background-color: #f5f5f5;
            padding: 8px 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-size: 9pt;
        }

        .filter-label {
            font-weight: bold;
            color: #333;
        }

        /* Table Section */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table thead {
            background-color: #f2f2f2;
            border-top: 1px solid #999;
            border-bottom: 2px solid #999;
        }

        table th {
            padding: 8px 5px;
            text-align: left;
            font-size: <?= $config['tableFontSize'] ?>pt;
            font-weight: bold;
            color: #000;
        }

        table td {
            padding: 6px 5px;
            font-size: <?= $config['tableFontSize'] ?>pt;
            border-bottom: 1px solid #ddd;
        }

        /* Align columns */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Alternate row colors */
        table tbody tr:nth-child(odd) {
            background-color: #fff;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Footer Section */
        .pdf-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #666;
        }

        .footer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .footer-left {
            text-align: left;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
        }

        .footer-divider {
            border-bottom: 1px solid #ddd;
            margin: 15px 0;
        }

        /* Totals Section */
        .totals-section {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            font-size: 10pt;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            font-weight: bold;
            color: #4285f4;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
            font-size: 11pt;
        }

        /* Column Widths */
        <?php foreach ($columns as $key => $col): ?>
            .col-<?= $key ?> { width: <?= $col['width'] ?>%; }
        <?php endforeach; ?>
    </style>
</head>
<body>
    <!-- PDF Header -->
    <div class="pdf-header">
        <?php if ($config['showLogo'] && file_exists(FCPATH . $config['companyLogo'])): ?>
            <img src="<?= FCPATH . $config['companyLogo'] ?>" alt="Logo" class="company-logo">
        <?php endif; ?>
        <div class="company-name"><?= $config['companyName'] ?></div>
        <div class="report-title"><?= esc($title) ?></div>
        <div class="report-meta">
            <span class="meta-item">Tanggal: <strong><?= date('d/m/Y') ?></strong></span>
            <span class="meta-item">Jam: <strong><?= date('H:i') ?></strong></span>
        </div>
    </div>

    <!-- Filters Section -->
    <?php if (!empty($filters)): ?>
        <div class="filters-section">
            <span class="filter-label">Filter:</span>
            <?php
            $filterStrings = [];
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $filterStrings[] = "{$key}: {$value}";
                }
            }
            ?>
            <?= esc(implode(' | ', $filterStrings) ?? 'Semua Data') ?>
        </div>
    <?php endif; ?>

    <!-- Data Table -->
    <?php if (!empty($data)): ?>
        <table>
            <thead>
                <tr>
                    <?php foreach ($columns as $key => $col): ?>
                        <th class="col-<?= $key ?> text-<?= $col['align'] ?>">
                            <?= esc($col['label']) ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($columns as $key => $col): ?>
                            <td class="col-<?= $key ?> text-<?= $col['align'] ?>">
                                <?= esc($row[$key] ?? '-') ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">Tidak ada data untuk ditampilkan</div>
    <?php endif; ?>

    <!-- Totals Section -->
    <div class="totals-section">
        <div class="total-item">
            <span class="total-label">Total Record:</span>
            <span class="total-value"><?= $totalRecords ?> item</span>
        </div>
    </div>

    <!-- PDF Footer -->
    <div class="pdf-footer">
        <div class="footer-divider"></div>
        <div class="footer-info">
            <div class="footer-left">
                <?php if ($config['includePrintedBy']): ?>
                    <strong>Dicetak oleh:</strong> <?= esc($generatedBy) ?><br>
                    <strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i') ?>
                <?php endif; ?>
            </div>
            <div class="footer-center">
                <?php if (!empty($config['footerText'])): ?>
                    <em><?= esc($config['footerText']) ?></em>
                <?php endif; ?>
            </div>
            <div class="footer-right">
                <strong>File:</strong> <?= esc($entity) ?>_export_<?= date('Ymd_His') ?>.pdf
            </div>
        </div>
    </div>
</body>
</html>
