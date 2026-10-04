<?php

namespace App\Services\Roadmap;

use App\Models\Exercise;
use App\Models\RoadmapCheckpoint;
use App\Models\RoadmapPage;
use App\Models\RoadmapSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class RoadmapContentImporter
{
    protected CommonMarkConverter $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Import roadmap content from markdown file.
     *
     * @return array{pages: int, sections: int, checkpoints: int, exercises: int}
     */
    public function import(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new \RuntimeException("Roadmap file not found at: {$filePath}");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Unable to read roadmap file at: {$filePath}");
        }

        return DB::transaction(function () use ($content) {
            $stats = [
                'pages' => 0,
                'sections' => 0,
                'checkpoints' => 0,
                'exercises' => 0,
            ];

            // 1. Parse level estimated times from Level table
            $estimatedTimes = $this->parseEstimatedTimes($content);

            // 2. Parse reference pages
            $this->importReferencePages($content, $stats);

            // 3. Parse Level 0 to Level 8
            $this->importLevels($content, $estimatedTimes, $stats);

            // 4. Import / Seed default interactive exercises
            $stats['exercises'] = $this->importDefaultExercises();

            return $stats;
        });
    }

    /**
     * Extract estimated times from the overview table.
     *
     * @return array<int, string>
     */
    protected function parseEstimatedTimes(string $content): array
    {
        $times = [];
        if (preg_match('/\|\s*Level\s*\|\s*Topik\s*\|\s*Perkiraan waktu\s*\|(.*?)\n\s*Total/s', $content, $match)) {
            $lines = explode("\n", trim($match[1]));
            foreach ($lines as $line) {
                if (preg_match('/\|\s*(\d+)\s*\|\s*([^|]+)\|\s*([^|]+)\|/', $line, $col)) {
                    $times[(int) $col[1]] = trim($col[3]);
                }
            }
        }

        return $times;
    }

    /**
     * Import reference pages (Peta Besar, Alur Mengerjakan, Troubleshooting, Checklist, Referensi).
     */
    protected function importReferencePages(string $content, array &$stats): void
    {
        $references = [
            [
                'slug' => 'peta-besar',
                'kind' => 'reference',
                'position' => 10,
                'title' => 'Peta besar: apa yang akan kamu bangun',
                'pattern' => '/## Peta besar: apa yang akan kamu bangun(.*?)(?=## Level 0:)/s',
            ],
            [
                'slug' => 'alur-pengerjaan',
                'kind' => 'guide',
                'position' => 11,
                'title' => 'Alur mengerjakan modul saat latihan dan lomba',
                'pattern' => '/## Alur mengerjakan modul saat latihan dan lomba(.*?)(?=## Kalau ada yang tidak jalan)/s',
            ],
            [
                'slug' => 'troubleshooting',
                'kind' => 'guide',
                'position' => 12,
                'title' => 'Kalau ada yang tidak jalan',
                'pattern' => '/## Kalau ada yang tidak jalan(.*?)(?=## Checklist kesiapan)/s',
            ],
            [
                'slug' => 'checklist',
                'kind' => 'guide',
                'position' => 13,
                'title' => 'Checklist kesiapan',
                'pattern' => '/## Checklist kesiapan(.*?)(?=## Referensi)/s',
            ],
            [
                'slug' => 'referensi',
                'kind' => 'reference',
                'position' => 14,
                'title' => 'Referensi',
                'pattern' => '/## Referensi(.*)$/s',
            ],
        ];

        foreach ($references as $ref) {
            if (preg_match($ref['pattern'], $content, $matches)) {
                $body = trim($matches[1]);
                $bodyClean = trim(preg_replace('/^---\s*$/m', '', $body));
                $html = (string) $this->converter->convert($bodyClean);
                $hash = hash('sha256', $bodyClean);

                $page = RoadmapPage::updateOrCreate(
                    ['slug' => $ref['slug']],
                    [
                        'kind' => $ref['kind'],
                        'position' => $ref['position'],
                        'title' => $ref['title'],
                        'goal' => null,
                        'estimated_time' => null,
                        'content_hash' => $hash,
                    ]
                );
                $stats['pages']++;

                RoadmapSection::updateOrCreate(
                    [
                        'roadmap_page_id' => $page->id,
                        'slug' => 'overview',
                    ],
                    [
                        'kind' => 'overview',
                        'title' => $ref['title'],
                        'position' => 1,
                        'body_md' => $bodyClean,
                        'body_html' => $html,
                        'hash' => $hash,
                    ]
                );
                $stats['sections']++;
            }
        }
    }

    /**
     * Import Level 0 through Level 8.
     *
     * @param  array<int, string>  $estimatedTimes
     */
    protected function importLevels(string $content, array $estimatedTimes, array &$stats): void
    {
        for ($lvl = 0; $lvl <= 8; $lvl++) {
            $nextMarker = $lvl < 8
                ? '(?=## Level '.($lvl + 1).':)'
                : '(?=## Alur mengerjakan modul)';

            $pattern = "/## Level {$lvl}:\s*(.*?)\n(.*?){$nextMarker}/s";

            if (! preg_match($pattern, $content, $matches)) {
                continue;
            }

            $levelTitle = trim($matches[1]);
            $levelBody = trim($matches[2]);
            $levelBody = trim(preg_replace('/^---\s*$/m', '', $levelBody));
            $levelSlug = "level-{$lvl}";
            $levelHash = hash('sha256', $levelBody);

            // Extract goal if present
            $goal = null;
            if (preg_match('/\*\*Tujuan:\*\*\s*(.+?)(?=\n\n|\n\*\*)/s', $levelBody, $goalMatch)) {
                $goal = trim($goalMatch[1]);
            }

            $page = RoadmapPage::updateOrCreate(
                ['slug' => $levelSlug],
                [
                    'kind' => 'level',
                    'position' => $lvl,
                    'title' => "Level {$lvl}: {$levelTitle}",
                    'goal' => $goal,
                    'estimated_time' => $estimatedTimes[$lvl] ?? null,
                    'content_hash' => $levelHash,
                ]
            );
            $stats['pages']++;

            $sectionPos = 1;

            // 1. Konsep Kunci section
            if (preg_match('/\*\*Konsep kunci\*\*(.*?)(?=\n\*\*|###|\z)/s', $levelBody, $kunciMatch)) {
                $kunciMd = trim($kunciMatch[1]);
                $hash = hash('sha256', $kunciMd);
                RoadmapSection::updateOrCreate(
                    [
                        'roadmap_page_id' => $page->id,
                        'slug' => 'konsep-kunci',
                    ],
                    [
                        'kind' => 'materi',
                        'title' => 'Konsep Kunci',
                        'position' => $sectionPos++,
                        'body_md' => $kunciMd,
                        'body_html' => (string) $this->converter->convert($kunciMd),
                        'hash' => $hash,
                    ]
                );
                $stats['sections']++;
            }

            // Level 7 has named subsections like ### 7.1 Graph, ### 7.2 BFS, etc.
            if (preg_match_all('/###\s*(7\.\d+\s+.*?)\n(.*?)(?=###|\*\*Latihan|\z)/s', $levelBody, $subMatches, PREG_SET_ORDER)) {
                foreach ($subMatches as $idx => $sub) {
                    $subTitle = trim($sub[1]);
                    $subMd = trim($sub[2]);
                    $subSlug = 'materi-'.Str::slug($subTitle);
                    $subHash = hash('sha256', $subMd);

                    RoadmapSection::updateOrCreate(
                        [
                            'roadmap_page_id' => $page->id,
                            'slug' => $subSlug,
                        ],
                        [
                            'kind' => 'materi',
                            'title' => $subTitle,
                            'position' => $sectionPos++,
                            'body_md' => $subMd,
                            'body_html' => (string) $this->converter->convert($subMd),
                            'hash' => $subHash,
                        ]
                    );
                    $stats['sections']++;
                }
            }

            // Geometri yang dipakai (Level 5)
            if (preg_match('/\*\*Geometri yang dipakai\*\*(.*?)(?=\n\*\*Latihan)/s', $levelBody, $geoMatch)) {
                $geoMd = trim($geoMatch[1]);
                $geoHash = hash('sha256', $geoMd);
                RoadmapSection::updateOrCreate(
                    [
                        'roadmap_page_id' => $page->id,
                        'slug' => 'geometri',
                    ],
                    [
                        'kind' => 'dipakai_di',
                        'title' => 'Geometri yang Dipakai',
                        'position' => $sectionPos++,
                        'body_md' => $geoMd,
                        'body_html' => (string) $this->converter->convert($geoMd),
                        'hash' => $geoHash,
                    ]
                );
                $stats['sections']++;
            }

            // Latihan section
            if (preg_match('/\*\*Latihan.*?\*\*(.*?)(?=\n\*\*Cek hasilmu|\n\*\*Soal tantangan|\n\*\*Cek pemahaman|\z)/s', $levelBody, $latMatch)) {
                $latMd = trim($latMatch[1]);
                $latHash = hash('sha256', $latMd);
                RoadmapSection::updateOrCreate(
                    [
                        'roadmap_page_id' => $page->id,
                        'slug' => 'latihan',
                    ],
                    [
                        'kind' => 'latihan',
                        'title' => 'Latihan Mandiri',
                        'position' => $sectionPos++,
                        'body_md' => $latMd,
                        'body_html' => (string) $this->converter->convert($latMd),
                        'hash' => $latHash,
                    ]
                );
                $stats['sections']++;
            }

            // Soal Tantangan (Level 7)
            if (preg_match('/\*\*Soal tantangan:\*\*(.*?)(?=\n\*\*Cek pemahaman|\z)/s', $levelBody, $tantangMatch)) {
                $tantangMd = trim($tantangMatch[1]);
                $tHash = hash('sha256', $tantangMd);
                RoadmapSection::updateOrCreate(
                    [
                        'roadmap_page_id' => $page->id,
                        'slug' => 'soal-tantangan',
                    ],
                    [
                        'kind' => 'latihan',
                        'title' => 'Soal Tantangan',
                        'position' => $sectionPos++,
                        'body_md' => $tantangMd,
                        'body_html' => (string) $this->converter->convert($tantangMd),
                        'hash' => $tHash,
                    ]
                );
                $stats['sections']++;
            }

            // Cek hasilmu
            if (preg_match('/\*\*Cek hasilmu.*?\*\*(.*?)(?=\n\*\*Cek pemahaman|\z)/s', $levelBody, $cekHasilMatch)) {
                $cekMd = trim($cekHasilMatch[1]);
                $cHash = hash('sha256', $cekMd);
                RoadmapSection::updateOrCreate(
                    [
                        'roadmap_page_id' => $page->id,
                        'slug' => 'cek-hasil',
                    ],
                    [
                        'kind' => 'cek_hasil',
                        'title' => 'Cek Hasilmu',
                        'position' => $sectionPos++,
                        'body_md' => $cekMd,
                        'body_html' => (string) $this->converter->convert($cekMd),
                        'hash' => $cHash,
                    ]
                );
                $stats['sections']++;
            }

            // Dipakai di
            if (preg_match('/\*\*Dipakai di:\*\*(.*?)(?=\n---|\z)/s', $levelBody, $pakaiMatch)) {
                $pakaiMd = trim($pakaiMatch[1]);
                $pHash = hash('sha256', $pakaiMd);
                RoadmapSection::updateOrCreate(
                    [
                        'roadmap_page_id' => $page->id,
                        'slug' => 'dipakai-di',
                    ],
                    [
                        'kind' => 'dipakai_di',
                        'title' => 'Dipakai di Modul LKS',
                        'position' => $sectionPos++,
                        'body_md' => $pakaiMd,
                        'body_html' => (string) $this->converter->convert($pakaiMd),
                        'hash' => $pHash,
                    ]
                );
                $stats['sections']++;
            }

            // Checkpoints: **Cek pemahaman**
            if (preg_match('/\*\*Cek pemahaman\*\*(.*?)(?=\n\*\*Dipakai di|\n---|\z)/s', $levelBody, $cpBlockMatch)) {
                $cpLines = explode("\n", trim($cpBlockMatch[1]));
                $cpIndex = 1;
                foreach ($cpLines as $line) {
                    $cleanLine = trim($line);
                    if (str_starts_with($cleanLine, '- ')) {
                        $prompt = trim(substr($cleanLine, 2));
                        $cpSlug = "{$levelSlug}-cp-{$cpIndex}";

                        $quizData = RoadmapQuizCatalog::getQuizForPrompt($prompt);

                        RoadmapCheckpoint::updateOrCreate(
                            [
                                'roadmap_page_id' => $page->id,
                                'slug' => $cpSlug,
                            ],
                            [
                                'position' => $cpIndex,
                                'prompt' => $prompt,
                                'options' => $quizData['options'],
                                'correct_answer' => $quizData['correct_answer'],
                                'explanation' => $quizData['explanation'],
                            ]
                        );
                        $cpIndex++;
                        $stats['checkpoints']++;
                    }
                }
            }
        }
    }

    /**
     * Import standard interactive exercises.
     */
    protected function importDefaultExercises(): int
    {
        $exercises = [
            // Level 0 Exercises
            [
                'slug' => 'l0-parse-protocol',
                'level_slug' => 'level-0',
                'title' => 'Cek Protokol URL (file:// vs http://)',
                'type' => 'function',
                'instructions' => "Tulis fungsi `parseProtocol(url)` yang menerima string URL (contoh: `'http://localhost:3000'` atau `'file:///C:/proyek/index.html'`) dan mengembalikan string nama protokolnya (`'http'`, `'https'`, atau `'file'`).",
                'starter_code' => "function parseProtocol(url) {\n    if (url.startsWith('https://')) return 'https';\n    if (url.startsWith('http://')) return 'http';\n    if (url.startsWith('file://')) return 'file';\n    return 'unknown';\n}",
                'test_cases' => [
                    ['input' => ['http://localhost:8080'], 'expected' => 'http'],
                    ['input' => ['https://lks.smktelkom-pwt.sch.id'], 'expected' => 'https'],
                    ['input' => ['file:///home/user/index.html'], 'expected' => 'file'],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l0-is-local-env',
                'level_slug' => 'level-0',
                'title' => 'Deteksi Lingkungan Server Lokal',
                'type' => 'function',
                'instructions' => "Tulis fungsi `isLocalEnv(hostname)` yang mengembalikan `true` jika hostname adalah `'localhost'`, `'127.0.0.1'`, atau `'::1'`, dan `false` jika selain itu.",
                'starter_code' => "function isLocalEnv(hostname) {\n    return ['localhost', '127.0.0.1', '::1'].includes(hostname);\n}",
                'test_cases' => [
                    ['input' => ['localhost'], 'expected' => true],
                    ['input' => ['127.0.0.1'], 'expected' => true],
                    ['input' => ['smktelkom-pwt.sch.id'], 'expected' => false],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 1 Exercises
            [
                'slug' => 'l1-hitung-total-harga',
                'level_slug' => 'level-1',
                'title' => 'Kalkulasi Total Diskon Formulir',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `hitungTotalHarga(harga, diskonPersen)` yang menerima harga asli dan persentase diskon (0-100), lalu mengembalikan harga akhir setelah dipotong diskon.',
                'starter_code' => "function hitungTotalHarga(harga, diskonPersen) {\n    return harga - (harga * (diskonPersen / 100));\n}",
                'test_cases' => [
                    ['input' => [100000, 10], 'expected' => 90000],
                    ['input' => [50000, 50], 'expected' => 25000],
                    ['input' => [20000, 0], 'expected' => 20000],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l1-validasi-email',
                'level_slug' => 'level-1',
                'title' => 'Validasi Format Alamat Email',
                'type' => 'function',
                'instructions' => "Tulis fungsi `validasiEmail(email)` yang mengembalikan `true` jika email memiliki tanda '@' dan titik '.' setelahnya, serta bukan di posisi awal/akhir.",
                'starter_code' => "function validasiEmail(email) {\n    const at = email.indexOf('@');\n    const dot = email.lastIndexOf('.');\n    return at > 0 && dot > at + 1 && dot < email.length - 1;\n}",
                'test_cases' => [
                    ['input' => ['siswa@telkom.sch.id'], 'expected' => true],
                    ['input' => ['invalid-email'], 'expected' => false],
                    ['input' => ['user@domain'], 'expected' => false],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 2 Exercises
            [
                'slug' => 'l2-hitung-kolom-grid',
                'level_slug' => 'level-2',
                'title' => 'Penentuan Jumlah Kolom Grid Responsif',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `hitungKolomGrid(lebarLayar)` yang mengembalikan angka jumlah kolom kartu: lebar < 640 mengembalikan 1, lebar antara 640 s.d. 1023 mengembalikan 2, dan lebar >= 1024 mengembalikan 4.',
                'starter_code' => "function hitungKolomGrid(lebarLayar) {\n    if (lebarLayar < 640) return 1;\n    if (lebarLayar < 1024) return 2;\n    return 4;\n}",
                'test_cases' => [
                    ['input' => [400], 'expected' => 1],
                    ['input' => [768], 'expected' => 2],
                    ['input' => [1200], 'expected' => 4],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l2-clamp-skala',
                'level_slug' => 'level-2',
                'title' => 'Batasi Rentang Zoom (Clamp)',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `clampSkala(skala, min, max)` yang memastikan nilai `skala` tidak lebih kecil dari `min` dan tidak lebih besar dari `max`.',
                'starter_code' => "function clampSkala(skala, min, max) {\n    return Math.min(Math.max(skala, min), max);\n}",
                'test_cases' => [
                    ['input' => [0.4, 0.5, 3.0], 'expected' => 0.5],
                    ['input' => [3.5, 0.5, 3.0], 'expected' => 3.0],
                    ['input' => [1.8, 0.5, 3.0], 'expected' => 1.8],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 3 Exercises
            [
                'slug' => 'l3-format-durasi',
                'level_slug' => 'level-3',
                'title' => 'Format Durasi Jam ke Jam & Menit',
                'type' => 'function',
                'instructions' => "Tulis fungsi `formatDurasi(jamDesimal)` yang menerima angka pecahan jam (misalnya `3.75`) dan mengembalikan string format `\"Xj Ym\"` (misalnya `\"3j 45m\"`).\n\nContoh:\n- `formatDurasi(3.75)` -> `\"3j 45m\"`\n- `formatDurasi(1.5)` -> `\"1j 30m\"`\n- `formatDurasi(2.0)` -> `\"2j 0m\"`",
                'starter_code' => "function formatDurasi(jamDesimal) {\n    const jam = Math.floor(jamDesimal);\n    const menit = Math.round((jamDesimal - jam) * 60);\n    return `\${jam}j \${menit}m`;\n}",
                'test_cases' => [
                    ['input' => [3.75], 'expected' => '3j 45m'],
                    ['input' => [1.5], 'expected' => '1j 30m'],
                    ['input' => [0.25], 'expected' => '0j 15m'],
                    ['input' => [5.0], 'expected' => '5j 0m'],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l3-format-rupiah',
                'level_slug' => 'level-3',
                'title' => 'Format Nominal Rupiah',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `formatRupiah(nominal)` yang mengubah angka seperti `30000` menjadi format `"Rp30.000"` dengan pemisah ribuan titik.',
                'starter_code' => "function formatRupiah(nominal) {\n    return 'Rp' + Number(nominal).toLocaleString('id-ID');\n}",
                'test_cases' => [
                    ['input' => [30000], 'expected' => 'Rp30.000'],
                    ['input' => [1500000], 'expected' => 'Rp1.500.000'],
                    ['input' => [500], 'expected' => 'Rp500'],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 4 Exercises
            [
                'slug' => 'l4-buat-pin',
                'level_slug' => 'level-4',
                'title' => 'Konstruksi Objek Data Pin Marker',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `buatPin(id, nama, lat, lng)` yang mengembalikan objek dengan format: `{ id, nama, lat: Number(lat), lng: Number(lng), active: false }`.',
                'starter_code' => "function buatPin(id, nama, lat, lng) {\n    return {\n        id: id,\n        nama: nama,\n        lat: Number(lat),\n        lng: Number(lng),\n        active: false\n    };\n}",
                'test_cases' => [
                    ['input' => ['pin-1', 'Stasiun Gambir', '-6.176', '106.830'], 'expected' => ['id' => 'pin-1', 'nama' => 'Stasiun Gambir', 'lat' => -6.176, 'lng' => 106.83, 'active' => false]],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l4-filter-kategori',
                'level_slug' => 'level-4',
                'title' => 'Filter Pin Lokasi Berdasarkan Kategori',
                'type' => 'function',
                'instructions' => "Tulis fungsi `filterPin(pins, kategori)` yang memfilter array objek pin berdasarkan properti `kategori`. Jika kategori adalah `'semua'`, kembalikan seluruh pin.",
                'starter_code' => "function filterPin(pins, kategori) {\n    if (kategori === 'semua') return pins;\n    return pins.filter(p => p.kategori === kategori);\n}",
                'test_cases' => [
                    ['input' => [[['nama' => 'A', 'kategori' => 'wisata'], ['nama' => 'B', 'kategori' => 'hotel']], 'wisata'], 'expected' => [['nama' => 'A', 'kategori' => 'wisata']]],
                    ['input' => [[['nama' => 'A', 'kategori' => 'wisata']], 'semua'], 'expected' => [['nama' => 'A', 'kategori' => 'wisata']]],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 5 Exercises
            [
                'slug' => 'l5-hitung-jarak',
                'level_slug' => 'level-5',
                'title' => 'Hitung Jarak Euclidean 2 Titik',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `hitungJarak(x1, y1, x2, y2)` yang mengembalikan jarak antara dua titik koordinat dibulatkan hingga 2 tempat desimal.',
                'starter_code' => "function hitungJarak(x1, y1, x2, y2) {\n    const d = Math.hypot(x2 - x1, y2 - y1);\n    return Math.round(d * 100) / 100;\n}",
                'test_cases' => [
                    ['input' => [100, 100, 300, 200], 'expected' => 223.61],
                    ['input' => [0, 0, 3, 4], 'expected' => 5],
                    ['input' => [10, 20, 10, 50], 'expected' => 30],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l5-titik-tengah',
                'level_slug' => 'level-5',
                'title' => 'Hitung Titik Tengah Garis',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `hitungTitikTengah(x1, y1, x2, y2)` yang mengembalikan array `[midX, midY]`.',
                'starter_code' => "function hitungTitikTengah(x1, y1, x2, y2) {\n    return [(x1 + x2) / 2, (y1 + y2) / 2];\n}",
                'test_cases' => [
                    ['input' => [100, 100, 300, 200], 'expected' => [200, 150]],
                    ['input' => [0, 0, 10, 20], 'expected' => [5, 10]],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 6 Exercises
            [
                'slug' => 'l6-layar-ke-peta',
                'level_slug' => 'level-6',
                'title' => 'Konversi Koordinat Layar ke Koordinat Peta',
                'type' => 'function',
                'instructions' => 'Peta diberi transform `translate(tx, ty) scale(s)`. Tulis fungsi `layarKePeta(sx, sy, tx, ty, s)` yang mengembalikan objek `{ mx, my }` di sistem koordinat peta.',
                'starter_code' => "function layarKePeta(sx, sy, tx, ty, s) {\n    return {\n        mx: (sx - tx) / s,\n        my: (sy - ty) / s\n    };\n}",
                'test_cases' => [
                    ['input' => [250, 230, 50, 30, 2], 'expected' => ['mx' => 100, 'my' => 100]],
                    ['input' => [100, 200, 0, 0, 1], 'expected' => ['mx' => 100, 'my' => 200]],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l6-zoom-tx',
                'level_slug' => 'level-6',
                'title' => 'Hitung TX Baru Saat Zoom ke Kursor',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `zoomTx(sx, tx, sLama, sBaru)` yang menghitung posisi `tx2` baru agar titik peta di bawah kursor `sx` tetap di tempat yang sama.',
                'starter_code' => "function zoomTx(sx, tx, sLama, sBaru) {\n    const mx = (sx - tx) / sLama;\n    const txBaru = sx - mx * sBaru;\n    return Math.round(txBaru * 100) / 100;\n}",
                'test_cases' => [
                    ['input' => [640, -120, 1.7, 2.4], 'expected' => -432.94],
                    ['input' => [0, 0, 1, 2], 'expected' => 0],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 7 Exercises
            [
                'slug' => 'l7-bfs-shortest',
                'level_slug' => 'level-7',
                'title' => 'BFS Jalur Terpendek',
                'type' => 'function',
                'instructions' => "Tulis fungsi `bfs(graph, awal, tujuan)` yang mengembalikan array simpul jalur terpendek (jumlah sisi paling sedikit). Contoh graph: `{ A: ['B', 'C'], B: ['A', 'C', 'D'], C: ['A', 'B', 'D'], D: ['B', 'C'] }`. Dari `A` ke `D` menghasilkan `['A', 'B', 'D']`.",
                'starter_code' => "function bfs(graph, awal, tujuan) {\n    if (awal === tujuan) return [awal];\n    const antrean = [awal];\n    const parent = { [awal]: null };\n    \n    while (antrean.length > 0) {\n        const current = antrean.shift();\n        if (current === tujuan) break;\n        \n        for (const tetangga of (graph[current] || [])) {\n            if (!(tetangga in parent)) {\n                parent[tetangga] = current;\n                antrean.push(tetangga);\n            }\n        }\n    }\n    \n    if (!(tujuan in parent)) return [];\n    const jalur = [];\n    let curr = tujuan;\n    while (curr !== null) {\n        jalur.unshift(curr);\n        curr = parent[curr];\n    }\n    return jalur;\n}",
                'test_cases' => [
                    [
                        'input' => [
                            [
                                'A' => ['B', 'C'],
                                'B' => ['A', 'C', 'D'],
                                'C' => ['A', 'B', 'D'],
                                'D' => ['B', 'C'],
                            ],
                            'A',
                            'D',
                        ],
                        'expected' => ['A', 'B', 'D'],
                    ],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l7-dfs-all-paths',
                'level_slug' => 'level-7',
                'title' => 'DFS Semua Jalur dengan Backtracking',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `dfsSemuaJalur(graph, awal, tujuan)` yang mengembalikan array dari seluruh jalur yang mungkin tanpa siklus.',
                'starter_code' => "function dfsSemuaJalur(graph, awal, tujuan) {\n    const hasil = [];\n    const visited = new Set([awal]);\n    const path = [awal];\n    \n    function cari(simpul) {\n        if (simpul === tujuan) {\n            hasil.push([...path]);\n            return;\n        }\n        for (const tetangga of (graph[simpul] || [])) {\n            if (!visited.has(tetangga)) {\n                visited.add(tetangga);\n                path.push(tetangga);\n                cari(tetangga);\n                path.pop();\n                visited.delete(tetangga);\n            }\n        }\n    }\n    cari(awal);\n    return hasil;\n}",
                'test_cases' => [
                    [
                        'input' => [
                            [
                                'A' => ['B', 'C'],
                                'B' => ['A', 'C', 'D'],
                                'C' => ['A', 'B', 'D'],
                                'D' => ['B', 'C'],
                            ],
                            'A',
                            'D',
                        ],
                        'expected' => [
                            ['A', 'B', 'C', 'D'],
                            ['A', 'B', 'D'],
                            ['A', 'C', 'B', 'D'],
                            ['A', 'C', 'D'],
                        ],
                    ],
                ],
                'is_required' => true,
                'position' => 2,
            ],

            // Level 8 Exercises
            [
                'slug' => 'l8-hitung-total-rute',
                'level_slug' => 'level-8',
                'title' => 'Akumulasi Jarak dan Biaya Rute Multi-Segmen',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `akumulasiRute(segmenList)` yang menerima array segmen `{ jarakKm: number, tarifPerKm: number }` dan mengembalikan objek `{ totalJarak: number, totalBiaya: number }`.',
                'starter_code' => "function akumulasiRute(segmenList) {\n    let totalJarak = 0;\n    let totalBiaya = 0;\n    for (const s of segmenList) {\n        totalJarak += s.jarakKm;\n        totalBiaya += s.jarakKm * s.tarifPerKm;\n    }\n    return { totalJarak, totalBiaya };\n}",
                'test_cases' => [
                    [
                        'input' => [
                            [
                                ['jarakKm' => 10, 'tarifPerKm' => 2000],
                                ['jarakKm' => 5, 'tarifPerKm' => 3000],
                            ],
                        ],
                        'expected' => ['totalJarak' => 15, 'totalBiaya' => 35000],
                    ],
                ],
                'is_required' => true,
                'position' => 1,
            ],
            [
                'slug' => 'l8-kecepatan-rata-rata',
                'level_slug' => 'level-8',
                'title' => 'Hitung Estimasi Kecepatan Tempuh Rata-Rata',
                'type' => 'function',
                'instructions' => 'Tulis fungsi `hitungKecepatan(jarakKm, waktuJam)` yang menghitung `jarakKm / waktuJam` dibulatkan ke 1 tempat desimal. Jika waktuJam <= 0, kembalikan 0.',
                'starter_code' => "function hitungKecepatan(jarakKm, waktuJam) {\n    if (waktuJam <= 0) return 0;\n    return Math.round((jarakKm / waktuJam) * 10) / 10;\n}",
                'test_cases' => [
                    ['input' => [120, 2.5], 'expected' => 48],
                    ['input' => [50, 1.2], 'expected' => 41.7],
                    ['input' => [10, 0], 'expected' => 0],
                ],
                'is_required' => true,
                'position' => 2,
            ],
        ];

        $count = 0;
        foreach ($exercises as $ex) {
            $hash = hash('sha256', $ex['instructions'].$ex['starter_code']);
            Exercise::updateOrCreate(
                ['slug' => $ex['slug']],
                [
                    'level_slug' => $ex['level_slug'],
                    'title' => $ex['title'],
                    'type' => $ex['type'],
                    'instructions' => $ex['instructions'],
                    'starter_code' => $ex['starter_code'],
                    'test_cases' => $ex['test_cases'],
                    'is_required' => $ex['is_required'],
                    'position' => $ex['position'],
                    'hash' => $hash,
                ]
            );
            $count++;
        }

        return $count;
    }
}
