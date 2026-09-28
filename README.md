# IT Helpdesk

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-8.83-orange" alt="Laravel 8">
    <img src="https://img.shields.io/badge/PHP-%5E7.3%20%7C%20%5E8.0-blue" alt="PHP 7.3+ / 8.0+">
    <img src="https://img.shields.io/badge/license-MIT-green" alt="License MIT">
</p>

Sistem **IT Helpdesk** berbasis web yang dibangun dengan **Laravel 8** untuk mengelola tiket bantuan (support ticket), knowledge base, FAQ, serta dilengkapi modul khusus **General Affairs (GA) Request** dengan alur persetujuan berjenjang.

## Tentang Aplikasi

Aplikasi ini menggunakan framework Laravel + [nwidart/laravel-modules](https://github.com/nwidart/laravel-modules) sehingga fitur tambahan dikemas dalam modul. Tersedia dua sisi pengguna:

- **User / Customer** — membuat tiket, mengajukan GA Request, melihat status, serta mengunduh PDF.
- **Admin** — dashboard, manajemen tiket, kategori, pengguna, approver GA, export laporan, dan pengaturan sistem.

## Fitur Utama

- Manajemen tiket lengkap: tiket user/guest, prioritas, kategori & subkategori, penugasan agen, rating, riwayat aktivitas.
- Email-to-ticket via IMAP, auto-close, auto-overdue, dan auto-response terjadwal.
- Knowledge base, FAQ, pengumuman, departemen, dan halaman statis.
- Autentikasi dengan OTP, Google reCAPTCHA, mews captcha, honeypot anti-spam, dan login sosial (Envato, Zoho).
- Integrasi Envato untuk verifikasi lisensi produk.
- Dukungan banyak bahasa (terjemahan di dalam modul)

## Teknologi & Library

- **Laravel 8** (v8.83.29), PHP 7.3+ / 8.0+
- `nwidart/laravel-modules` — struktur modul
- `spatie/laravel-permission` — manajemen role & permission
- `spatie/laravel-medialibrary` — upload media/avatar
- `yajra/laravel-datatables` — tabel data interaktif
- `maatwebsite/excel` — export laporan Excel
- `barryvdh/laravel-dompdf` — generate PDF
- `laravel/socialite` + socialiteproviders — login sosial
- `mews/captcha`, `google/recaptcha`, `spatie/laravel-honeypot` — keamanan form
- `webklex/laravel-imap`, `torann/geoip`, `stichoza/google-translate-php`, dan lainnya

## Persyaratan Sistem

- PHP 7.3+ (disarankan 8.0) dengan ekstensi: `mbstring`, `gd`, `zip`, `curl`, `fileinfo`, `xml`, `dom`
- Composer
- MySQL / MariaDB
- Web server (Apache/XAMPP, Nginx, atau `php artisan serve`)

## Instalasi

Berikut langkah instalasi untuk lingkungan lokal (XAMPP) maupun server:

1. **Clone repository**

   ```bash
   git clone https://github.com/Kakilima09/it-helpdesk.git
   cd it-helpdesk
   ```

2. **Install dependensi Composer**

   ```bash
   composer install
   ```

3. **Konfigurasi environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Sesuaikan `.env` untuk koneksi database, mail, dan WhatsApp:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=it_helpdesk
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi & seeding database**

   ```bash
   php artisan migrate --seed
   php artisan db:seed --class=GaSetupSeeder
   ```

5. **Buat symbolic link storage**

   ```bash
   php artisan storage:link
   ```

6. **Jalankan aplikasi**

   ```bash
   php artisan serve
   ```

   Akses melalui `http://127.0.0.1:8000`. Jika memakai XAMPP, arahkan document root ke folder `public/`.

7. **Scheduler (untuk auto-close tiket & auto-expire GA di production)**

   ```bash
   crontab -e
   # tambahkan baris berikut
   * * * * * cd /path/to/it-helpdesk && php artisan schedule:run >> /dev/null 2>&1
   ```

## Lisensi

Proyek ini dirilis di bawah lisensi **MIT**. Lihat file [LICENSE](LICENSE) untuk detail.
