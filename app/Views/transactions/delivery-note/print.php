<!DOCTYPE html>
<?php $this->section('content') ?>
<?php $this->extend('layout/main') ?>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - <?= $sale['invoice_number'] ?></title>
    <link href="<?= base_url('public/css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            padding: 20px;
            background: white;
        }

        .delivery-note {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .info-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .info-box {
            flex: 1;
            margin-right: 20px;
        }

        .info-box h3 {
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-box p {
            margin-bottom: 5px;
        }

        .table-container {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 8px 12px;
            text-align: left;
        }

        table th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        table td.no {
            text-align: center;
            width: 50px;
        }

        table td.qty {
            text-align: center;
            width: 80px;
        }

        table td.unit {
            text-align: center;
            width: 80px;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-box p {
            margin-bottom: 30px;
        }

        .signature-box .signature {
            height: 60px;
            border-top: 1px solid #333;
            margin-top: 60px;
        }

        .print-button {
            text-align: center;
            margin: 20px 0;
            padding: 20px 0;
            border-top: 1px solid #ddd;
        }

        .print-button button {
            padding: 10px 30px;
            font-size: 16px;
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .print-button button:hover {
            background: #0056b3;
        }

        @media print {
            .print-button {
                display: none;
            }

            body {
                padding: 0;
            }

            .delivery-note {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="delivery-note">
        <div class="header">
            <h1>SURAT JALAN</h1>
            <p><?= $sale['invoice_number'] ?></p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>PENGIRIM</h3>
                <p><strong><?= $warehouse['name'] ?></strong></p>
                <p><?= $warehouse['address'] ?></p>
                <p><?= $warehouse['phone'] ?></p>
            </div>
            <div class="info-box">
                <h3>PENERIMA</h3>
                <p><strong><?= $customer['name'] ?></strong></p>
                <p><?= $customer['address'] ?></p>
                <p><?= $customer['phone'] ?></p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>INFORMASI PENGIRIMAN</h3>
                <p><strong>Tanggal:</strong> <?= format_date($sale['created_at']) ?></p>
                <p><strong>Salesman:</strong> <?= $salesperson['name'] ?></p>
                <p><strong>Gudang:</strong> <?= $warehouse['name'] ?></p>
            </div>
            <div class="info-box">
                <h3>KETERANGAN</h3>
                <p><strong>No. Faktur:</strong> <?= $sale['invoice_number'] ?></p>
                <p><strong>Tipe Pembayaran:</strong> <?= $sale['payment_type'] === 'CASH' ? 'Tunai' : 'Kredit' ?></p>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="no">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="unit">Satuan</th>
                        <th class="qty">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="no"><?= $no++ ?></td>
                            <td><?= $item['product_code'] ?></td>
                            <td><?= $item['product_name'] ?></td>
                            <td class="unit"><?= $item['unit'] ?></td>
                            <td class="qty"><?= $item['quantity'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div class="signature-box">
                <p>Pengirim,</p>
                <div class="signature"></div>
                <p>( ........................ )</p>
            </div>
            <div class="signature-box">
                <p>Penerima,</p>
                <div class="signature"></div>
                <p>( ........................ )</p>
            </div>
        </div>

        <div class="print-button">
            <button onclick="window.print()">Cetak Surat Jalan</button>
        </div>
    </div>
</body>
</html>


<?php $this->endSection() ?>