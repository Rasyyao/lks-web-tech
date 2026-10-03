<?php

namespace Database\Seeders;

use App\Enums\CompetitionLevel;
use App\Enums\CompetitionModuleType;
use App\Models\QuestionBankPaper;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first() ?? User::first();
        $mentor1 = User::where('username', 'mentor1')->first() ?? $admin;
        $mentor2 = User::where('username', 'mentor2')->first() ?? $admin;

        $papers = [
            [
                'title' => 'LKS Nasional XXXII 2024 - Modul Server-Side PintarMenabung (REST API)',
                'slug' => 'lks-nasional-xxxii-2024-server-pintarmenabung',
                'description' => 'Paket soal resmi LKS SMK Tingkat Nasional 2024 bidang Web Technologies. Mencakup arsitektur RESTful API Laravel Sanctum, transaksi keuangan atomik, dan pelaporan bulanan.',
                'year' => 2024,
                'level' => CompetitionLevel::Nasional,
                'module_type' => CompetitionModuleType::Server,
                'file_path' => 'question-bank/lks-nasional-2024-server-pintarmenabung.pdf',
                'file_name' => 'LKS-Nasional-2024-Server-Side-PintarMenabung.pdf',
                'file_size' => 8149910,
                'uploaded_by' => $admin?->id,
                'download_count' => 142,
            ],
            [
                'title' => 'LKS Provinsi Jawa Tengah 2025 - Modul Client-Side Interactive Map & Transit',
                'slug' => 'lks-provinsi-jateng-2025-client-map',
                'description' => 'Dokumen soal seleksi LKS Tingkat Provinsi Jawa Tengah 2025. Peserta diminta membangun aplikasi peta interaktif pulau Jawa berbasis SVG & JavaScript murni dengan algoritma pencarian rute.',
                'year' => 2025,
                'level' => CompetitionLevel::Provinsi,
                'module_type' => CompetitionModuleType::Client,
                'file_path' => 'question-bank/lks-provinsi-2025-client-map.pdf',
                'file_name' => 'LKS-Jateng-2025-Client-Side-Map.pdf',
                'file_size' => 8149910,
                'uploaded_by' => $mentor1?->id,
                'download_count' => 98,
            ],
            [
                'title' => 'LKS Kabupaten Banyumas 2026 - Modul Server-Side POS & Inventaris Apotek',
                'slug' => 'lks-kabupaten-banyumas-2026-server-pos',
                'description' => 'Berkas soal seleksi LKS Tingkat Kabupaten Banyumas 2026. Fokus pada pembuatan REST API Point of Sales kasir, manajemen stok obat berbatch, dan otentikasi multi-role kasir/gudang.',
                'year' => 2026,
                'level' => CompetitionLevel::Kabupaten,
                'module_type' => CompetitionModuleType::Server,
                'file_path' => 'question-bank/lks-kabupaten-2026-server-rest-api.pdf',
                'file_name' => 'LKS-Kab-Banyumas-2026-Server-Side-POS.pdf',
                'file_size' => 8149910,
                'uploaded_by' => $mentor2?->id,
                'download_count' => 45,
            ],
            [
                'title' => 'LKS Nasional XXXIII 2025 - Modul Client-Side High-Speed Dashboard & Analytics',
                'slug' => 'lks-nasional-xxxiii-2025-client-analytics',
                'description' => 'Soal seleksi LKS Nasional 2025 Modul Client-Side. Membangun visualisasi analitik real-time, canvas chart rendering tanpa library eksternal, dan pengelolaan offline cache.',
                'year' => 2025,
                'level' => CompetitionLevel::Nasional,
                'module_type' => CompetitionModuleType::Client,
                'file_path' => 'question-bank/lks-nasional-2025-client-e-commerce.pdf',
                'file_name' => 'LKS-Nasional-2025-Client-Side-Analytics.pdf',
                'file_size' => 8149910,
                'uploaded_by' => $admin?->id,
                'download_count' => 88,
            ],
            [
                'title' => 'LKS Kabupaten Cilacap 2024 - Modul Client-Side Ticket Booking System',
                'slug' => 'lks-kabupaten-cilacap-2024-client-booking',
                'description' => 'Soal seleksi LKS Kabupaten Cilacap 2024. Aplikasi frontend reservasi tiket bus pariwisata dengan pemilihan kursi interaktif, validasi formulir pemesanan, dan ringkasan pembayaran.',
                'year' => 2024,
                'level' => CompetitionLevel::Kabupaten,
                'module_type' => CompetitionModuleType::Client,
                'file_path' => 'question-bank/lks-nasional-2024-server-pintarmenabung.pdf',
                'file_name' => 'LKS-Kab-Cilacap-2024-Client-Ticket-Booking.pdf',
                'file_size' => 8149910,
                'uploaded_by' => $mentor1?->id,
                'download_count' => 31,
            ],
            [
                'title' => 'LKS Provinsi Jawa Tengah 2026 - Modul Server-Side Realtime Telemedicine API',
                'slug' => 'lks-provinsi-jateng-2026-server-telemedicine',
                'description' => 'Soal seleksi LKS Provinsi Jawa Tengah 2026. Backend API konsultasi dokter daring dengan jadwal konsultasi, rekam medis terenkripsi, dan integrasi payment gateway sandbox.',
                'year' => 2026,
                'level' => CompetitionLevel::Provinsi,
                'module_type' => CompetitionModuleType::Server,
                'file_path' => 'question-bank/lks-provinsi-2025-client-map.pdf',
                'file_name' => 'LKS-Jateng-2026-Server-Telemedicine.pdf',
                'file_size' => 8149910,
                'uploaded_by' => $admin?->id,
                'download_count' => 64,
            ],
        ];

        foreach ($papers as $data) {
            QuestionBankPaper::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
