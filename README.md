## 🏨 Luxury Hotel Management System
Aplikasi web ini adalah sistem manajemen kamar dan pemesanan hotel berbasis PHP, menggunakan database MySQL dan framework CSS Bootstrap 5 untuk tampilan yang modern dan premium (Navy & Gold).

---

## 🔑 Ringkasan Fitur Utama Berdasarkan Peran
| Peran | Modul | Deskripsi Fungsionalitas |
| :--- | :--- | :--- |
| **Admin** | **Manajemen Kamar** (`/admin/dashboard.php`) | **CRUD Lengkap** (Create, Read, Update, Delete) data kamar. Dilengkapi *Search* dan *Pagination* dinamis. |
| **User/Guest** | **Daftar Kamar** (`/user/rooms.php`) | Menampilkan semua kamar dan **Penawaran Eksklusif** (Diskon 30%) dengan desain Gold & Navy. |
| **User/Guest** | **Form Pencarian** | Mencari kamar berdasarkan tanggal (*check-in/out*) dan tipe kamar (Form *floating*). |
| **User** | **Pemesanan** (`/user/booking.php`) | Proses *booking* dengan perhitungan harga *real-time*. Mendukung metode **Cash** dan **QRIS**. |
| **User** | **User Dashboard** (`/user/user_dashboard.php`) | Melihat riwayat pemesanan yang telah dibuat. |
| **General** | **Autentikasi** (`/auth`) | *Login* dan *Logout* untuk memisahkan akses Admin dan Pengguna. |

---
## 💻 Kebutuhan Sistem

Untuk menjalankan aplikasi ini di lingkungan lokal (Local Development Environment):

  * **Server Web:** Apache / Nginx (Disarankan **Laragon** atau **XAMPP**).
  * **PHP Versi:** PHP 7.4 atau lebih tinggi (Disarankan **PHP 8.x**).
  * **Ekstensi PHP:** `php-pdo` (untuk MySQL) dan `php-gd` (untuk *upload* gambar).
  * **Database:** **MySQL** atau **MariaDB**.

-----

## ⚙️ Cara Instalasi dan Konfigurasi

### 1\. Clone Repositori

Tempatkan folder **`Luxury_Hotel`** di direktori *root* web server Anda (`htdocs` atau `www`).

### 2\. Konfigurasi Database (MySQL)

  * Buat *database* baru bernama **`hotel_db`**.

  * Impor *script* SQL awal (`db.hotel.sql` atau *script* *create table* Anda).

  * **WAJIB: Perbarui Struktur Tabel `bookings`**
    Jalankan *query* SQL berikut untuk menambahkan kolom yang diperlukan untuk fitur pemesanan:

    ```sql
    ALTER TABLE bookings
    ADD COLUMN total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ADD COLUMN payment_method VARCHAR(50) NULL,
    ADD COLUMN proof_image VARCHAR(255) NULL;
    ```

### 3\. Konfigurasi Koneksi PHP

Edit *file* **`db/database.php`** dengan kredensial *database* lokal Anda:

| Parameter | Contoh Nilai | Deskripsi |
| :--- | :--- | :--- |
| `$host` | `'localhost'` | Host database. |
| `$db` | `'hotel_db'` | Nama database. |
| `$user` | `'root'` | Username database. |
| `$pass` | `''` | Password database (biasanya kosong di Laragon/XAMPP). |

### 4\. Struktur Folder Asset

Pastikan *folder-folder* berikut ada dan memiliki izin tulis (**0777** untuk `proofs/`):

  * `assets/foto/`
  * `assets/foto/rooms/`
  * `assets/proofs/` **(WAJIB izin TULIS/WRITE)**

-----

## 📂 Struktur Folder Proyek

```
Luxury_Hotel/
├── admin/                 # Panel Admin (Manajemen Kamar)
├── assets/
│   ├── foto/              # Aset gambar umum (hotel.jpg, dll.)
│   ├── foto/rooms/        # Gambar kamar spesifik
│   └── proofs/            # Bukti Pembayaran (WAJIB 0777)
├── auth/                  # Sistem Autentikasi (login.php, logout.php)
├── db/                    # Konfigurasi Database
│   └── database.php
├── include/               # File Template (navbar.php, header.php)
└── user/                  # Area Pengguna
    ├── rooms.php          # Daftar Kamar & Promo (Halaman Utama)
    └── booking.php        # Form Pemesanan
```

Tentu\! Berdasarkan proyek PHP dan MySQL Anda, *environment config* utama yang dibutuhkan adalah file koneksi *database* (**`db/database.php`**).

Berikut adalah contoh lengkap file konfigurasi tersebut, yang harus Anda sesuaikan dengan pengaturan server lokal Anda (Laragon atau XAMPP):

-----

## ⚙️ Contoh Environment Config: `db/database.php`

File ini mendefinisikan kredensial yang dibutuhkan **PDO** untuk terhubung ke *database* **`hotel_db`**.

```php
<?php
// FILE: db/database.php

// ===========================================
// Pengaturan Koneksi MySQL/MariaDB
// ===========================================

// Ganti nilai di bawah ini sesuai konfigurasi lokal Anda
$host = 'localhost';        // Host database (biasanya localhost)
$db   = 'hotel_db';        // Nama database yang telah Anda buat
$user = 'root';             // Username database (default root untuk lokal)
$pass = '';                 // Password database (kosong jika menggunakan XAMPP/Laragon default)
$charset = 'utf8mb4';       // Karakter set

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    // Mengatur mode error ke Exception agar error PDO bisa di-catch
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    // Mengatur mode fetch default ke Associative Array
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    // Menonaktifkan emulasi prepared statement untuk keamanan
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Mencoba membuat instance koneksi PDO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Menampilkan pesan yang jelas jika koneksi gagal (HANYA untuk development)
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
    // Di lingkungan produksi, Anda bisa menggunakan:
    // die("Terjadi kesalahan koneksi database."); 
}
// Variabel $pdo sekarang berisi objek koneksi database yang siap digunakan.
?>
```

### 📋 Penjelasan Konfigurasi
Anda **wajib** mengubah nilai-nilai berikut di bagian atas *file* agar aplikasi dapat berjalan:
| Variabel | Deskripsi | Nilai Umum (Default Laragon/XAMPP) |
| :--- | :--- | :--- |
| **`$host`** | Lokasi *server* database. | `'localhost'` |
| **`$db`** | Nama *database* Anda. | `'hotel_db'` |
| **`$user`** | *Username* MySQL. | `'root'` |
| **`$pass`** | *Password* MySQL. | `''` (Kosong) |

Tentu! Berdasarkan semua file yang telah kita buat, berikut adalah panduan penggunaan lengkap untuk **Aplikasi Luxury Hotel Management System** Anda.

Panduan ini dibagi berdasarkan peran pengguna: **Admin** dan **User/Guest**.

---

## 🛠️ 1. Panduan untuk Administrator (Admin)

Area Admin digunakan untuk mengelola data kamar (CRUD) melalui **Dashboard**.

### A. Akses dan Login Admin

1.  **Akses:** Buka *browser* dan navigasikan ke `http://localhost/Luxury_Hotel/admin/dashboard.php`.
2.  **Kredensial Default:** Gunakan akun *default* (atau akun admin yang telah Anda buat):
    * **Username:** `admin`
    * **Password:** `12345` (atau *hash* yang setara di database).
3.  **Logout:** Tautan **Logout** berada di pojok kanan atas *Dashboard*.

### B. Manajemen Kamar (CRUD)

Semua tindakan manajemen dilakukan di `dashboard.php`.

| Fitur | Lokasi/Aksi | Deskripsi |
| :--- | :--- | :--- |
| **Tambah Cepat** | Formulir **"Add Room Quickly"** | Masukkan **Tipe**, **Nomor Kamar** (unik), **Harga**, dan **Deskripsi Singkat**. Klik **Save**. |
| **Lihat Data** | Tabel **"Room List"** | Data kamar tampil dalam bentuk tabel dengan *pagination* dan *search* di bagian atas. |
| **Edit Data** | Kolom **Action**, ikon **Pensil Kuning** (`<i class="fas fa-pencil-alt"></i>`) | Mengarahkan ke `room_edit.php` (membutuhkan implementasi terpisah) untuk memodifikasi detail kamar. |
| **Lihat Detail** | Kolom **Action**, ikon **Info Biru** (`<i class="fas fa-info-circle"></i>`) | Mengarahkan ke `room_detail.php` (membutuhkan implementasi terpisah) untuk melihat detail kamar secara lengkap. |
| **Hapus Data** | Kolom **Action**, ikon **Tempat Sampah Merah** (`<i class="fas fa-trash"></i>`) | Memunculkan *modal* **Konfirmasi Penghapusan**. Jika dikonfirmasi, data kamar dan gambar terkait akan dihapus permanen. |

---

## 👨‍💻 Panduan untuk Pengguna & Tamu (User/Guest)

Area pengguna difokuskan pada pencarian kamar, melihat promo, dan membuat pemesanan.

### A. Mencari dan Menjelajahi Kamar

1.  **Akses Utama:** Navigasikan ke `http://localhost/Luxury_Hotel/user/rooms.php`.
2.  **Header:** Halaman dibuka dengan *Hero Banner* (latar belakang foto hotel) dan formulir **Cari Kamar Tersedia** (*floating form*).
3.  **Pencarian:** Gunakan kolom **Check-in**, **Check-out**, dan **Tipe Kamar** di formulir *floating* untuk memfilter kamar yang tersedia.

### B. Proses Pemesanan Kamar (`booking.php`)

#### 1. Memulai Pemesanan

* **Pilihan Kamar:** Di bagian **Daftar Kamar Lengkap** atau **Penawaran Eksklusif**, klik tombol **Book Now** (jika sudah *login*) atau **Login to Book** (jika belum *login*).
* **Login Wajib:** Jika Anda belum *login*, Anda akan diarahkan ke `auth/login.php`.

#### 2. Melengkapi Formulir (`booking.php`)

Anda akan melihat *form* pemesanan dua langkah:

1.  **Durasi & Detail Tamu:**
    * Pilih **Tanggal Check-in** dan **Check-out**.
    * **Perhitungan Harga:** Saat tanggal dipilih, kotak **Total Harga** akan diperbarui secara dinamis menggunakan JavaScript (`$totalPrice = $roomPrice * diffDays`).

2.  **Metode Pembayaran:**
    * **Cash:** Pilih opsi ini untuk membayar di tempat. **Status pemesanan akan langsung menjadi Confirmed.**
    * **QRIS:** Pilih opsi ini untuk transfer. Anda harus **Upload Bukti Pembayaran** (kolom akan muncul dan menjadi wajib).

3.  **Mengirimkan Pesanan:**

| Metode | Status Awal di Database | Keterangan |
| :--- | :--- | :--- |
| **Cash** | `confirmed` | Pemesanan dikonfirmasi secara otomatis. |
| **QRIS** | `pending` | Membutuhkan **Verifikasi Manual oleh Admin** setelah bukti diunggah. |

#### 3. Setelah Pemesanan

* Anda akan di-*redirect* ke **Dashboard Pengguna** (`user/user_dashboard.php`).
* Pesan konfirmasi akan muncul, menunjukkan detail pemesanan dan statusnya.

### C. Dashboard Pengguna (`user/user_dashboard.php`)

* **Akses:** Otomatis setelah *login* atau pemesanan berhasil.
* **Fitur:** Menampilkan **Riwayat Pemesanan Anda**, termasuk status (`pending`, `confirmed`, `cancelled`), total harga, dan metode pembayaran.

## 🖼️ Screenshot Aplikasi
## 🏠 Beranda
<img width="1781" height="893" alt="image" src="https://github.com/user-attachments/assets/ae187969-22d9-476c-9561-85a8039a82ba" />

## 🔑 Login
<img width="680" height="587" alt="image" src="https://github.com/user-attachments/assets/5cedf7f6-e710-4fbb-a684-2d2e803de235" />

