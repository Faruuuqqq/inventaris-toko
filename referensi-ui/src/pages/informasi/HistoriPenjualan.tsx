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
import { Search, Eye } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useState } from 'react';

interface HistoriPenjualanItem {
  id: string;
  faktur: string;
  tanggal: string;
  customer: string;
  sales: string;
  total: number;
  tipe: 'tunai' | 'kredit';
  status: 'lunas' | 'kredit' | 'sebagian';
}

const historiData: HistoriPenjualanItem[] = [
  { id: '1', faktur: 'PJ-2024-001', tanggal: '2024-01-20', customer: 'Toko Makmur', sales: 'Ahmad Sulaiman', total: 2500000, tipe: 'tunai', status: 'lunas' },
  { id: '2', faktur: 'PK-2024-002', tanggal: '2024-01-19', customer: 'CV Sejahtera', sales: 'Dewi Kartika', total: 12000000, tipe: 'kredit', status: 'kredit' },
  { id: '3', faktur: 'PJ-2024-003', tanggal: '2024-01-18', customer: 'UD Bahagia', sales: 'Ahmad Sulaiman', total: 3800000, tipe: 'tunai', status: 'lunas' },
  { id: '4', faktur: 'PK-2024-004', tanggal: '2024-01-17', customer: 'PT Sentosa', sales: 'Rudi Hartono', total: 8800000, tipe: 'kredit', status: 'sebagian' },
  { id: '5', faktur: 'PJ-2024-005', tanggal: '2024-01-16', customer: 'Toko Jaya', sales: 'Dewi Kartika', total: 1500000, tipe: 'tunai', status: 'lunas' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const HistoriPenjualan = () => {
  const [searchTerm, setSearchTerm] = useState('');

  const filteredData = historiData.filter(
    (item) =>
      item.faktur.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.customer.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <MainLayout title="Histori Penjualan" subtitle="Riwayat penjualan barang ke customer">
      <div className="mb-6 flex items-center justify-between">
        <div className="relative w-72">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari faktur/customer..."
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
                <TableHead>Customer</TableHead>
                <TableHead>Sales</TableHead>
                <TableHead>Tipe</TableHead>
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
                  <TableCell>{item.customer}</TableCell>
                  <TableCell>{item.sales}</TableCell>
                  <TableCell>
                    <Badge variant={item.tipe === 'tunai' ? 'secondary' : 'outline'}>
                      {item.tipe === 'tunai' ? 'Tunai' : 'Kredit'}
                    </Badge>
                  </TableCell>
                  <TableCell className="text-right font-medium">{formatCurrency(item.total)}</TableCell>
                  <TableCell>
                    <Badge variant={
                      item.status === 'lunas' ? 'default' : 
                      item.status === 'kredit' ? 'destructive' : 'secondary'
                    }>
                      {item.status === 'lunas' ? 'Lunas' : item.status === 'kredit' ? 'Belum Bayar' : 'Sebagian'}
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

export default HistoriPenjualan;
