import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Plus, Search, Pencil, Trash2, Phone, MapPin, AlertCircle } from 'lucide-react';
import { Badge } from '@/components/ui/badge';

interface Customer {
  id: string;
  name: string;
  phone: string;
  address: string;
  email: string;
  piutang: number;
  totalTransaksi: string;
}

const initialCustomers: Customer[] = [
  { id: 'CUS001', name: 'Toko Makmur', phone: '081234567890', address: 'Jl. Pasar No. 15, Jakarta', email: 'makmur@toko.com', piutang: 5500000, totalTransaksi: 'Rp 45.000.000' },
  { id: 'CUS002', name: 'CV Sejahtera', phone: '082345678901', address: 'Jl. Raya Bogor No. 88', email: 'sejahtera@cv.id', piutang: 0, totalTransaksi: 'Rp 78.500.000' },
  { id: 'CUS003', name: 'UD Bahagia', phone: '083456789012', address: 'Jl. Sudirman No. 45, Bandung', email: 'bahagia@ud.com', piutang: 12000000, totalTransaksi: 'Rp 120.200.000' },
  { id: 'CUS004', name: 'PT Sentosa', phone: '084567890123', address: 'Jl. Gatot Subroto No. 200', email: 'sentosa@pt.com', piutang: 3200000, totalTransaksi: 'Rp 65.800.000' },
  { id: 'CUS005', name: 'Toko Jaya', phone: '085678901234', address: 'Jl. Merdeka No. 10, Surabaya', email: 'jaya@toko.id', piutang: 0, totalTransaksi: 'Rp 32.100.000' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const Customer = () => {
  const [customers] = useState<Customer[]>(initialCustomers);
  const [searchTerm, setSearchTerm] = useState('');
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [activeTab, setActiveTab] = useState('all');

  const filteredCustomers = customers.filter((cust) => {
    const matchesSearch =
      cust.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      cust.id.toLowerCase().includes(searchTerm.toLowerCase());
    
    if (activeTab === 'piutang') {
      return matchesSearch && cust.piutang > 0;
    }
    return matchesSearch;
  });

  const totalPiutang = customers.reduce((sum, c) => sum + c.piutang, 0);
  const customersWithPiutang = customers.filter((c) => c.piutang > 0).length;

  return (
    <MainLayout title="Customer" subtitle="Kelola data customer toko Anda">
      {/* Summary Cards */}
      <div className="mb-6 grid gap-4 md:grid-cols-3">
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Customer</p>
            <p className="text-2xl font-bold">{customers.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Customer dengan Piutang</p>
            <p className="text-2xl font-bold text-warning">{customersWithPiutang}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Piutang</p>
            <p className="text-2xl font-bold text-destructive">{formatCurrency(totalPiutang)}</p>
          </CardContent>
        </Card>
      </div>

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
          <Tabs value={activeTab} onValueChange={setActiveTab}>
            <TabsList>
              <TabsTrigger value="all">Semua</TabsTrigger>
              <TabsTrigger value="piutang">Piutang</TabsTrigger>
            </TabsList>
          </Tabs>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Tambah Customer
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Tambah Customer Baru</DialogTitle>
            </DialogHeader>
            <form className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Nama Customer</Label>
                <Input id="name" placeholder="Masukkan nama customer" />
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
                <Label htmlFor="address">Alamat</Label>
                <Input id="address" placeholder="Masukkan alamat lengkap" />
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
        {filteredCustomers.map((customer) => (
          <Card key={customer.id} className="transition-shadow hover:shadow-md">
            <CardHeader className="pb-3">
              <div className="flex items-start justify-between">
                <div>
                  <div className="flex items-center gap-2">
                    <p className="text-xs text-muted-foreground">{customer.id}</p>
                    {customer.piutang > 0 && (
                      <Badge variant="destructive" className="text-xs">
                        <AlertCircle className="mr-1 h-3 w-3" />
                        Piutang
                      </Badge>
                    )}
                  </div>
                  <CardTitle className="mt-1 text-lg">{customer.name}</CardTitle>
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
                {customer.phone}
              </div>
              <div className="flex items-start gap-2 text-sm text-muted-foreground">
                <MapPin className="mt-0.5 h-4 w-4 shrink-0" />
                {customer.address}
              </div>
              <div className="grid grid-cols-2 gap-2 border-t pt-3">
                <div>
                  <p className="text-xs text-muted-foreground">Total Transaksi</p>
                  <p className="font-semibold text-primary">{customer.totalTransaksi}</p>
                </div>
                {customer.piutang > 0 && (
                  <div>
                    <p className="text-xs text-muted-foreground">Piutang</p>
                    <p className="font-semibold text-destructive">{formatCurrency(customer.piutang)}</p>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </MainLayout>
  );
};

export default Customer;
