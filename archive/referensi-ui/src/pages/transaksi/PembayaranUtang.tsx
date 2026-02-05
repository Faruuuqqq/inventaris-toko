import { useState } from 'react';
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
import { Search, Receipt, Check } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';

interface UtangItem {
  id: string;
  faktur: string;
  supplier: string;
  tanggal: string;
  jatuhTempo: string;
  jumlah: number;
  terbayar: number;
  sisa: number;
}

const utangList: UtangItem[] = [
  { id: '1', faktur: 'PB-2024-001', supplier: 'PT Sumber Makmur', tanggal: '2024-01-15', jatuhTempo: '2024-02-15', jumlah: 15000000, terbayar: 5000000, sisa: 10000000 },
  { id: '2', faktur: 'PB-2024-002', supplier: 'CV Berkah Jaya', tanggal: '2024-01-18', jatuhTempo: '2024-02-18', jumlah: 8500000, terbayar: 0, sisa: 8500000 },
  { id: '3', faktur: 'PB-2024-003', supplier: 'UD Sentosa Abadi', tanggal: '2024-01-20', jatuhTempo: '2024-02-20', jumlah: 5200000, terbayar: 2000000, sisa: 3200000 },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const PembayaranUtang = () => {
  const [selectedItems, setSelectedItems] = useState<string[]>([]);
  const [searchTerm, setSearchTerm] = useState('');

  const filteredUtang = utangList.filter(
    (u) =>
      u.faktur.toLowerCase().includes(searchTerm.toLowerCase()) ||
      u.supplier.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const totalSelected = utangList
    .filter((u) => selectedItems.includes(u.id))
    .reduce((sum, u) => sum + u.sisa, 0);

  return (
    <MainLayout title="Pembayaran Utang" subtitle="Bayar utang ke supplier/distributor">
      <div className="grid gap-6 lg:grid-cols-3">
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle className="flex items-center gap-2">
                  <Receipt className="h-5 w-5" />
                  Daftar Utang
                </CardTitle>
                <div className="relative w-64">
                  <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                  <Input
                    placeholder="Cari faktur/supplier..."
                    className="pl-9"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead className="w-12"></TableHead>
                    <TableHead>Faktur</TableHead>
                    <TableHead>Supplier</TableHead>
                    <TableHead>Jatuh Tempo</TableHead>
                    <TableHead className="text-right">Total</TableHead>
                    <TableHead className="text-right">Terbayar</TableHead>
                    <TableHead className="text-right">Sisa</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredUtang.map((item) => (
                    <TableRow key={item.id}>
                      <TableCell>
                        <Checkbox
                          checked={selectedItems.includes(item.id)}
                          onCheckedChange={(checked) => {
                            if (checked) {
                              setSelectedItems([...selectedItems, item.id]);
                            } else {
                              setSelectedItems(selectedItems.filter((i) => i !== item.id));
                            }
                          }}
                        />
                      </TableCell>
                      <TableCell className="font-medium text-primary">{item.faktur}</TableCell>
                      <TableCell>{item.supplier}</TableCell>
                      <TableCell>
                        <Badge variant={new Date(item.jatuhTempo) < new Date() ? 'destructive' : 'secondary'}>
                          {item.jatuhTempo}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-right">{formatCurrency(item.jumlah)}</TableCell>
                      <TableCell className="text-right text-success">{formatCurrency(item.terbayar)}</TableCell>
                      <TableCell className="text-right font-medium text-destructive">{formatCurrency(item.sisa)}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </div>

        <div>
          <Card className="sticky top-24">
            <CardHeader>
              <CardTitle>Form Pembayaran</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label>Tanggal Bayar</Label>
                <Input type="date" defaultValue={new Date().toISOString().split('T')[0]} />
              </div>

              <div className="space-y-2">
                <Label>Metode Pembayaran</Label>
                <Select>
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih metode" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="cash">Tunai</SelectItem>
                    <SelectItem value="transfer">Transfer Bank</SelectItem>
                    <SelectItem value="giro">Giro</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="rounded-lg border bg-muted/50 p-4">
                <p className="text-sm text-muted-foreground">Total Dipilih ({selectedItems.length} faktur)</p>
                <p className="text-2xl font-bold text-primary">{formatCurrency(totalSelected)}</p>
              </div>

              <div className="space-y-2">
                <Label>Jumlah Bayar</Label>
                <Input type="number" placeholder="0" className="text-right text-lg" />
              </div>

              <div className="space-y-2">
                <Label>Catatan</Label>
                <Input placeholder="Catatan pembayaran..." />
              </div>

              <div className="flex gap-2 pt-4">
                <Button variant="outline" className="flex-1">
                  Batal
                </Button>
                <Button className="flex-1" disabled={selectedItems.length === 0}>
                  <Check className="mr-2 h-4 w-4" />
                  Bayar
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </MainLayout>
  );
};

export default PembayaranUtang;
