📘 PROJECT BLUEPRINT: Sistem Distributor Mini ERP
1. Project Context
Nama Project: Toko Distributor Management System

Tujuan: Membangun sistem web-based (Local LAN) untuk toko grosir dengan fitur manajemen stok multi-gudang, keuangan B2B (Kontra Bon), dan hak akses ketat (Owner vs Admin).

Sifat: Monolith Application (Tanpa API terpisah untuk frontend).

2. Tech Stack (Final Decision)
Backend: CodeIgniter 4 (PHP 8.1+)

Database: MySQL (Storage Engine: InnoDB)

Frontend Styling: Tailwind CSS (via CLI Standalone)

Frontend Logic: Alpine.js (Pengganti React/jQuery)

Icons: Lucide Icons (SVG Sprite)

Server: Laragon (Apache)

3. Product Requirements Document (PRD)
<roles>
OWNER (Super Admin):

Bisa melihat SEMUA laporan (termasuk penjualan yang di-hide).

Bisa melihat laba rugi bersih.

Bisa edit stok/harga secara paksa (bypass).

ADMIN (Operator):

Input transaksi harian (Jual/Beli).

Cetak Surat Jalan & Invoice.

Restriksi: Tidak melihat transaksi "Hidden" dan Laba Bersih.

</roles>

<features>
A. Modul Master Data
Produk: CRUD dengan field SKU, Nama, Kategori, Harga Beli (HPP), Harga Jual.

Gudang: Multi-lokasi (Min. 2: Gudang Utama, Gudang BS/Rusak).

Customer: Dilengkapi Limit Kredit (Plafon Utang).

Supplier: Data hutang awal.

B. Modul Transaksi (Inventory & Sales)
Penjualan (Kasir):

Input via Barcode Scanner.

Pilihan Pembayaran: Tunai / Kredit.

Fitur Owner: Checkbox is_hidden (Sembunyikan dari Laporan Admin).

Cek Limit Kredit otomatis saat input.

Surat Jalan: Cetak dokumen pengiriman (Hanya Qty & Nama Barang, Tanpa Harga).

Retur Penjualan:

Input barang kembali dari customer.

Pilih kondisi: Bagus (Masuk Gudang Utama) atau Rusak (Masuk Gudang BS).

Otomatis potong Piutang Customer.

C. Modul Keuangan (Finance)
Kontra Bon (Tukar Faktur):

Memilih beberapa invoice UNPAID milik satu customer.

Digabung menjadi satu dokumen tagihan (Kontra Bon).

Pelunasan: Pembayaran bisa parsial (cicil).

Laporan:

Kartu Stok: Log detil pergerakan barang (Masuk/Keluar/Opname).

Laporan Harian: Kas Masuk/Keluar.

Aging Schedule: Daftar umur piutang customer.

</features>

4. Implementation Plan
<backend_plan>
Fokus: CodeIgniter 4 & MySQL

Database Design (Schema First):

Eksekusi Script SQL (Tabel: users, products, product_stocks, sales, sales_items, kontra_bons, stock_mutations).

Pastikan relasi Foreign Key aktif (ON UPDATE CASCADE ON DELETE RESTRICT).

Gunakan tipe data DECIMAL(15,2) untuk uang.

Setup CI4 Core:

Install CI4.

Setup .env (Database Connection).

Buat AuthFilter (Middleware) untuk proteksi rute Admin vs Owner.

MVC Logic Strategy:

Model: Gunakan Entity (App\Entities\Product) untuk pengelolaan data objek.

Controller: Pisahkan logic per modul (SalesController, InventoryController, FinanceController).

Transaction: Gunakan $db->transStart() pada fitur Penjualan dan Retur untuk menjaga konsistensi stok dan keuangan.

Security Logic:

Implementasi Global Scope pada Model Penjualan: Jika user bukan Owner, otomatis tambahkan WHERE is_hidden = 0 pada semua query laporan. </backend_plan>

<frontend_plan>
Fokus: Replikasi UI React menggunakan CI4 Views + Tailwind

Setup "The T.A.L Stack":

Download tailwindcss.exe ke root project.

Download alpine.min.js ke public/assets/js/.

Config Tailwind untuk scan folder app/Views/**/*.php.

Layouting (Slicing):

app/Views/layout/main.php: Skeleton HTML.

app/Views/layout/sidebar.php: Replikasi menu dari file src/components/layout/AppSidebar.tsx (yang kamu upload).

app/Views/layout/navbar.php: Bagian atas (Profile & Toggle Sidebar).

Component Mapping (React -> CI4 View):

React <Card> ➡️ CI4 View components/ui/card.php.

React <Button> ➡️ CI4 View components/ui/button.php.

React <Table> ➡️ HTML Table dengan class Tailwind w-full caption-bottom text-sm....

Interactivity Logic (Alpine.js):

Modal Tambah Barang: Gunakan x-data="{ open: false }" dan x-show="open".

Form Penjualan (Dynamic Rows): Gunakan x-data="{ items: [] }" dan x-for="item in items" untuk menambah baris barang tanpa reload. </frontend_plan>