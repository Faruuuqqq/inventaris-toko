import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Card, CardContent } from '@/components/ui/card';
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
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Plus, Search, Pencil, Trash2 } from 'lucide-react';

interface Supplier {
  id: string;
  name: string;
  phone: string;
  address: string;
  email: string;
  totalTransaksi: string;
}

const initialSuppliers: Supplier[] = [
  { id: 'SUP001', name: 'PT Sumber Makmur', phone: '021-5551234', address: 'Jl. Industri No. 45, Jakarta', email: 'info@sumbermakmur.com', totalTransaksi: 'Rp 125.000.000' },
  { id: 'SUP002', name: 'CV Berkah Jaya', phone: '022-4445678', address: 'Jl. Raya Bandung No. 12', email: 'berkah@jaya.id', totalTransaksi: 'Rp 85.500.000' },
  { id: 'SUP003', name: 'UD Sentosa Abadi', phone: '031-3334567', address: 'Jl. Pasar Baru No. 8, Surabaya', email: 'sentosa@abadi.com', totalTransaksi: 'Rp 67.200.000' },
  { id: 'SUP004', name: 'PT Indo Distributor', phone: '021-7778899', address: 'Jl. Gatot Subroto No. 100', email: 'sales@indodist.com', totalTransaksi: 'Rp 210.800.000' },
];

const Supplier = () => {
  const [suppliers] = useState<Supplier[]>(initialSuppliers);
  const [searchTerm, setSearchTerm] = useState('');
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  const filteredSuppliers = suppliers.filter(
    (sup) =>
      sup.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      sup.id.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <MainLayout title="Supplier" subtitle="Kelola data supplier toko Anda">
      <div className="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative w-full sm:w-72">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari supplier..."
            className="pl-9"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Tambah Supplier
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Tambah Supplier Baru</DialogTitle>
            </DialogHeader>
            <form className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Nama Supplier</Label>
                <Input id="name" placeholder="Masukkan nama supplier" />
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

      <Card>
        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Kode</TableHead>
                <TableHead>Nama Supplier</TableHead>
                <TableHead>Telepon</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Alamat</TableHead>
                <TableHead className="text-right">Total Transaksi</TableHead>
                <TableHead className="text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredSuppliers.map((supplier) => (
                <TableRow key={supplier.id}>
                  <TableCell className="font-medium text-primary">{supplier.id}</TableCell>
                  <TableCell className="font-medium">{supplier.name}</TableCell>
                  <TableCell>{supplier.phone}</TableCell>
                  <TableCell>{supplier.email}</TableCell>
                  <TableCell className="max-w-[200px] truncate">{supplier.address}</TableCell>
                  <TableCell className="text-right font-medium">{supplier.totalTransaksi}</TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-1">
                      <Button variant="ghost" size="icon" className="h-8 w-8">
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button variant="ghost" size="icon" className="h-8 w-8 text-destructive">
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </div>
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

export default Supplier;
