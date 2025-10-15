# Eventory (Event App MVC)

Eventory adalah starter kit manajemen event kampus/komunitas berbasis PHP murni dengan arsitektur MVC ringan. Aplikasi ini menyediakan autentikasi dasar, CRUD event, pengelolaan pendaftaran peserta & panitia, serta dashboard ringkas tanpa ketergantungan framework berat.

## Fitur Utama
- Autentikasi: register, login, logout dengan `password_hash`/`password_verify`.
- Dashboard ringkasan event, total pending peserta/panitia, dan sisa slot.
- CRUD penuh untuk entitas `events`, `participant_regs`, `committee_apps`.
- Form publik pendaftaran peserta & panitia dengan validasi kuota dan periode.
- Routing kustom, helper session & validator, view PHP dengan Tailwind CDN.
- Migrasi SQL manual melalui `php bin/migrate.php`.

## Prasyarat
- PHP 8.1+ dengan ekstensi `pdo_mysql` aktif.
- MySQL/MariaDB (disarankan MySQL 8.0+).
- Composer tidak diperlukan.

## Instalasi
1. Salin repo / ekstrak source ke direktori pilihan Anda.
2. Duplikat file `.env.example` menjadi `.env` dan sesuaikan kredensial database.
   ```bash
   cp .env.example .env
   ```
3. Buat database kosong sesuai nama pada `.env` (`event_app` bawaan).
4. Jalankan migrasi skema & seed status referensi:
   ```bash
   php bin/migrate.php
   ```
5. Jalankan server pengembangan PHP:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Akses aplikasi di `http://localhost:8000`.

## Admin Awal
Tidak ada data admin otomatis. Buat akun pertama lewat halaman register, contoh kredensial referensi:
- Email: `admin@eventory.test`
- Password: `Admin123!`

Aplikasi saat ini menganggap seluruh pengguna terautentikasi sebagai panitia/admin, sehingga akun pertama langsung dapat mengakses dashboard dan menu kelola.

## Struktur Direktori Ringkas
```
app/            # Controllers, Models, Core, Views
bootstrap/      # Autoload & helper
bin/            # Skrip CLI (migrasi)
config/         # Konfigurasi app & database
database/       # File migrasi SQL
public/         # Front controller
routes/         # Definisi rute
storage/logs/   # Log aplikasi & migrasi
```

## Alur Migrasi
`php bin/migrate.php` akan:
1. Membuat tabel referensi (users, events, statuses, registrations, dll).
2. Mengisi tabel `participant_statuses` & `committee_statuses` dengan nilai default.
3. Mencatat eksekusi ke tabel `migrations` sehingga tidak dijalankan dua kali.

Log migrasi tersimpan di `storage/logs/migrate.log`.

## Pengujian Cepat
Pastikan menjalankan langkah berikut setelah setup:

1. **CRUD Events**
   1. Login menggunakan akun admin.
   2. Buka menu `Events Manage` -> tambah event baru.
   3. Edit event yang baru dibuat, lalu hapus untuk memastikan alur DELETE bekerja.
2. **CRUD Participant Registrations**
   1. Kunjungi halaman event publik (`/events`) -> pilih event -> submit form peserta.
   2. Masuk ke `Participants` -> cek data masuk, ubah status, dan hapus data.
3. **CRUD Committee Applications**
   1. Dari halaman event publik, kirim form panitia.
   2. Di menu `Committees`, perbarui status aplikasi dan hapus data untuk menguji alur DELETE.

Tambahan: pastikan dashboard menampilkan total event, pending peserta/panitia, serta sisa slot setelah data uji dibuat.

## Menjalankan Server
Gunakan server bawaan PHP:
```bash
php -S localhost:8000 -t public
```

Jika ingin mengaktifkan mode produksi, set `APP_DEBUG=false` pada `.env`. Log kesalahan akan ditulis ke `storage/logs/app.log`.

## Keamanan & Catatan
- Seluruh query menggunakan prepared statement PDO.
- Data output di-escape dengan `htmlspecialchars` melalui helper `e()`.
- Middleware `AuthMiddleware` melindungi `/dashboard` dan seluruh rute `/manage/*`.
- Tidak ada CSRF token bawaan; gunakan dengan bijak untuk demo/skripsi.

Selamat membangun event dengan Eventory!
