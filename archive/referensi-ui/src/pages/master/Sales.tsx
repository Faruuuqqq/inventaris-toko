import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Plus, Search, Pencil, Trash2, Phone, Mail, TrendingUp, Users } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

interface SalesData {
  id: string;
  name: string;
  phone: string;
  email: string;
  area: string;
  totalSales: string;
  totalTransactions: number;
  status: 'active' | 'inactive';
}

const initialSales: SalesData[] = [
  { id: 'SLS001', name: 'Ahmad Sulaiman', phone: '081234567890', email: 'ahmad@toko.com', area: 'Jakarta Utara', totalSales: 'Rp 125.000.000', totalTransactions: 85, status: 'active' },
  { id: 'SLS002', name: 'Dewi Kartika', phone: '082345678901', email: 'dewi@toko.com', area: 'Jakarta Selatan', totalSales: 'Rp 98.500.000', totalTransactions: 72, status: 'active' },
  { id: 'SLS003', name: 'Rudi Hartono', phone: '083456789012', email: 'rudi@toko.com', area: 'Jakarta Barat', totalSales: 'Rp 78.200.000', totalTransactions: 58, status: 'active' },
  { id: 'SLS004', name: 'Siti Aminah', phone: '084567890123', email: 'siti@toko.com', area: 'Jakarta Timur', totalSales: 'Rp 45.000.000', totalTransactions: 32, status: 'inactive' },
];

const Sales = () => {
  const [salesList] = useState<SalesData[]>(initialSales);
  const [searchTerm, setSearchTerm] = useState('');
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  const filteredSales = salesList.filter(
    (s) =>
      s.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      s.id.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const activeSales = salesList.filter((s) => s.status === 'active').length;

  return (
    <MainLayout title="Sales" subtitle="Kelola daftar sales/marketing">
      {/* Summary */}
      <div className="mb-6 grid gap-4 md:grid-cols-3">
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                <Users className="h-5 w-5 text-primary" />
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Total Sales</p>
                <p className="text-2xl font-bold">{salesList.length}</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                <TrendingUp className="h-5 w-5 text-success" />
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Sales Aktif</p>
                <p className="text-2xl font-bold text-success">{activeSales}</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <div className="flex items-center gap-3">
              <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                <TrendingUp className="h-5 w-5 text-warning" />
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Total Transaksi</p>
                <p className="text-2xl font-bold">{salesList.reduce((sum, s) => sum + s.totalTransactions, 0)}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative w-full sm:w-72">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari sales..."
            className="pl-9"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Tambah Sales
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Tambah Sales Baru</DialogTitle>
            </DialogHeader>
            <form className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Nama Sales</Label>
                <Input id="name" placeholder="Masukkan nama sales" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="phone">No. Telepon</Label>
                <Input id="phone" placeholder="Masukkan no. telepon" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="email">Email</Label>
                <Input id="email" type="email" placeholder="Masukkan email" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="area">Area Kerja</Label>
                <Input id="area" placeholder="Masukkan area kerja" />
              </div>
              <div className="flex justify-end gap-2">
                <Button type="button" variant="outline" onClick={() => setIsDialogOpen(false)}>
                  Batal
                </Button>
                <Button type="submit">Simpan</Button>
              </div>
            </form>
          </DialogContent>
        </Dialog>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {filteredSales.map((sales) => (
          <Card key={sales.id} className="transition-shadow hover:shadow-md">
            <CardHeader className="pb-3">
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <Avatar className="h-12 w-12">
                    <AvatarFallback className="bg-primary text-primary-foreground">
                      {sales.name.split(' ').map((n) => n[0]).join('')}
                    </AvatarFallback>
                  </Avatar>
                  <div>
                    <div className="flex items-center gap-2">
                      <p className="text-xs text-muted-foreground">{sales.id}</p>
                      <Badge variant={sales.status === 'active' ? 'default' : 'secondary'}>
                        {sales.status === 'active' ? 'Aktif' : 'Tidak Aktif'}
                      </Badge>
                    </div>
                    <CardTitle className="mt-1 text-lg">{sales.name}</CardTitle>
                  </div>
                </div>
                <div className="flex gap-1">
                  <Button variant="ghost" size="icon" className="h-8 w-8">
                    <Pencil className="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="icon" className="h-8 w-8 text-destructive">
                    <Trash2 className="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </CardHeader>
            <CardContent className="space-y-3">
              <div className="flex items-center gap-2 text-sm text-muted-foreground">
                <Phone className="h-4 w-4" />
                {sales.phone}
              </div>
              <div className="flex items-center gap-2 text-sm text-muted-foreground">
                <Mail className="h-4 w-4" />
                {sales.email}
              </div>
              <p className="text-sm">
                <span className="text-muted-foreground">Area: </span>
                <span className="font-medium">{sales.area}</span>
              </p>
              <div className="grid grid-cols-2 gap-4 border-t pt-3">
                <div>
                  <p className="text-xs text-muted-foreground">Total Penjualan</p>
                  <p className="font-semibold text-primary">{sales.totalSales}</p>
                </div>
                <div>
                  <p className="text-xs text-muted-foreground">Transaksi</p>
                  <p className="font-semibold">{sales.totalTransactions}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </MainLayout>
  );
};

export default Sales;
