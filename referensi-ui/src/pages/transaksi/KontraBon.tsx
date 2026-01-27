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
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion';
import { Search, ClipboardList, Printer } from 'lucide-react';
import { Badge } from '@/components/ui/badge';

interface KontraBonItem {
  id: string;
  customer: string;
  faktur: string;
  tanggal: string;
  jatuhTempo: string;
  jumlah: number;
  status: 'belum_lunas' | 'sebagian';
}

const kontraBonData: Record<string, KontraBonItem[]> = {
  'Toko Makmur': [
    { id: '1', customer: 'Toko Makmur', faktur: 'PK-2024-001', tanggal: '2024-01-10', jatuhTempo: '2024-02-10', jumlah: 5500000, status: 'belum_lunas' },
    { id: '2', customer: 'Toko Makmur', faktur: 'PK-2024-005', tanggal: '2024-01-15', jatuhTempo: '2024-02-15', jumlah: 3200000, status: 'belum_lunas' },
  ],
  'CV Sejahtera': [
    { id: '3', customer: 'CV Sejahtera', faktur: 'PK-2024-002', tanggal: '2024-01-12', jatuhTempo: '2024-02-12', jumlah: 12000000, status: 'sebagian' },
  ],
  'UD Bahagia': [
    { id: '4', customer: 'UD Bahagia', faktur: 'PK-2024-003', tanggal: '2024-01-08', jatuhTempo: '2024-02-08', jumlah: 8800000, status: 'belum_lunas' },
    { id: '5', customer: 'UD Bahagia', faktur: 'PK-2024-006', tanggal: '2024-01-18', jatuhTempo: '2024-02-18', jumlah: 4500000, status: 'belum_lunas' },
    { id: '6', customer: 'UD Bahagia', faktur: 'PK-2024-008', tanggal: '2024-01-20', jatuhTempo: '2024-02-20', jumlah: 2100000, status: 'sebagian' },
  ],
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const KontraBon = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [filterCustomer, setFilterCustomer] = useState('all');

  const customers = Object.keys(kontraBonData);
  
  const filteredData = Object.entries(kontraBonData)
    .filter(([customer]) => 
      filterCustomer === 'all' || customer === filterCustomer
    )
    .filter(([customer]) =>
      customer.toLowerCase().includes(searchTerm.toLowerCase())
    );

  const totalKontraBon = Object.values(kontraBonData)
    .flat()
    .reduce((sum, item) => sum + item.jumlah, 0);

  return (
    <MainLayout title="Kontra Bon" subtitle="Daftar bon yang belum dilunasi per customer">
      {/* Summary */}
      <div className="mb-6 grid gap-4 md:grid-cols-3">
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Customer</p>
            <p className="text-2xl font-bold">{customers.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Bon</p>
            <p className="text-2xl font-bold">{Object.values(kontraBonData).flat().length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Nilai</p>
            <p className="text-2xl font-bold text-warning">{formatCurrency(totalKontraBon)}</p>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="flex gap-4">
          <div className="relative w-full sm:w-72">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input
              placeholder="Cari customer..."
              className="pl-9"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
          <Select value={filterCustomer} onValueChange={setFilterCustomer}>
            <SelectTrigger className="w-48">
              <SelectValue placeholder="Semua Customer" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Customer</SelectItem>
              {customers.map((cust) => (
                <SelectItem key={cust} value={cust}>{cust}</SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <Button variant="outline">
          <Printer className="mr-2 h-4 w-4" />
          Cetak Kontra Bon
        </Button>
      </div>

      {/* Kontra Bon List */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <ClipboardList className="h-5 w-5" />
            Daftar Kontra Bon per Customer
          </CardTitle>
        </CardHeader>
        <CardContent>
          <Accordion type="multiple" className="w-full">
            {filteredData.map(([customer, items]) => {
              const totalCustomer = items.reduce((sum, item) => sum + item.jumlah, 0);
              return (
                <AccordionItem key={customer} value={customer}>
                  <AccordionTrigger className="hover:no-underline">
                    <div className="flex w-full items-center justify-between pr-4">
                      <div className="flex items-center gap-3">
                        <span className="font-semibold">{customer}</span>
                        <Badge variant="secondary">{items.length} bon</Badge>
                      </div>
                      <span className="text-lg font-bold text-warning">{formatCurrency(totalCustomer)}</span>
                    </div>
                  </AccordionTrigger>
                  <AccordionContent>
                    <Table>
                      <TableHeader>
                        <TableRow>
                          <TableHead>No. Faktur</TableHead>
                          <TableHead>Tanggal</TableHead>
                          <TableHead>Jatuh Tempo</TableHead>
                          <TableHead className="text-right">Jumlah</TableHead>
                          <TableHead>Status</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        {items.map((item) => (
                          <TableRow key={item.id}>
                            <TableCell className="font-medium text-primary">{item.faktur}</TableCell>
                            <TableCell>{item.tanggal}</TableCell>
                            <TableCell>
                              <Badge variant={new Date(item.jatuhTempo) < new Date() ? 'destructive' : 'secondary'}>
                                {item.jatuhTempo}
                              </Badge>
                            </TableCell>
                            <TableCell className="text-right font-medium">{formatCurrency(item.jumlah)}</TableCell>
                            <TableCell>
                              <Badge variant={item.status === 'belum_lunas' ? 'destructive' : 'outline'}>
                                {item.status === 'belum_lunas' ? 'Belum Lunas' : 'Sebagian'}
                              </Badge>
                            </TableCell>
                          </TableRow>
                        ))}
                      </TableBody>
                    </Table>
                  </AccordionContent>
                </AccordionItem>
              );
            })}
          </Accordion>
        </CardContent>
      </Card>
    </MainLayout>
  );
};

export default KontraBon;
