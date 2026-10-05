# Platform Seleksi & Inkubasi LKS Web Technologies
### SMK Telkom Purwokerto — WorldSkills Standard (Skill 17)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat-square&logo=php)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat-square&logo=livewire)](https://livewire.laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=flat-square&logo=filament)](https://filamentphp.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4-38B2AC?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![Tests](https://img.shields.io/badge/Tests-110%20Passed-10B981?style=flat-square&logo=checkmarx)](tests)

Platform terpadu seleksi, pelatihan intensif, dan inkubasi calon delegasi **LKS (Lomba Kompetensi Siswa) Bidang Lomba Web Technologies** SMK Telkom Purwokerto. Didesain secara khusus mengikuti kurikulum standar **WorldSkills Competition (Skill 17)** dan kisi-kisi resmi LKS SMK tingkat Kabupaten, Provinsi, hingga Nasional.

---

## 🌟 Fitur Utama

### 1. Landing Page Publik Interaktif (`/`)
- Desain modern bernuansa Telkom School (*Crimson Accent*).
- Showcase modul kompetisi riil (Client-Side & Server-Side).
- Preview live papan peringkat top calon delegasi.
- Visualisasi roadmap kurikulum 9 level materi dan alur 4 tahap seleksi.

### 2. Dashboard Siswa Berorientasi KPI (`/beranda`)
- **4 Kartu Statistik Terukur:**
  - **Modul Selesai:** Total modul yang telah dikerjakan & dinilai.
  - **Roadmap Belajar:** Persentase kelulusan materi & kuis level.
  - **Keaktifan Belajar:** Poin aktivitas kuis, latihan, dan submission.
  - **Posisi Peringkat:** Ranking dinamis dari seluruh peserta seleksi.
- **Katalog Modul dengan Track & Timestamp:**
  - Kategori jelas: **Client-side** vs **Server-side**.
  - Tanggal upload (*Diupload: Hari, Tanggal*) dan batas tenggat (*Deadline: Hari, Tanggal, Jam*).
  - Status submission visual (*Sudah Dikumpulkan* vs *Belum Dikumpulkan*).

### 3. Modul Praktik Riil LKS (`/modul`)
- Deskripsi spesifikasi proyek, rubric penilaian per section poin, dan starter kit template.
- Upload submission kode proyek berupa file archive `.ZIP`.
- **Locking Mechanism:** Pengumpulan otomatis terkunci setelah submit dengan banner konfirmasi waktu pengumpulan.
- Riwayat submission dan catatan evaluasi/skor dari mentor & automated grader.

### 4. Roadmap Belajar Terkunci & Kuis Checkpoint (`/materi`)
- 9 Tingkatan Level terstruktur (Level 0 Fondasi s/d Level 8 High-Performance Fullstack).
- **Sequential Unlocking:** Level berikutnya terkunci hingga siswa menyelesaikan materi dan **lulus kuis checkpoint** level aktif.
- Kuis pilihan ganda interaktif dengan pembahasan langsung dan perolehan poin instan.

### 5. Bank Soal & Starter Kit Resmi (`/bank-soal`)
- Arsip berkas soal LKS masa lampau tingkat Kabupaten, Provinsi Jawa Tengah, dan Nasional.
- Akses unduh berkas PDF Soal, Starter Pack Aset Gambar/Media, dan SQL Database Dumps.

### 6. Papan Peringkat Live & Tier Humoris (`/peringkat`)
- **Podium Juara 1, 2, dan 3** berdesain esports trophy dengan medali emas, perak, dan perunggu.
- **Student Tier Humoris:**
  - 🥇 Peringkat 1: `OTW Nasional Ini Mah 🚀`
  - 🥈 Peringkat 2: `Ayo Masa Tahun Depan 🔥`
  - 🥉 Peringkat 3: `Minimal Usaha Dulu ☕`
  - 🏃 Peringkat 4–5: `Masih Pemanasan 🏃`
  - 🎮 Peringkat 6+: `AFK di Lobby 🎮`
- Filter leaderboard berdasarkan kelas / angkatan cohort (RPL 1, RPL 2, Seleksi LKS 2026).
- Rincian akumulasi skor: Poin Kuis + Poin Modul + Poin Keaktifan.

### 7. Portal Mentor & Guru Pembimbing
- Inbox peninjauan submission tugas modul siswa.
- Penilaian kriteria obyektif, input nilai per butir soal, dan catatan koreksi/feedback.

### 8. Panel Manajemen Admin Filament (`/admin`)
- Pengelolaan akun pengguna, penetapan peran (Admin, Mentor, Siswa).
- Pengelompokan Cohort (kelas atau kontingen seleksi).
- Pembuatan dan pembaruan modul praktik, materi roadmap, bank soal, dan rekalkulasi skor.

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| **Framework Backend** | [Laravel 12](https://laravel.com) (PHP 8.5) |
| **Reactivity & Frontend** | [Livewire 3](https://livewire.laravel.com) & [Alpine.js](https://alpinejs.dev) |
| **Admin Panel** | [Filament 3](https://filamentphp.com) |
| **Styling & Design System** | [Tailwind CSS v4](https://tailwindcss.com) + SMK Telkom Purwokerto Tokens |
| **Otorisasi & Role** | [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) |
| **Database** | SQLite (Development/Testing) / MySQL (Production) |
| **Code Quality & Testing** | PHPUnit (110 Feature & Integration Tests) & Laravel Pint |

---

## 🚀 Panduan Instalasi & Menjalankan

### Kebutuhan Sistem
- **PHP** >= 8.2 (Direkomendasikan PHP 8.4 atau 8.5)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **Git**

### Langkah Setup

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/Rasyyao/lks-web-tech.git
   cd lks-web-tech
   ```

2. **Install Dependensi PHP & JavaScript:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeder Data Awal:**
   ```bash
   php artisan migrate --seed
   ```
   *Seeder akan membuat modul LKS Client/Server, roadmap 9 level, bank soal arsip, dan akun-akun demo.*

5. **Build Aset Frontend:**
   ```bash
   npm run build
   ```
   *Atau jalankan `npm run dev` untuk hot reload saat development.*

6. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka browser di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔑 Akun Demo (Bawaan Seeder)

Semua akun demo menggunakan kata sandi default: `password123`

| Role | Nama Pengguna / NIS | Email | Akses Halaman |
|---|---|---|---|
| **Admin** | `admin` | `admin@smktelkom-pwt.sch.id` | Panel Admin (`/admin`) |
| **Mentor 1** | `mentor1` | `budi@smktelkom-pwt.sch.id` | Inbox Mentor (`/mentor/submissions`) |
| **Mentor 2** | `mentor2` | `siti@smktelkom-pwt.sch.id` | Inbox Mentor (`/mentor/submissions`) |
| **Siswa (Top 1)** | `541221001` | `dewi@student.smktelkom-pwt.sch.id` | Portal Siswa (`/beranda`) |
| **Siswa (Top 2)** | `541221002` | `raka@student.smktelkom-pwt.sch.id` | Portal Siswa (`/beranda`) |
| **Siswa (Top 3)** | `541221003` | `dimas@student.smktelkom-pwt.sch.id` | Portal Siswa (`/beranda`) |
| **Siswa (Ganti Password)** | `541221004` | `putri@student.smktelkom-pwt.sch.id` | Demo Paksa Ganti Sandi Pertama Kali |

---

## 🧪 Pengujian & Kode Bersih

Aplikasi ini dilengkapi dengan pengujian menyeluruh (Unit, Feature, dan Integration) untuk seluruh peran:

```bash
# Menjalankan seluruh test suite (110 tests, 565 assertions)
php artisan test --compact

# Memeriksa dan memformat standar kode PHP (Laravel Pint)
vendor/bin/pint --format agent
```

---

## 📁 Struktur Direktori Utama

```
lks-web-tech/
├── app/
│   ├── Enums/                 # Enums: ModuleTrack, ModuleStatus, CohortType
│   ├── Filament/Admin/        # Resource & Halaman Panel Admin
│   ├── Http/Controllers/      # LandingController, Auth, Download handlers
│   ├── Livewire/
│   │   ├── Shared/            # LeaderboardPage, Profile
│   │   └── Student/           # Dashboard, ModuleList, ModuleShow, RoadmapShow, BankSoal
│   ├── Models/                # User, Module, Submission, RoadmapPage, Cohort, etc.
│   └── Services/              # Scorer, LeaderboardCalculator, ZipVerifier
├── database/
│   ├── migrations/            # Struktur skema tabel
│   └── seeders/               # DatabaseSeeder (Modul LKS riil, bank soal, kurikulum)
├── resources/
│   ├── css/                   # Tailwind v4 configuration & tokens.css
│   └── views/
│       ├── components/        # Blade layout & sidebar navigasi
│       ├── landing.blade.php  # Public Landing Page
│       └── livewire/          # Template Livewire komponen siswa & mentor
├── routes/
│   └── web.php                # Rute publik, siswa, mentor, dan shared
└── tests/
    └── Feature/               # Student, Mentor, Admin & Landing Integration Tests
```

---

## 📜 Standar & Lisensi

Dibuat untuk kebutuhan seleksi dan pelatihan perwakilan delegasi kompetisi:
- **Sekolah:** SMK Telkom Purwokerto
- **Bidang Lomba:** LKS SMK Web Technologies / WorldSkills Skill 17
- **Hak Cipta:** &copy; 2026 SMK Telkom Purwokerto.
