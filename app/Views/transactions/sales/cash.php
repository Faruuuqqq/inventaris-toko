<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="grid gap-6 lg:grid-cols-3" 
     x-data="salesForm()" 
     x-init="initData()">
     
    <!-- Form Section (Left 2/3) -->
    <div class="lg:col-span-2">
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Banknote', 'h-5 w-5') ?>
                    Form Penjualan Tunai
                </h3>
            </div>
            
            <form id="sales-form" action="<?= base_url('transactions/sales/storeCash') ?>" method="POST" @submit.prevent="submitForm">
                <?= csrf_field() ?>
                <input type="hidden" name="items" :value="JSON.stringify(items)">
                
                <div class="p-6 pt-0 space-y-6">
                    <!-- Alerts -->
                    <?php if(session()->getFlashdata('error')): ?>
                    <div class="p-4 rounded-md bg-destructive/15 text-destructive text-sm font-medium">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                    <?php endif; ?>

                    <?php if(session()->getFlashdata('success')): ?>
                    <div class="p-4 rounded-md bg-green-100 text-green-700 text-sm font-medium">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                    <?php endif; ?>

                    <!-- Header Inputs Grid -->
                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">No. Faktur</label>
                            <input type="text" value="Auto (INV-xxxx)" class="flex h-10 w-full rounded-md border border-input bg-muted px-3 py-2 text-sm ring-offset-background disabled:cursor-not-allowed disabled:opacity-50" disabled>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">Tanggal</label>
                            <input type="date" value="<?= date('Y-m-d') ?>" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">Customer</label>
                            <select name="customer_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" required>
                                <option value="">Pilih customer</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">Sales / Gudang</label>
                            <select name="warehouse_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" required>
                                <?php foreach ($warehouses as $w): ?>
                                    <option value="<?= $w['id'] ?>"><?= $w['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Add Product Section -->
                    <div class="rounded-lg border p-4">
                        <h4 class="mb-4 font-medium">Tambah Produk</h4>
                        <div class="grid gap-4 md:grid-cols-5">
                            <div class="md:col-span-2">
                                <select x-model="tempItem.product_id" @change="fillPrice()" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                    <option value="">Pilih Member...</option>
                                    <template x-for="p in products" :key="p.id">
                                        <option :value="p.id" x-text="p.name + ' - ' + formatRupiahSimple(p.price_sell)"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <input type="number" x-model.number="tempItem.quantity" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" placeholder="Qty">
                            </div>
                            <div>
                                <input type="number" x-model.number="tempItem.discount" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" placeholder="Diskon (Rp)">
                            </div>
                            <button type="button" @click="addItem()" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                                <?= icon('Plus', 'mr-2 h-4 w-4') ?>
                                Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="[&_tr]:border-b">
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-12">No</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Qty</th>
                                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Harga</th>
                                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Diskon</th>
                                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Subtotal</th>
                                    <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground w-[50px]"></th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <template x-if="items.length === 0">
                                    <tr>
                                        <td colspan="7" class="p-4 text-center text-muted-foreground">Belum ada barang ditambahkan.</td>
                                    </tr>
                                </template>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-b transition-colors hover:bg-muted/50">
                                        <td class="p-4 align-middle" x-text="index + 1"></td>
                                        <td class="p-4 align-middle" x-text="item.name"></td>
                                        <td class="p-4 align-middle text-right">
                                            <input type="number" x-model.number="item.quantity" class="w-16 rounded border px-2 py-1 text-center text-xs bg-transparent">
                                        </td>
                                        <td class="p-4 align-middle text-right" x-text="formatRupiahSimple(item.price)"></td>
                                        <td class="p-4 align-middle text-right" x-text="formatRupiahSimple(item.discount)"></td>
                                        <td class="p-4 align-middle text-right font-medium" x-text="formatRupiahSimple(itemSubtotal(item))"></td>
                                        <td class="p-4 align-middle text-center">
                                            <button type="button" @click="removeItem(index)" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8 text-destructive">
                                                <?= icon('Trash2', 'h-4 w-4') ?>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Summary Section (Right 1/3) -->
    <div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm sticky top-24">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <?= icon('Calculator', 'h-5 w-5') ?>
                    Ringkasan
                </h3>
            </div>
            
            <div class="p-6 pt-0 space-y-4">
                <!-- Subtotal -->
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground" x-text="'Subtotal (' + items.length + ' item)'"></span>
                    <span x-text="formatRupiah(grandTotalWithoutDiscount())"></span>
                </div>
                <!-- Total Discount -->
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Diskon Total</span>
                    <span x-text="formatRupiah(totalDiscount())"></span>
                </div>
                
                <!-- Divider & Grand Total -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Grand Total</span>
                        <span class="text-2xl font-bold text-primary" x-text="formatRupiah(grandTotal())"></span>
                    </div>
                </div>

                <!-- Pay Input -->
                <div class="space-y-2 pt-4">
                    <label class="text-sm font-medium leading-none">Bayar</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">Rp</span>
                        <input type="number" x-model.number="payAmount" class="flex h-12 w-full rounded-md border border-input bg-background px-3 py-2 pl-9 text-right text-lg font-medium ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="0">
                    </div>
                </div>

                <!-- Change Display -->
                <div class="flex justify-between rounded-lg bg-green-100 p-3 dark:bg-green-900/20">
                    <span class="font-medium text-green-700 dark:text-green-400">Kembalian</span>
                    <span class="font-bold text-green-700 dark:text-green-400" x-text="formatRupiah(changeAmount())"></span>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-4">
                    <button type="button" @click="resetForm()" class="inline-flex flex-1 items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        Batal
                    </button>
                    <button type="submit" form="sales-form" :disabled="items.length === 0" class="inline-flex flex-1 items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Simpan
                    </button>
                </div>
                
                <button type="button" class="inline-flex w-full items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    <?= icon('Printer', 'mr-2 h-4 w-4') ?>
                    Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function salesForm() {
        return {
            items: [],
            products: [],
            payAmount: 0,
            tempItem: {
                product_id: '',
                name: '',
                price: 0,
                quantity: 1,
                discount: 0
            },
            
            async initData() {
                try {
                    const res = await fetch('<?= base_url('transactions/sales/getProducts') ?>');
                    this.products = await res.json();
                } catch (e) {
                    console.error("Failed to load products", e);
                }
            },

            fillPrice() {
                const product = this.products.find(p => p.id == this.tempItem.product_id);
                if (product) {
                    this.tempItem.name = product.name;
                    this.tempItem.price = parseFloat(product.price_sell);
                }
            },

            addItem() {
                if (!this.tempItem.product_id) {
                    alert('Pilih produk terlebih dahulu!');
                    return;
                }
                
                // Add item to list
                this.items.push({ ...this.tempItem });
                
                // Reset form fields
                this.tempItem.product_id = '';
                this.tempItem.quantity = 1;
                this.tempItem.price = 0;
                this.tempItem.discount = 0;
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },
            
            resetForm() {
                if(confirm('Yakin ingin mereset transaksi?')) {
                    this.items = [];
                    this.payAmount = 0;
                }
            },

            itemSubtotal(item) {
                return (item.price * item.quantity) - item.discount;
            },

            totalDiscount() {
                return this.items.reduce((acc, item) => acc + parseFloat(item.discount), 0);
            },

            grandTotalWithoutDiscount() {
                 return this.items.reduce((acc, item) => acc + parseFloat(item.price * item.quantity), 0);
            },

            grandTotal() {
                return this.items.reduce((acc, item) => {
                    return acc + ((item.price * item.quantity) - item.discount);
                }, 0);
            },
            
            changeAmount() {
                const total = this.grandTotal();
                if (this.payAmount <= 0) return 0;
                return Math.max(0, this.payAmount - total);
            },

            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            },
            
            formatRupiahSimple(number) {
                 return new Intl.NumberFormat('id-ID').format(number);
            },

            submitForm(e) {
                if (this.items.length === 0) {
                    alert('Keranjang masih kosong!');
                    return;
                }
                if (this.payAmount < this.grandTotal()) {
                    alert('Peringatan: Jumlah bayar kurang dari total belanja! (Hanya Peringatan)');
                    // allow submit for now or return
                }
                e.target.submit();
            }
        }
    }
</script>
<?= $this->endSection() ?>