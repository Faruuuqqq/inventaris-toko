import { MainLayout } from '@/components/layout/MainLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  TrendingUp,
  TrendingDown,
  Package,
  Users,
  ShoppingCart,
  Wallet,
  ArrowUpRight,
  ArrowDownRight,
} from 'lucide-react';
import { useAuth } from '@/contexts/AuthContext';

const stats = [
  {
    title: 'Total Penjualan Hari Ini',
    value: 'Rp 12.450.000',
    change: '+12.5%',
    trend: 'up',
    icon: TrendingUp,
  },
  {
    title: 'Total Pembelian Hari Ini',
    value: 'Rp 8.200.000',
    change: '+5.2%',
    trend: 'up',
    icon: ShoppingCart,
  },
  {
    title: 'Stok Produk',
    value: '1.234',
    change: '-2.1%',
    trend: 'down',
    icon: Package,
  },
  {
    title: 'Customer Aktif',
    value: '156',
    change: '+8.3%',
    trend: 'up',
    icon: Users,
  },
];

const recentTransactions = [
  { id: 'TRX001', customer: 'Toko Makmur', type: 'Penjualan', amount: 'Rp 2.500.000', status: 'Lunas' },
  { id: 'TRX002', customer: 'CV Sejahtera', type: 'Penjualan Kredit', amount: 'Rp 5.800.000', status: 'Kredit' },
  { id: 'TRX003', customer: 'PT Abadi', type: 'Pembelian', amount: 'Rp 12.000.000', status: 'Lunas' },
  { id: 'TRX004', customer: 'Toko Bahagia', type: 'Penjualan', amount: 'Rp 1.200.000', status: 'Lunas' },
  { id: 'TRX005', customer: 'UD Sentosa', type: 'Retur', amount: 'Rp 350.000', status: 'Selesai' },
];

const lowStockItems = [
  { name: 'Beras Premium 5kg', stock: 12, minStock: 50 },
  { name: 'Minyak Goreng 2L', stock: 8, minStock: 30 },
  { name: 'Gula Pasir 1kg', stock: 15, minStock: 40 },
  { name: 'Tepung Terigu 1kg', stock: 5, minStock: 25 },
];

const Dashboard = () => {
  const { user } = useAuth();

  return (
    <MainLayout title="Dashboard" subtitle={`Selamat datang, ${user?.name}`}>
      {/* Stats Grid */}
      <div className="mb-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {stats.map((stat) => (
          <Card key={stat.title}>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                  <stat.icon className="h-5 w-5 text-primary" />
                </div>
                <div className={`flex items-center gap-1 text-sm ${
                  stat.trend === 'up' ? 'text-success' : 'text-destructive'
                }`}>
                  {stat.change}
                  {stat.trend === 'up' ? (
                    <ArrowUpRight className="h-4 w-4" />
                  ) : (
                    <ArrowDownRight className="h-4 w-4" />
                  )}
                </div>
              </div>
              <div className="mt-4">
                <p className="text-2xl font-bold text-foreground">{stat.value}</p>
                <p className="text-sm text-muted-foreground">{stat.title}</p>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Recent Transactions */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <ShoppingCart className="h-5 w-5" />
              Transaksi Terbaru
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b text-left text-sm text-muted-foreground">
                    <th className="pb-3 font-medium">ID</th>
                    <th className="pb-3 font-medium">Customer</th>
                    <th className="pb-3 font-medium">Tipe</th>
                    <th className="pb-3 font-medium">Jumlah</th>
                    <th className="pb-3 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody>
                  {recentTransactions.map((tx) => (
                    <tr key={tx.id} className="border-b last:border-0">
                      <td className="py-3 text-sm font-medium text-primary">{tx.id}</td>
                      <td className="py-3 text-sm">{tx.customer}</td>
                      <td className="py-3 text-sm">{tx.type}</td>
                      <td className="py-3 text-sm font-medium">{tx.amount}</td>
                      <td className="py-3">
                        <span className={`inline-flex rounded-full px-2 py-1 text-xs font-medium ${
                          tx.status === 'Lunas' 
                            ? 'bg-success/10 text-success'
                            : tx.status === 'Kredit'
                            ? 'bg-warning/10 text-warning'
                            : 'bg-muted text-muted-foreground'
                        }`}>
                          {tx.status}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>

        {/* Low Stock Alert */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <TrendingDown className="h-5 w-5 text-destructive" />
              Stok Menipis
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {lowStockItems.map((item) => (
                <div key={item.name} className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium">{item.name}</p>
                    <p className="text-xs text-muted-foreground">Min: {item.minStock}</p>
                  </div>
                  <span className="rounded-full bg-destructive/10 px-2 py-1 text-sm font-medium text-destructive">
                    {item.stock}
                  </span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Quick Actions */}
      <div className="mt-6">
        <h3 className="mb-4 text-lg font-semibold">Aksi Cepat</h3>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {[
            { title: 'Buat Penjualan', icon: ShoppingCart, color: 'bg-primary' },
            { title: 'Terima Pembayaran', icon: Wallet, color: 'bg-success' },
            { title: 'Tambah Produk', icon: Package, color: 'bg-accent-foreground' },
            { title: 'Lihat Laporan', icon: TrendingUp, color: 'bg-warning' },
          ].map((action) => (
            <button
              key={action.title}
              className="flex items-center gap-3 rounded-lg border border-border bg-card p-4 transition-colors hover:bg-accent"
            >
              <div className={`flex h-10 w-10 items-center justify-center rounded-lg ${action.color}`}>
                <action.icon className="h-5 w-5 text-primary-foreground" />
              </div>
              <span className="font-medium">{action.title}</span>
            </button>
          ))}
        </div>
      </div>
    </MainLayout>
  );
};

export default Dashboard;
