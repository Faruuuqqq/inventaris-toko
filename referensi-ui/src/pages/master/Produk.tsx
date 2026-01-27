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
import { Plus, Search, Pencil, Trash2, Package } from 'lucide-react';
import { Badge } from '@/components/ui/badge';

interface Product {
  id: string;
  name: string;
  category: string;
  buyPrice: number;
  sellPrice: number;
  stock: number;
  unit: string;
}

const categories = ['Makanan', 'Minuman', 'Sembako', 'Elektronik', 'Peralatan'];

const initialProducts: Product[] = [
  { id: 'PRD001', name: 'Beras Premium 5kg', category: 'Sembako', buyPrice: 65000, sellPrice: 75000, stock: 120, unit: 'Karung' },
  { id: 'PRD002', name: 'Minyak Goreng 2L', category: 'Sembako', buyPrice: 28000, sellPrice: 35000, stock: 85, unit: 'Botol' },
  { id: 'PRD003', name: 'Indomie Goreng', category: 'Makanan', buyPrice: 2500, sellPrice: 3500, stock: 500, unit: 'Pcs' },
  { id: 'PRD004', name: 'Aqua 600ml', category: 'Minuman', buyPrice: 3000, sellPrice: 4000, stock: 200, unit: 'Botol' },
  { id: 'PRD005', name: 'Gula Pasir 1kg', category: 'Sembako', buyPrice: 14000, sellPrice: 18000, stock: 150, unit: 'Kg' },
  { id: 'PRD006', name: 'Teh Botol Sosro', category: 'Minuman', buyPrice: 4500, sellPrice: 6000, stock: 180, unit: 'Botol' },
  { id: 'PRD007', name: 'Sabun Lifebuoy', category: 'Peralatan', buyPrice: 3500, sellPrice: 5000, stock: 90, unit: 'Pcs' },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const Produk = () => {
  const [products] = useState<Product[]>(initialProducts);
  const [searchTerm, setSearchTerm] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('all');
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  const filteredProducts = products.filter((prod) => {
    const matchesSearch = prod.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      prod.id.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesCategory = categoryFilter === 'all' || prod.category === categoryFilter;
    return matchesSearch && matchesCategory;
  });

  const totalValue = products.reduce((sum, p) => sum + (p.sellPrice * p.stock), 0);

  return (
    <MainLayout title="Produk" subtitle="Kelola daftar produk dan kategori">
      {/* Summary */}
      <div className="mb-6 grid gap-4 md:grid-cols-4">
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Produk</p>
            <p className="text-2xl font-bold">{products.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Kategori</p>
            <p className="text-2xl font-bold">{categories.length}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Total Stok</p>
            <p className="text-2xl font-bold">{products.reduce((sum, p) => sum + p.stock, 0)}</p>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-4">
            <p className="text-sm text-muted-foreground">Nilai Persediaan</p>
            <p className="text-2xl font-bold text-primary">{formatCurrency(totalValue)}</p>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="flex gap-4">
          <div className="relative w-full sm:w-72">
            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
            <Input
              placeholder="Cari produk..."
              className="pl-9"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
          <Select value={categoryFilter} onValueChange={setCategoryFilter}>
            <SelectTrigger className="w-40">
              <SelectValue placeholder="Kategori" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Kategori</SelectItem>
              {categories.map((cat) => (
                <SelectItem key={cat} value={cat}>{cat}</SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Tambah Produk
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Tambah Produk Baru</DialogTitle>
            </DialogHeader>
            <form className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="name">Nama Produk</Label>
                <Input id="name" placeholder="Masukkan nama produk" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="category">Kategori</Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Pilih kategori" />
                    </SelectTrigger>
                    <SelectContent>
                      {categories.map((cat) => (
                        <SelectItem key={cat} value={cat}>{cat}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="unit">Satuan</Label>
                  <Input id="unit" placeholder="Pcs, Kg, dll" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="buyPrice">Harga Beli</Label>
                  <Input id="buyPrice" type="number" placeholder="0" />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="sellPrice">Harga Jual</Label>
                  <Input id="sellPrice" type="number" placeholder="0" />
                </div>
              </div>
              <div className="space-y-2">
                <Label htmlFor="stock">Stok Awal</Label>
                <Input id="stock" type="number" placeholder="0" />
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

      {/* Products Table */}
      <Card>
        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Kode</TableHead>
                <TableHead>Nama Produk</TableHead>
                <TableHead>Kategori</TableHead>
                <TableHead className="text-right">Harga Beli</TableHead>
                <TableHead className="text-right">Harga Jual</TableHead>
                <TableHead className="text-center">Stok</TableHead>
                <TableHead className="text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredProducts.map((product) => (
                <TableRow key={product.id}>
                  <TableCell className="font-medium text-primary">{product.id}</TableCell>
                  <TableCell>
                    <div className="flex items-center gap-2">
                      <div className="flex h-8 w-8 items-center justify-center rounded bg-muted">
                        <Package className="h-4 w-4 text-muted-foreground" />
                      </div>
                      <span>{product.name}</span>
                    </div>
                  </TableCell>
                  <TableCell>
                    <Badge variant="secondary">{product.category}</Badge>
                  </TableCell>
                  <TableCell className="text-right">{formatCurrency(product.buyPrice)}</TableCell>
                  <TableCell className="text-right font-medium">{formatCurrency(product.sellPrice)}</TableCell>
                  <TableCell className="text-center">
                    <span className={`font-medium ${product.stock < 50 ? 'text-destructive' : ''}`}>
                      {product.stock} {product.unit}
                    </span>
                  </TableCell>
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

export default Produk;
