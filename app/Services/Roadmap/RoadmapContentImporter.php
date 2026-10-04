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

                        RoadmapCheckpoint::updateOrCreate(
                            [
                                'roadmap_page_id' => $page->id,
                                'slug' => $cpSlug,
                            ],
                            [
                                'position' => $cpIndex,
                                'prompt' => $prompt,
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
