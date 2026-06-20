# E-commerce REST API (Laravel 12)

REST API sederhana untuk sistem e-commerce menggunakan Laravel 12 dan PostgreSQL. 
Fitur utama meliputi autentikasi menggunakan Laravel Sanctum, sistem role (admin & user), serta manajemen Produk dan Order.

## Persyaratan Sistem

- PHP 8.2 atau lebih baru
- PostgreSQL
- Composer

## Cara Install & Run

1. **Clone/Download Repository**
   Pastikan Anda berada di direktori proyek.

2. **Install Dependencies**
   Buka terminal di folder proyek dan jalankan:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (.env)**
   Secara default file `.env` sudah diatur untuk terhubung ke database PostgreSQL.
   Jika Anda perlu mengubah kredensial (seperti password atau user), silakan buka file `.env` dan ubah bagian ini:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=rest-api-db
   DB_USERNAME=postgres
   DB_PASSWORD=your_password_here
   ```

4. **Jalankan Migrasi & Seeder Database**
   Untuk membuat tabel-tabel di database sekaligus mengisi data awal (seperti Akun Admin dan Produk dummy), jalankan perintah:
   ```bash
   php artisan migrate:fresh --seed
   ```
   Perintah ini sangat disarankan agar semua data awal langsung tersedia (termasuk User Admin dan Product untuk kebutuhan test order).

5. **Akun Test yang Tersedia (Otomatis)**
   Setelah menjalankan perintah di atas, Anda bisa login menggunakan akun berikut:
   - **Email:** `admin@mail.com`
   - **Password:** `password`
   - **Role:** `admin`

6. **Jalankan Local Server**
   ```bash
   php artisan serve
   ```
   API akan berjalan di `http://localhost:8000`.

## Testing

Proyek ini telah dilengkapi dengan **Feature Test** menggunakan PHPUnit. Untuk menjalankan tes, jalankan perintah berikut:
```bash
php artisan test
```

## Postman Collection

File Postman collection telah disediakan di dalam folder proyek ini dengan nama `Postman_Collection.json`.
Anda bisa mengimpor file tersebut langsung ke aplikasi Postman Anda untuk melakukan uji coba endpoint (Login, CRUD Produk, Order, dll).
