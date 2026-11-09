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
