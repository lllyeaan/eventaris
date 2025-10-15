# Eventory (Event App)

Eventory adalah starter kit manajemen event kampus/komunitas berbasis PHP murni dengan arsitektur MVC. Aplikasi ini menyediakan CRUD event, pengelolaan pendaftaran peserta & panitia, serta dashboard ringkas

## Fitur Utama
- Autentikasi: register, login, logout
- Dashboard pribadi: hanya menampilkan statistik event milik pengguna.
- CRUD untuk entitas `events`, `participant_regs`, `committee_apps` (terbatas pada event milik pengguna).
- Form publik pendaftaran peserta & panitia dengan validasi kuota dan periode.
- Profil pengguna: ubah nama tampilan, ganti password (email tetap).
- Routing kustom, helper session & validator, view PHP dengan Tailwind CDN.
