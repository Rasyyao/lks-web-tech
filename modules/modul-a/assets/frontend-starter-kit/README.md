# PintarMenabung - Frontend Starter Kit
**Lomba Kompetensi Siswa (LKS) Bidang Web Technologies 2026**
**Server Side Module - Phase 2 (Frontend Integration)**

Panduan starter kit frontend untuk integrasi aplikasi pengelolaan keuangan PintarMenabung dengan RESTful API Laravel Sanctum.

---

## 📁 Struktur Berkas Starter Kit
```
frontend-starter-kit/
├── index.html           # Halaman Overview (Daftar wallet & transaksi terbaru)
├── login.html           # Halaman Login
├── register.html        # Halaman Register
├── wallet-detail.html   # Halaman Detail Wallet (Chart.js doughnut, filter bulan/tahun, transfer)
├── js/
│   ├── api.js           # Konfigurasi Axios & API Client PintarMenabung
│   ├── utils.js         # Fungsi format mata uang, tanggal, grouping, & shortcuts
│   └── chart-example.js # Contoh inisialisasi Doughnut Chart menggunakan Chart.js
└── README.md            # Dokumentasi panduan pengerjaan
```

---

## ⚡ Fitur Utama Sesuai Kisi-Kisi LKS 2026

### 1. Document Title Dinamis
- Setiap halaman wajib memiliki judul dokumen yang merefleksikan halaman aktif:
  - `"Login - PintarMenabung"`
  - `"Register - PintarMenabung"`
  - `"Overview - PintarMenabung"`
  - `"Wallet Detail - PintarMenabung"`

### 2. Navigasi & Proteksi Rute
- Rute Overview (`/` atau `index.html`) dan Detail Wallet (`/wallets/:id` atau `wallet-detail.html`) hanya dapat diakses oleh user yang sudah login (memiliki Bearer token di `localStorage`).
- Jika user belum login mengakses halaman terproteksi, redirect otomatis ke `login.html`.
- Jika user yang sudah login mengakses `login.html` atau `register.html`, redirect otomatis ke `index.html`.
- Tampilkan tombol **Logout** di navbar saat sudah login. Klik logout akan memanggil endpoint `POST /api/auth/logout` dan menghapus token dari `localStorage`.

### 3. Tampilan Transaksi & Pengelompokan Tanggal
- Format tanggal transaksi harus ramah pengguna (human-readable), contoh: `"Jul 31, 2025"`.
- Tanggal hanya ditampilkan sekali per grup tanggal (disembunyikan bila tanggal sama dengan transaksi sebelumnya).
- Indikator kategori & nominal:
  - **Expense (Pengeluaran)**: Ikon/teks warna **Merah**, nominal diawali tanda `-` (contoh: `- IDR 50.000`).
  - **Income (Pemasukan)**: Ikon/teks warna **Hijau**, nominal diawali tanda `+` (contoh: `+ IDR 2.500.000`).
- Format mata uang konsisten: `"IDR 50.000"` atau `"USD 12"`.

### 4. Detail Wallet & Doughnut Chart
- Menampilkan nama wallet dan saldo kalkulasi saat ini.
- Edit nama wallet secara inline dengan klik nama wallet, ketik nama baru, lalu tekan `Enter` untuk menyimpan.
- Hapus wallet dengan mengosongkan input nama wallet lalu tekan `Enter`, dilanjutkan dengan dialog konfirmasi `"Ok"`.
- Filter transaksi berdasarkan bulan (1–12) dan tahun (rentang 2015–2030).
- Diagram donat (Doughnut Chart) menggunakan Chart.js yang mengambil data dari endpoint `/api/reports/summary-by-category/expense` dan `/api/reports/summary-by-category/income`, dengan label kategori di bagian bawah.

### 5. Pintasan Keyboard (Shortcuts)
- `Alt + W`: Membuka modal form "Add Wallet"
- `Alt + N`: Membuka modal form "Add Transaction"
- `Alt + T`: Membuka modal form "Transfer Money"
- `Esc`: Menutup modal/form yang sedang aktif

---

## 🛠️ Cara Menjalankan Frontend
Gunakan static web server lokal apa saja, misalnya:
```bash
# Menggunakan PHP built-in server
php -S localhost:3000

# Atau menggunakan Live Server (VS Code Extension)
# Atau menggunakan npx serve
npx serve .
```
Pastikan backend Laravel berjalan di `http://localhost:8000`.
