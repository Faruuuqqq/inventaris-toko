import { useState } from 'react';
import { NavLink, useLocation } from 'react-router-dom';
import {
  LayoutDashboard,
  Users,
  UserCheck,
  Package,
  Warehouse,
  BadgePercent,
  ShoppingCart,
  Banknote,
  CreditCard,
  Receipt,
  RotateCcw,
  FileText,
  ClipboardList,
  History,
  Wallet,
  BarChart3,
  Settings,
  ChevronDown,
  ChevronRight,
  LogOut,
  Truck,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/button';

interface MenuItem {
  title: string;
  icon: React.ElementType;
  path?: string;
  children?: MenuItem[];
}

const menuItems: MenuItem[] = [
  { title: 'Dashboard', icon: LayoutDashboard, path: '/dashboard' },
  {
    title: 'Data Utama',
    icon: Users,
    children: [
      { title: 'Supplier', icon: Truck, path: '/supplier' },
      { title: 'Customer', icon: UserCheck, path: '/customer' },
      { title: 'Produk', icon: Package, path: '/produk' },
      { title: 'Gudang', icon: Warehouse, path: '/gudang' },
      { title: 'Sales', icon: BadgePercent, path: '/sales' },
    ],
  },
  {
    title: 'Transaksi',
    icon: ShoppingCart,
    children: [
      { title: 'Pembelian', icon: ShoppingCart, path: '/transaksi/pembelian' },
      { title: 'Penjualan Tunai', icon: Banknote, path: '/transaksi/penjualan-tunai' },
      { title: 'Penjualan Kredit', icon: CreditCard, path: '/transaksi/penjualan-kredit' },
      { title: 'Pembayaran Utang', icon: Receipt, path: '/transaksi/pembayaran-utang' },
      { title: 'Pembayaran Piutang', icon: Receipt, path: '/transaksi/pembayaran-piutang' },
      { title: 'Retur Pembelian', icon: RotateCcw, path: '/transaksi/retur-pembelian' },
      { title: 'Retur Penjualan', icon: RotateCcw, path: '/transaksi/retur-penjualan' },
      { title: 'Surat Jalan', icon: FileText, path: '/transaksi/surat-jalan' },
      { title: 'Kontra Bon', icon: ClipboardList, path: '/transaksi/kontra-bon' },
    ],
  },
  {
    title: 'Informasi',
    icon: History,
    children: [
      { title: 'Histori Pembelian', icon: History, path: '/informasi/pembelian' },
      { title: 'Histori Penjualan', icon: History, path: '/informasi/penjualan' },
      { title: 'Histori Retur Pembelian', icon: History, path: '/informasi/retur-pembelian' },
      { title: 'Histori Retur Penjualan', icon: History, path: '/informasi/retur-penjualan' },
      { title: 'Biaya/Jasa', icon: Wallet, path: '/informasi/biaya-jasa' },
      { title: 'Histori Pembayaran Utang', icon: History, path: '/informasi/pembayaran-utang' },
      { title: 'Histori Pembayaran Piutang', icon: History, path: '/informasi/pembayaran-piutang' },
    ],
  },
  {
    title: 'Info Tambahan',
    icon: BarChart3,
    children: [
      { title: 'Saldo Piutang', icon: Wallet, path: '/info/saldo-piutang' },
      { title: 'Saldo Utang', icon: Wallet, path: '/info/saldo-utang' },
      { title: 'Saldo Stok', icon: Package, path: '/info/saldo-stok' },
      { title: 'Kartu Stok', icon: ClipboardList, path: '/info/kartu-stok' },
      { title: 'Laporan Harian', icon: BarChart3, path: '/info/laporan-harian' },
    ],
  },
  { title: 'Pengaturan', icon: Settings, path: '/pengaturan' },
];

const SidebarItem = ({ item, level = 0 }: { item: MenuItem; level?: number }) => {
  const [isOpen, setIsOpen] = useState(false);
  const location = useLocation();
  const hasChildren = item.children && item.children.length > 0;
  const isActive = item.path === location.pathname;
  const isChildActive = item.children?.some((child) => child.path === location.pathname);

  if (hasChildren) {
    return (
      <div>
        <button
          onClick={() => setIsOpen(!isOpen)}
          className={cn(
            'flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors',
            'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground',
            isChildActive && 'bg-sidebar-accent text-sidebar-accent-foreground'
          )}
        >
          <div className="flex items-center gap-3">
            <item.icon className="h-4 w-4" />
            <span>{item.title}</span>
          </div>
          {isOpen ? <ChevronDown className="h-4 w-4" /> : <ChevronRight className="h-4 w-4" />}
        </button>
        {isOpen && (
          <div className="ml-4 mt-1 space-y-1 border-l border-sidebar-border pl-3">
            {item.children?.map((child) => (
              <SidebarItem key={child.title} item={child} level={level + 1} />
            ))}
          </div>
        )}
      </div>
    );
  }

  return (
    <NavLink
      to={item.path || '#'}
      className={cn(
        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors',
        'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground',
        isActive && 'bg-sidebar-primary text-sidebar-primary-foreground'
      )}
    >
      <item.icon className="h-4 w-4" />
      <span>{item.title}</span>
    </NavLink>
  );
};

export const AppSidebar = () => {
  const { user, logout } = useAuth();

  return (
    <aside className="fixed left-0 top-0 z-40 flex h-screen w-64 flex-col bg-sidebar">
      <div className="flex h-16 items-center gap-3 border-b border-sidebar-border px-6">
        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-sidebar-primary">
          <Package className="h-4 w-4 text-sidebar-primary-foreground" />
        </div>
        <div>
          <h1 className="text-lg font-bold text-sidebar-foreground">TokoManager</h1>
        </div>
      </div>

      <nav className="flex-1 space-y-1 overflow-y-auto p-4">
        {menuItems.map((item) => (
          <SidebarItem key={item.title} item={item} />
        ))}
      </nav>

      <div className="border-t border-sidebar-border p-4">
        <div className="mb-3 flex items-center gap-3">
          <div className="flex h-9 w-9 items-center justify-center rounded-full bg-sidebar-accent">
            <span className="text-sm font-medium text-sidebar-accent-foreground">
              {user?.name.charAt(0)}
            </span>
          </div>
          <div className="flex-1">
            <p className="text-sm font-medium text-sidebar-foreground">{user?.name}</p>
            <p className="text-xs text-sidebar-muted capitalize">{user?.role}</p>
          </div>
        </div>
        <Button
          variant="ghost"
          className="w-full justify-start gap-2 text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
          onClick={logout}
        >
          <LogOut className="h-4 w-4" />
          Keluar
        </Button>
      </div>
    </aside>
  );
};
