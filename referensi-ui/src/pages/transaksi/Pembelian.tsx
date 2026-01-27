import { MainLayout } from '@/components/layout/MainLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Plus, Trash2, ShoppingCart, Calculator } from 'lucide-react';

const Pembelian = () => {
  return (
    <MainLayout title="Transaksi Pembelian" subtitle="Buat transaksi pembelian barang dari supplier">
      <div className="grid gap-6 lg:grid-cols-3">
        {/* Form Section */}
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <ShoppingCart className="h-5 w-5" />
                Form Pembelian
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Header Info */}
              <div className="grid gap-4 md:grid-cols-3">
                <div className="space-y-2">
                  <Label>No. Faktur</Label>
                  <Input value="PB-2024-001" disabled />
                </div>
                <div className="space-y-2">
                  <Label>Tanggal</Label>
                  <Input type="date" defaultValue={new Date().toISOString().split('T')[0]} />
                </div>
                <div className="space-y-2">
                  <Label>Supplier</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih supplier" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="sup1">PT Sumber Makmur</SelectItem>
                      <SelectItem value="sup2">CV Berkah Jaya</SelectItem>
                      <SelectItem value="sup3">UD Sentosa Abadi</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              {/* Add Product */}
              <div className="rounded-lg border p-4">
                <h4 className="mb-4 font-medium">Tambah Produk</h4>
                <div className="grid gap-4 md:grid-cols-5">
                  <div className="md:col-span-2">
                    <Select>
                      <SelectTrigger>
                        <SelectValue placeholder="Pilih produk" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="prd1">Beras Premium 5kg</SelectItem>
                        <SelectItem value="prd2">Minyak Goreng 2L</SelectItem>
                        <SelectItem value="prd3">Gula Pasir 1kg</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <Input type="number" placeholder="Qty" />
                  <Input type="number" placeholder="Harga" />
                  <Button>
                    <Plus className="mr-2 h-4 w-4" />
                    Tambah
                  </Button>
                </div>
              </div>

              {/* Items Table */}
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>No</TableHead>
                    <TableHead>Produk</TableHead>
                    <TableHead className="text-right">Qty</TableHead>
                    <TableHead className="text-right">Harga</TableHead>
                    <TableHead className="text-right">Subtotal</TableHead>
                    <TableHead></TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow>
                    <TableCell>1</TableCell>
                    <TableCell>Beras Premium 5kg</TableCell>
                    <TableCell className="text-right">50</TableCell>
                    <TableCell className="text-right">Rp 65.000</TableCell>
                    <TableCell className="text-right font-medium">Rp 3.250.000</TableCell>
                    <TableCell>
                      <Button variant="ghost" size="icon" className="h-8 w-8 text-destructive">
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                  <TableRow>
                    <TableCell>2</TableCell>
                    <TableCell>Minyak Goreng 2L</TableCell>
                    <TableCell className="text-right">30</TableCell>
                    <TableCell className="text-right">Rp 28.000</TableCell>
                    <TableCell className="text-right font-medium">Rp 840.000</TableCell>
                    <TableCell>
                      <Button variant="ghost" size="icon" className="h-8 w-8 text-destructive">
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </div>

        {/* Summary Section */}
        <div>
          <Card className="sticky top-24">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Calculator className="h-5 w-5" />
                Ringkasan
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Subtotal (2 item)</span>
                <span>Rp 4.090.000</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Diskon</span>
                <Input className="w-24 text-right" placeholder="0" />
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">PPN (11%)</span>
                <span>Rp 449.900</span>
              </div>
              <div className="border-t pt-4">
                <div className="flex justify-between">
                  <span className="font-semibold">Total</span>
                  <span className="text-xl font-bold text-primary">Rp 4.539.900</span>
                </div>
              </div>

              <div className="space-y-2 pt-4">
                <Label>Metode Pembayaran</Label>
                <Select>
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih metode" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="cash">Tunai</SelectItem>
                    <SelectItem value="transfer">Transfer Bank</SelectItem>
                    <SelectItem value="credit">Kredit</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="flex gap-2 pt-4">
                <Button variant="outline" className="flex-1">
                  Batal
                </Button>
                <Button className="flex-1">
                  Simpan
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </MainLayout>
  );
};

export default Pembelian;
