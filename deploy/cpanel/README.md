# Deploy Infoscreening ke cPanel

Paket ini dibuat untuk cPanel yang menyediakan PHP **8.3 atau lebih baru, tetapi di bawah PHP 9** (sesuai batas `^8.3` project), ekstensi `pdo_mysql`, dan Apache dengan `mod_rewrite`.

## Membuat paket

Dari PowerShell di root project:

```powershell
.\deploy\cpanel\build.ps1
```

Hasilnya adalah `deploy/cpanel/infoscreening-cpanel.zip`. Paket memuat kode aplikasi, migration, aset frontend yang sudah dibangun, dan dependency Composer production. Paket tidak memuat `.env`, database SQLite lokal, file upload, log, cache, test, `node_modules`, atau tooling lokal. Tidak ada data database yang dihapus atau dimasukkan ke arsip.

## Persiapan di cPanel

1. Di **MultiPHP Manager** atau **Select PHP Version**, pilih PHP 8.3 dan aktifkan `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, dan `zip`. Minta provider mengaktifkannya jika pilihan tidak tersedia.
2. Buat database MySQL dan user melalui **MySQL Database Wizard**, lalu berikan user akses ke database.
3. Buat folder aplikasi di luar `public_html`, misalnya `/home/USER/infoscreening`. Ekstrak isi ZIP ke folder tersebut.
4. Atur document root domain/subdomain ke folder `public` aplikasi, misalnya `/home/USER/infoscreening/public`. Jangan arahkan document root ke folder aplikasi utama. Jika cPanel tidak mengizinkan perubahan document root, minta bantuan provider hosting sebelum lanjut.
5. Salin `.env.production.example` menjadi `.env` di folder utama aplikasi. Isi `APP_URL` dan detail database cPanel. Jangan unggah `.env` ke Git atau membagikan isinya.
6. Di **Terminal** cPanel, dari folder aplikasi, jalankan:

   ```bash
   php artisan key:generate
   php artisan storage:link
   ```

   Simpan `APP_KEY` dan gunakan kembali saat update berikutnya; jangan membuat key baru untuk aplikasi yang sudah menyimpan data terenkripsi atau sesi.

7. Pastikan folder `storage` dan `bootstrap/cache` dapat ditulis oleh PHP. Jika database baru sudah siap, backup dahulu bila ada data, lalu jalankan `php artisan migrate --force`. Perintah ini membuat/memperbarui struktur tabel; tidak menjalankan seeder dan tidak dimaksudkan untuk mengosongkan database.
8. Setelah `.env` final, jalankan `php artisan config:cache`. Untuk perubahan konfigurasi berikutnya, jalankan `php artisan config:clear` lalu cache ulang.
9. Aktifkan SSL/HTTPS di cPanel. Pastikan `APP_DEBUG=false` tetap digunakan.

## Data dan file yang dipindahkan terpisah

ZIP sengaja tidak berisi database lokal, data akun/konten, file upload dari `storage/app/public`, `.env`, atau `APP_KEY`. Jika data atau upload tersebut penting, migrasikan secara terpisah melalui ekspor/impor database dan backup file setelah menentukan sumber serta targetnya. Jangan menjalankan seeder di production kecuali memang sudah ditinjau dan disetujui.

Jika hosting tidak menyediakan Terminal/SSH, jangan mencoba mengunggah file aplikasi ke `public_html` sebagai pengganti pengaturan document root. Minta provider mengaktifkan Terminal/Composer atau bantu menyiapkan document root yang menunjuk ke `public`.
