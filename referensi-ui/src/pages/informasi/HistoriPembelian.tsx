import { MainLayout } from '@/components/layout/MainLayout';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Search, History, Eye } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useState } from 'react';

interface HistoriPembelianItem {
  id: string;
  faktur: string;
  tanggal: string;
  supplier: string;
  total: number;
  status: 'lunas' | 'kredit' | 'sebagian';
}

const historiData: HistoriPembelianItem[] = [
  { id: '1', faktur: 'PB-2024-001', tanggal: '2024-01-20', supplier: 'PT Sumber Makmur', total: 15000000, status: 'lunas' },
  { id: '2', faktur: 'PB-2024-002', tanggal: '2024-01-18', supplier: 'CV Berkah Jaya', total: 8500000, status: 'kredit' },
  { id: '3', faktur: 'PB-2024-003', tanggal: '2024-01-15', supplier: 'UD Sentosa Abadi', total: 5200000, status: 'sebagian' },
  { id: '4', faktur: 'PB-2024-004', tanggal: '2024-01-12', supplier: 'PT Indo Distributor', total: 22000000, status: 'lunas' },
  { id: '5', faktur: 'PB-2024-005', tanggal: '2024-01-10', supplier: 'PT Sumber Makmur', total: 18500000, status: 'lunas' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const HistoriPembelian = () => {
  const [searchTerm, setSearchTerm] = useState('');

  const filteredData = historiData.filter(
    (item) =>
      item.faktur.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.supplier.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <MainLayout title="Histori Pembelian" subtitle="Riwayat pembelian barang dari supplier">
      <div className="mb-6 flex items-center justify-between">
        <div className="relative w-72">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari faktur/supplier..."
            className="pl-9"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <div className="flex gap-2">
          <Input type="date" className="w-40" />
          <span className="flex items-center text-muted-foreground">s/d</span>
          <Input type="date" className="w-40" />
        </div>
      </div>

      <Card>
        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>No. Faktur</TableHead>
                <TableHead>Tanggal</TableHead>
                <TableHead>Supplier</TableHead>
                <TableHead className="text-right">Total</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredData.map((item) => (
                <TableRow key={item.id}>
                  <TableCell className="font-medium text-primary">{item.faktur}</TableCell>
                  <TableCell>{item.tanggal}</TableCell>
                  <TableCell>{item.supplier}</TableCell>
                  <TableCell className="text-right font-medium">{formatCurrency(item.total)}</TableCell>
                  <TableCell>
                    <Badge variant={
                      item.status === 'lunas' ? 'default' : 
                      item.status === 'kredit' ? 'destructive' : 'secondary'
                    }>
                      {item.status === 'lunas' ? 'Lunas' : item.status === 'kredit' ? 'Kredit' : 'Sebagian'}
                    </Badge>
                  </TableCell>
                  <TableCell className="text-right">
                    <Button variant="ghost" size="icon" className="h-8 w-8">
                      <Eye className="h-4 w-4" />
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </MainLayout>
  );
};

export default HistoriPembelian;
