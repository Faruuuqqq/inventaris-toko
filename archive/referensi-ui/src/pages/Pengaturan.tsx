import { MainLayout } from '@/components/layout/MainLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { useAuth } from '@/contexts/AuthContext';
import { Settings, User, Store, Lock, Bell, Shield } from 'lucide-react';
import { Badge } from '@/components/ui/badge';

const Pengaturan = () => {
  const { user } = useAuth();

  return (
    <MainLayout title="Pengaturan" subtitle="Kelola pengaturan aplikasi">
      <div className="grid gap-6 lg:grid-cols-3">
        <div className="lg:col-span-2 space-y-6">
          {/* Profile Settings */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <User className="h-5 w-5" />
                Profil Pengguna
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center gap-4">
                <div className="flex h-16 w-16 items-center justify-center rounded-full bg-primary text-2xl font-bold text-primary-foreground">
                  {user?.name.charAt(0)}
                </div>
                <div>
                  <p className="text-lg font-semibold">{user?.name}</p>
                  <div className="flex items-center gap-2">
                    <Badge variant="secondary" className="capitalize">{user?.role}</Badge>
                    <span className="text-sm text-muted-foreground">{user?.email}</span>
                  </div>
                </div>
              </div>
              <Separator />
              <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <Label>Nama Lengkap</Label>
                  <Input defaultValue={user?.name} />
                </div>
                <div className="space-y-2">
                  <Label>Email</Label>
                  <Input defaultValue={user?.email} type="email" />
                </div>
              </div>
              <Button>Simpan Perubahan</Button>
            </CardContent>
          </Card>

          {/* Store Settings */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Store className="h-5 w-5" />
                Informasi Toko
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <Label>Nama Toko</Label>
                  <Input defaultValue="Toko Sejahtera" />
                </div>
                <div className="space-y-2">
                  <Label>No. Telepon</Label>
                  <Input defaultValue="021-5551234" />
                </div>
              </div>
              <div className="space-y-2">
                <Label>Alamat</Label>
                <Input defaultValue="Jl. Raya Pasar No. 123, Jakarta" />
              </div>
              <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <Label>NPWP</Label>
                  <Input placeholder="Masukkan NPWP" />
                </div>
                <div className="space-y-2">
                  <Label>No. Izin Usaha</Label>
                  <Input placeholder="Masukkan No. SIUP" />
                </div>
              </div>
              <Button>Simpan Perubahan</Button>
            </CardContent>
          </Card>

          {/* Security Settings */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Lock className="h-5 w-5" />
                Keamanan
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label>Password Saat Ini</Label>
                <Input type="password" placeholder="••••••••" />
              </div>
              <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                  <Label>Password Baru</Label>
                  <Input type="password" placeholder="••••••••" />
                </div>
                <div className="space-y-2">
                  <Label>Konfirmasi Password</Label>
                  <Input type="password" placeholder="••••••••" />
                </div>
              </div>
              <Button>Ubah Password</Button>
            </CardContent>
          </Card>
        </div>

        <div className="space-y-6">
          {/* Notification Settings */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Bell className="h-5 w-5" />
                Notifikasi
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="font-medium">Stok Menipis</p>
                  <p className="text-sm text-muted-foreground">Peringatan saat stok rendah</p>
                </div>
                <Switch defaultChecked />
              </div>
              <Separator />
              <div className="flex items-center justify-between">
                <div>
                  <p className="font-medium">Piutang Jatuh Tempo</p>
                  <p className="text-sm text-muted-foreground">Pengingat piutang</p>
                </div>
                <Switch defaultChecked />
              </div>
              <Separator />
              <div className="flex items-center justify-between">
                <div>
                  <p className="font-medium">Laporan Harian</p>
                  <p className="text-sm text-muted-foreground">Kirim laporan via email</p>
                </div>
                <Switch />
              </div>
            </CardContent>
          </Card>

          {/* Access Control */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="h-5 w-5" />
                Kontrol Akses
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-3">
              <div className="rounded-lg border p-3">
                <div className="flex items-center justify-between">
                  <span className="font-medium">Owner</span>
                  <Badge>Akses Penuh</Badge>
                </div>
                <p className="mt-1 text-sm text-muted-foreground">
                  Dapat mengakses semua fitur termasuk laporan dan pengaturan
                </p>
              </div>
              <div className="rounded-lg border p-3">
                <div className="flex items-center justify-between">
                  <span className="font-medium">Admin</span>
                  <Badge variant="secondary">Terbatas</Badge>
                </div>
                <p className="mt-1 text-sm text-muted-foreground">
                  Dapat mengelola transaksi harian dan data master
                </p>
              </div>
            </CardContent>
          </Card>

          {/* App Info */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Settings className="h-5 w-5" />
                Tentang Aplikasi
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-2 text-sm">
              <div className="flex justify-between">
                <span className="text-muted-foreground">Versi</span>
                <span>1.0.0</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Build</span>
                <span>2024.01.20</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Lisensi</span>
                <span>Enterprise</span>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </MainLayout>
  );
};

export default Pengaturan;
