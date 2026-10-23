# LaundryKu - Tugas 2 Penerapan Teknologi Internet

**Nama:** Muhammad Anis  
**NIM:** 10124153  
**Kelas:** IF 4  

## Deskripsi
Aplikasi sistem informasi manajemen data pesanan laundry berbasis PHP Native dan MySQL/MariaDB.

## Cara Menjalankan
1. Import file `database.sql` ke MySQL (buat database dengan nama `laundryku`).
2. Sesuaikan kredensial database di `config.php`.
3. Letakkan folder project di `laragon/www/laundryku`.
4. Akses `http://localhost/laundryku/login.php` di browser.
5. Login menggunakan akun yang terdaftar di tabel `users`.

## Fitur
- Login & autentikasi berbasis session
- CRUD pesanan laundry (tambah, lihat, edit, hapus)
- Validasi form & sanitasi input
- Fitur dua bahasa (Indonesia / Inggris)
