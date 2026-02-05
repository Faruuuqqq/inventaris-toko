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
import { Plus, Trash2, RotateCcw, Printer } from 'lucide-react';

const ReturPembelian = () => {
  return (
    <MainLayout title="Retur Pembelian" subtitle="Buat surat retur barang ke supplier">
      <div className="grid gap-6 lg:grid-cols-3">
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <RotateCcw className="h-5 w-5" />
                Form Retur Pembelian
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid gap-4 md:grid-cols-3">
                <div className="space-y-2">
                  <Label>No. Retur</Label>
                  <Input value="RTB-2024-001" disabled />
                </div>
                <div className="space-y-2">
                  <Label>Tanggal</Label>
                  <Input type="date" defaultValue={new Date().toISOString().split('T')[0]} />
                </div>
                <div className="space-y-2">
                  <Label>No. Faktur Pembelian</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih faktur" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="pb1">PB-2024-001 - PT Sumber Makmur</SelectItem>
                      <SelectItem value="pb2">PB-2024-002 - CV Berkah Jaya</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="space-y-2">
                <Label>Alasan Retur</Label>
                <Select>
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih alasan retur" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="rusak">Barang Rusak</SelectItem>
                    <SelectItem value="kadaluarsa">Kadaluarsa</SelectItem>
                    <SelectItem value="tidak_sesuai">Tidak Sesuai Pesanan</SelectItem>
                    <SelectItem value="cacat">Cacat Produksi</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="rounded-lg border p-4">
                <h4 className="mb-4 font-medium">Pilih Barang Retur</h4>
                <div className="grid gap-4 md:grid-cols-4">
                  <div className="md:col-span-2">
                    <Select>
                      <SelectTrigger>
                        <SelectValue placeholder="Pilih produk" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="prd1">Beras Premium 5kg (Stok: 50)</SelectItem>
                        <SelectItem value="prd2">Minyak Goreng 2L (Stok: 30)</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <Input type="number" placeholder="Qty Retur" />
                  <Button>
                    <Plus className="mr-2 h-4 w-4" />
                    Tambah
                  </Button>
                </div>
              </div>

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
                    <TableCell className="text-right">5</TableCell>
                    <TableCell className="text-right">Rp 65.000</TableCell>
                    <TableCell className="text-right font-medium">Rp 325.000</TableCell>
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

        <div>
          <Card className="sticky top-24">
            <CardHeader>
              <CardTitle>Ringkasan Retur</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Total Item</span>
                <span>1 produk</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Total Qty</span>
                <span>5 pcs</span>
              </div>
              <div className="border-t pt-4">
                <div className="flex justify-between">
                  <span className="font-semibold">Total Nilai Retur</span>
                  <span className="text-xl font-bold text-destructive">Rp 325.000</span>
                </div>
              </div>

              <div className="space-y-2 pt-4">
                <Label>Catatan</Label>
                <Input placeholder="Catatan retur..." />
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
                Cetak Surat Retur
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </MainLayout>
  );
};

export default ReturPembelian;
