# Memperbarui Infoscreening di cPanel

Paket ini untuk **memperbarui aplikasi yang sudah terpasang**, bukan instalasi baru. Proses ini tidak mengimpor konten atau mengganti `.env`.

## Persyaratan

- PHP **8.3 atau lebih baru** dengan ekstensi Laravel/Filament yang diperlukan, termasuk `pdo_mysql`, `intl`, `mbstring`, `openssl`, `xml`, `ctype`, `fileinfo`, dan `curl`.
- Document root domain tetap mengarah ke folder `public` aplikasi.
- Apache `mod_rewrite` aktif.

## 1. Buat ZIP pembaruan

Jalankan dari PowerShell di root project:

```powershell
.\deploy\cpanel\build.ps1
```

Hasilnya `deploy/cpanel/infoscreening-cpanel.zip`. Paket berisi kode aplikasi, `vendor/` production yang dibangun dari `composer.lock`, dan build Vite production. Paket **tidak** berisi `.env`, dump SQL, installer web, upload pengguna, atau cache konfigurasi.

Builder tidak menghapus atau mengubah folder `vendor` maupun database di project lokal.

## 2. Cadangkan dan unggah

1. Dari cPanel File Manager, buat salinan file aplikasi saat ini dan backup database lewat phpMyAdmin sebagai langkah pemulihan.
2. Upload ZIP ke folder aplikasi yang berisi `artisan` (bukan ke `public_html` kecuali itu memang folder aplikasi di luar document root).
3. Extract ZIP **ke folder aplikasi yang sama** dan izinkan file kode diganti.
4. Jangan hapus `.env`, database, `storage/app/public`, atau `public/storage`. ZIP tidak berisi file-file tersebut. Folder `vendor` boleh ditimpa dari paket, tetapi jangan hapus foldernya.
5. Pastikan `storage`, `storage/framework`, dan `bootstrap/cache` dapat ditulis oleh PHP.

## 3. Periksa cache dan situs

- Pertahankan nilai `APP_KEY`, koneksi database, dan konfigurasi lain di `.env`. Jangan membuat APP_KEY baru.
- Agar fitur lupa password dapat mengirim email, atur konfigurasi SMTP berikut di `.env` server. Isi `MAIL_PASSWORD` dengan password mailbox yang dibuat di cPanel; jangan masukkan password ke repositori atau paket ZIP.

  ```dotenv
  MAIL_MAILER=smtp
  MAIL_SCHEME=smtps
  MAIL_HOST=mail.infoscreening.id
  MAIL_PORT=465
  MAIL_USERNAME=reset@infoscreening.id
  MAIL_PASSWORD=
  MAIL_FROM_ADDRESS=reset@infoscreening.id
  MAIL_FROM_NAME="Infoscreening"
  ```

- Pastikan `APP_URL` di `.env` memakai URL HTTPS website agar tautan reset mengarah ke domain yang benar. Jika konfigurasi Laravel di-cache, hapus `bootstrap/cache/config.php` lewat File Manager agar pengaturan SMTP baru terbaca. Jangan menghapus `.env`.
- Paket tidak menjalankan migration atau seeder secara otomatis. Untuk rilis yang menyediakan kategori dan tag bawaan, jalankan seeder khusus di bawah ini setelah backup database. Seeder ini hanya menambahkan kategori/tag yang belum ada dan menyelaraskan tautan menu bawaan Artikel; seeder tidak membuat artikel atau menghapus konten.

  ```sh
  php artisan db:seed --class='Database\Seeders\DefaultTaxonomySeeder' --force
  ```

- Jangan impor SQL atau menjalankan migration untuk rilis ini.
- Jika aplikasi memakai cache konfigurasi/rute lama, hapus file cache konfigurasi dan rute yang dihasilkan di `bootstrap/cache` lewat File Manager; jangan menghapus `.env` atau data di `storage`.
- Pastikan `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` memakai domain `https://` yang benar. Aktifkan AutoSSL di cPanel bila perlu; reverse proxy harus meneruskan informasi HTTPS agar URL yang dihasilkan Laravel tetap aman.
- Buka situs dan `/admin`, lalu cek log terbaru di `storage/logs/laravel.log` jika terjadi error.

Paket ini sengaja tidak menyertakan `install.php`; tidak ada installer publik yang perlu diunggah atau dijalankan.

## 4. Jika simpan artikel menghasilkan 403

Pesan **"403 Forbidden — Access to this resource on the server is denied!"** berasal dari web server cPanel (ModSecurity / WAF), bukan dari Laravel. Request POST yang berisi HTML artikel sering dianggap serangan XSS oleh aturan WAF.

Perbaikan di cPanel:

1. Buka **cPanel → Security → ModSecurity™**.
2. Nonaktifkan ModSecurity untuk domain `infoscreening.id` (tombol menjadi **OFF**), lalu simpan.
3. Jika hosting memakai Imunify360, buka **Imunify360 → Firewall → WAF** dan matikan mode blocking untuk domain tersebut.
4. Coba simpan artikel kembali.

Jika ingin ModSecurity tetap aktif, cek `error_log` domain untuk baris `ModSecurity: Access denied with code 403 ... [id "XXXXXX"]`, lalu minta penyedia hosting meng-whitelist rule ID tersebut untuk domain.

Catatan: pastikan juga `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL=https://infoscreening.id` di `.env` server.

## 5. CI/CD via cPanel Git Version Control

Repo GitHub bersifat publik, jadi cPanel bisa clone langsung tanpa token. Alurnya:

1. Push ke `main` → GitHub Actions membangun `vendor/` dan `public/build/`, lalu force-push branch `deploy` berisi aplikasi siap jalan.
2. cPanel Git Version Control menarik branch `deploy` ke folder aplikasi.
3. Cron menjalankan migration + cache setiap 5 menit.

Tidak ada secrets GitHub yang perlu dibuat untuk alur ini.

### Sekali saja di cPanel

1. Buka **Files → Git Version Control → Create**.
   - Clone URL: `https://github.com/denisptra/infoscreening.git`
   - Repository Path: `/home/mcodgrfd/apps/next` (sesuaikan dengan path aplikasi kamu)
   - Setelah dibuat, buka **Terminal**:
     ```sh
     cd ~/apps/next
     git fetch origin deploy
     git checkout deploy
     ```
2. Salin `.env` dan upload dari instalasi lama:
   ```sh
   cp /path/lama/.env ~/apps/next/.env
   cp -a /path/lama/storage/. ~/apps/next/storage/
   ```
3. Di cPanel → **Domains** → `next.infoscreening.id` → **Document Root**, arahkan ke `/home/mcodgrfd/apps/next/public` (folder `public`, bukan folder aplikasi). Aktifkan AutoSSL/HTTPS.
4. Buat script deploy:
   ```sh
   mkdir -p ~/bin ~/logs
   nano ~/bin/deploy-infoscreening.sh
   ```
   Isi (sesuaikan `PHP_BIN` dan `GIT_BIN` dengan hasil `which php` dan `which git`):
   ```sh
   #!/bin/bash
   set -u

   APP_DIR="/home/mcodgrfd/apps/next"
   PHP_BIN="/usr/local/bin/php"
   GIT_BIN="/usr/bin/git"
   LOG="/home/mcodgrfd/logs/deploy-infoscreening.log"

   cd "$APP_DIR" || exit 1

   BEFORE=$("$GIT_BIN" rev-parse HEAD 2>/dev/null || echo none)
   "$GIT_BIN" fetch origin deploy >>"$LOG" 2>&1 || exit 1
   "$GIT_BIN" reset --hard origin/deploy >>"$LOG" 2>&1 || exit 1
   AFTER=$("$GIT_BIN" rev-parse HEAD)

   [ "$BEFORE" = "$AFTER" ] && exit 0

   echo "Deploy $AFTER $(date)" >>"$LOG"
   "$PHP_BIN" artisan migrate --force >>"$LOG" 2>&1
   "$PHP_BIN" artisan optimize:clear >>"$LOG" 2>&1
   "$PHP_BIN" artisan config:cache >>"$LOG" 2>&1
   "$PHP_BIN" artisan view:cache >>"$LOG" 2>&1
   "$PHP_BIN" artisan route:cache >>"$LOG" 2>&1
   "$PHP_BIN" artisan storage:link >>"$LOG" 2>&1 || true
   ```
   ```sh
   chmod +x ~/bin/deploy-infoscreening.sh
   ```
5. Tambah cron di cPanel → **Cron Jobs**:
   ```
   */5 * * * * /bin/bash /home/mcodgrfd/bin/deploy-infoscreening.sh >/dev/null 2>&1
   ```
6. Jalankan sekali dan cek log:
   ```sh
   bash ~/bin/deploy-infoscreening.sh
   tail -n 50 ~/logs/deploy-infoscreening.log
   ```

Catatan:

- `.env` dan upload di `storage/` tidak ikut di branch `deploy`, jadi tidak tertimpa.
- Jangan edit file aplikasi langsung di server; perubahan akan hilang saat cron berikutnya.
- Rollback: `git reset --hard <commit-lama>` di folder aplikasi, lalu jalankan ulang script.
- Pastikan `.env` server memakai `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://next.infoscreening.id`, `LOG_LEVEL=warning`, dan `FILESYSTEM_DISK=public`. Untuk SMTP port 587 gunakan `MAIL_SCHEME=smtp`; untuk port 465 gunakan `MAIL_SCHEME=smtps`.
