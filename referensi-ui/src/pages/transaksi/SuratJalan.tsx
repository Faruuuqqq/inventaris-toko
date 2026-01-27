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
import { Plus, Trash2, FileText, Printer, Truck } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';

const SuratJalan = () => {
  return (
    <MainLayout title="Surat Jalan" subtitle="Buat surat jalan untuk pengiriman barang">
      <Alert className="mb-6 border-primary/50 bg-primary/10">
        <FileText className="h-4 w-4 text-primary" />
        <AlertDescription className="text-primary">
          Surat jalan tidak mencantumkan harga. Digunakan sebagai bukti serah terima barang.
        </AlertDescription>
      </Alert>

      <div className="grid gap-6 lg:grid-cols-3">
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Truck className="h-5 w-5" />
                Form Surat Jalan
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="grid gap-4 md:grid-cols-3">
                <div className="space-y-2">
                  <Label>No. Surat Jalan</Label>
                  <Input value="SJ-2024-001" disabled />
                </div>
                <div className="space-y-2">
                  <Label>Tanggal</Label>
                  <Input type="date" defaultValue={new Date().toISOString().split('T')[0]} />
                </div>
                <div className="space-y-2">
                  <Label>No. Faktur</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih faktur" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="pj1">PJ-2024-001</SelectItem>
                      <SelectItem value="pk1">PK-2024-002</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <Label>Customer</Label>
                  <Input value="Toko Makmur" disabled />
                </div>
                <div className="space-y-2">
                  <Label>Alamat Pengiriman</Label>
                  <Input placeholder="Alamat tujuan pengiriman" />
                </div>
              </div>

              <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <Label>Pengirim/Driver</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih pengirim" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="drv1">Bambang - B 1234 CD</SelectItem>
                      <SelectItem value="drv2">Agus - B 5678 EF</SelectItem>
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

              <div className="rounded-lg border p-4">
                <h4 className="mb-4 font-medium">Tambah Barang</h4>
                <div className="grid gap-4 md:grid-cols-4">
                  <div className="md:col-span-2">
                    <Select>
                      <SelectTrigger>
                        <SelectValue placeholder="Pilih produk" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="prd1">Beras Premium 5kg</SelectItem>
                        <SelectItem value="prd2">Minyak Goreng 2L</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <Input type="number" placeholder="Qty" />
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
                    <TableHead>Nama Barang</TableHead>
                    <TableHead className="text-right">Quantity</TableHead>
                    <TableHead className="text-right">Satuan</TableHead>
                    <TableHead>Keterangan</TableHead>
                    <TableHead></TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow>
                    <TableCell>1</TableCell>
                    <TableCell>Beras Premium 5kg</TableCell>
                    <TableCell className="text-right">10</TableCell>
                    <TableCell className="text-right">Karung</TableCell>
                    <TableCell className="text-muted-foreground">-</TableCell>
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
                    <TableCell className="text-right">Botol</TableCell>
                    <TableCell className="text-muted-foreground">-</TableCell>
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
              <CardTitle>Ringkasan</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Total Jenis Barang</span>
                <span className="font-medium">2 produk</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Total Quantity</span>
                <span className="font-medium">30 pcs</span>
              </div>

              <div className="space-y-2 border-t pt-4">
                <Label>Catatan Pengiriman</Label>
                <Input placeholder="Catatan khusus..." />
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
                Cetak Surat Jalan
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>
    </MainLayout>
  );
};

export default SuratJalan;
