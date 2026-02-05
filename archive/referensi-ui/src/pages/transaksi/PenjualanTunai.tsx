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
import { Plus, Trash2, Banknote, Calculator, Printer } from 'lucide-react';

const PenjualanTunai = () => {
  return (
    <MainLayout title="Penjualan Tunai" subtitle="Buat transaksi penjualan tunai">
      <div className="grid gap-6 lg:grid-cols-3">
        {/* Form Section */}
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Banknote className="h-5 w-5" />
                Form Penjualan Tunai
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Header Info */}
              <div className="grid gap-4 md:grid-cols-4">
                <div className="space-y-2">
                  <Label>No. Faktur</Label>
                  <Input value="PJ-2024-001" disabled />
                </div>
                <div className="space-y-2">
                  <Label>Tanggal</Label>
                  <Input type="date" defaultValue={new Date().toISOString().split('T')[0]} />
                </div>
                <div className="space-y-2">
                  <Label>Customer</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih customer" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="cus1">Toko Makmur</SelectItem>
                      <SelectItem value="cus2">CV Sejahtera</SelectItem>
                      <SelectItem value="cus3">UD Bahagia</SelectItem>
                      <SelectItem value="walk">Walk-in Customer</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>Sales</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih sales" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="sls1">Ahmad Sulaiman</SelectItem>
                      <SelectItem value="sls2">Dewi Kartika</SelectItem>
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
                        <SelectItem value="prd1">Beras Premium 5kg - Rp 75.000</SelectItem>
                        <SelectItem value="prd2">Minyak Goreng 2L - Rp 35.000</SelectItem>
                        <SelectItem value="prd3">Gula Pasir 1kg - Rp 18.000</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <Input type="number" placeholder="Qty" />
                  <Input type="number" placeholder="Diskon" />
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
                    <TableHead className="text-right">Diskon</TableHead>
                    <TableHead className="text-right">Subtotal</TableHead>
                    <TableHead></TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow>
                    <TableCell>1</TableCell>
                    <TableCell>Beras Premium 5kg</TableCell>
                    <TableCell className="text-right">10</TableCell>
                    <TableCell className="text-right">Rp 75.000</TableCell>
                    <TableCell className="text-right">0</TableCell>
                    <TableCell className="text-right font-medium">Rp 750.000</TableCell>
                    <TableCell>
                      <Button variant="ghost" size="icon" className="h-8 w-8 text-destructive">
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                  <TableRow>
                    <TableCell>2</TableCell>
                    <TableCell>Minyak Goreng 2L</TableCell>
                    <TableCell className="text-right">20</TableCell>
                    <TableCell className="text-right">Rp 35.000</TableCell>
                    <TableCell className="text-right">5%</TableCell>
                    <TableCell className="text-right font-medium">Rp 665.000</TableCell>
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
                <span>Rp 1.415.000</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Diskon Total</span>
                <span>Rp 35.000</span>
              </div>
              <div className="border-t pt-4">
                <div className="flex justify-between">
                  <span className="font-semibold">Grand Total</span>
                  <span className="text-xl font-bold text-primary">Rp 1.380.000</span>
                </div>
              </div>

              <div className="space-y-2 pt-4">
                <Label>Bayar</Label>
                <Input type="number" placeholder="0" className="text-right text-lg" />
              </div>

              <div className="flex justify-between rounded-lg bg-success/10 p-3">
                <span className="font-medium text-success">Kembalian</span>
                <span className="font-bold text-success">Rp 120.000</span>
              </div>

              <div className="flex gap-2 pt-4">
                <Button variant="outline" className="flex-1">
                  Batal
                </Button>
                <Button className="flex-1">
                  Simpan
                </Button>
              </div>

              <Button variant="outline" className="w-full">
                <Printer className="mr-2 h-4 w-4" />
                Cetak Struk
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </MainLayout>
  );
};

export default PenjualanTunai;
