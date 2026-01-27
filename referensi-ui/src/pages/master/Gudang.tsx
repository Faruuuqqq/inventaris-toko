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
import { Plus, Search, Pencil, Trash2, Warehouse, MapPin, Package, User } from 'lucide-react';
import { Badge } from '@/components/ui/badge';

interface GudangData {
  id: string;
  name: string;
  address: string;
  manager: string;
  totalProducts: number;
  totalValue: string;
  status: 'active' | 'inactive';
}

const initialGudang: GudangData[] = [
  { id: 'GDG001', name: 'Gudang Utama', address: 'Jl. Industri No. 1, Jakarta Utara', manager: 'Budi Santoso', totalProducts: 450, totalValue: 'Rp 125.000.000', status: 'active' },
  { id: 'GDG002', name: 'Gudang Cabang Bandung', address: 'Jl. Soekarno Hatta No. 88, Bandung', manager: 'Andi Wijaya', totalProducts: 280, totalValue: 'Rp 85.500.000', status: 'active' },
  { id: 'GDG003', name: 'Gudang Surabaya', address: 'Jl. Raya Darmo No. 45, Surabaya', manager: 'Siti Rahayu', totalProducts: 320, totalValue: 'Rp 92.000.000', status: 'active' },
  { id: 'GDG004', name: 'Gudang Semarang', address: 'Jl. Pemuda No. 12, Semarang', manager: 'Dedi Kurniawan', totalProducts: 0, totalValue: 'Rp 0', status: 'inactive' },
];

const Gudang = () => {
  const [gudangList] = useState<GudangData[]>(initialGudang);
  const [searchTerm, setSearchTerm] = useState('');
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  const filteredGudang = gudangList.filter(
    (g) =>
      g.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      g.id.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const activeGudang = gudangList.filter((g) => g.status === 'active').length;

  return (
    <MainLayout title="Gudang" subtitle="Kelola daftar gudang penyimpanan">
      {/* Summary */}
      <div className="mb-6 grid gap-4 md:grid-cols-3">
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Gudang</p>
            <p className="text-2xl font-bold">{gudangList.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Gudang Aktif</p>
            <p className="text-2xl font-bold text-success">{activeGudang}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Produk Tersimpan</p>
            <p className="text-2xl font-bold">{gudangList.reduce((sum, g) => sum + g.totalProducts, 0)}</p>
          </CardContent>
        </Card>
      </div>

      <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative w-full sm:w-72">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input
            placeholder="Cari gudang..."
            className="pl-9"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Tambah Gudang
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Tambah Gudang Baru</DialogTitle>
            </DialogHeader>
            <form className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Nama Gudang</Label>
                <Input id="name" placeholder="Masukkan nama gudang" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="address">Alamat</Label>
                <Input id="address" placeholder="Masukkan alamat gudang" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="manager">Pengelola</Label>
                <Input id="manager" placeholder="Nama pengelola gudang" />
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

      <div className="grid gap-4 md:grid-cols-2">
        {filteredGudang.map((gudang) => (
          <Card key={gudang.id} className="transition-shadow hover:shadow-md">
            <CardHeader className="pb-3">
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <Warehouse className="h-5 w-5 text-primary" />
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <p className="text-xs text-muted-foreground">{gudang.id}</p>
                      <Badge variant={gudang.status === 'active' ? 'default' : 'secondary'}>
                        {gudang.status === 'active' ? 'Aktif' : 'Tidak Aktif'}
                      </Badge>
                    </div>
                    <CardTitle className="mt-1 text-lg">{gudang.name}</CardTitle>
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
              <div className="flex items-start gap-2 text-sm text-muted-foreground">
                <MapPin className="mt-0.5 h-4 w-4 shrink-0" />
                {gudang.address}
              </div>
              <div className="flex items-center gap-2 text-sm text-muted-foreground">
                <User className="h-4 w-4" />
                Pengelola: {gudang.manager}
              </div>
              <div className="grid grid-cols-2 gap-4 border-t pt-3">
                <div className="flex items-center gap-2">
                  <Package className="h-4 w-4 text-muted-foreground" />
                  <div>
                    <p className="text-xs text-muted-foreground">Total Produk</p>
                    <p className="font-semibold">{gudang.totalProducts}</p>
                  </div>
                </div>
                <div>
                  <p className="text-xs text-muted-foreground">Nilai Stok</p>
                  <p className="font-semibold text-primary">{gudang.totalValue}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </MainLayout>
  );
};

export default Gudang;
