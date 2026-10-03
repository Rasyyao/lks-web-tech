<?php

namespace Database\Seeders;

use App\Enums\CohortType;
use App\Enums\ModuleStatus;
use App\Enums\ModuleTrack;
use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Enums\SubmissionStatus;
use App\Models\Announcement;
use App\Models\Cohort;
use App\Models\Material;
use App\Models\Module;
use App\Models\ModuleAsset;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionOption;
use App\Models\Topic;
use App\Models\User;
use App\Services\Leaderboard\RecomputeLeaderboard;
use App\Services\Practice\AttemptBuilder;
use App\Services\Practice\AttemptScorer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // 2. Cohorts
        $cohortRpl1 = Cohort::create([
            'name' => 'XII RPL 1',
            'type' => CohortType::ClassGroup,
            'year' => 2026,
        ]);

        $cohortRpl2 = Cohort::create([
            'name' => 'XII RPL 2',
            'type' => CohortType::ClassGroup,
            'year' => 2026,
        ]);

        $cohortSeleksi = Cohort::create([
            'name' => 'Seleksi LKS 2026',
            'type' => CohortType::Selection,
            'year' => 2026,
        ]);

        // 3. Admin & Mentors
        $admin = User::create([
            'name' => 'Administrator LKS',
            'username' => 'admin',
            'email' => 'admin@smktelkom-pwt.sch.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $admin->assignRole($adminRole);

        $mentor1 = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'username' => 'mentor1',
            'email' => 'budi@smktelkom-pwt.sch.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $mentor1->assignRole($mentorRole);

        $mentor2 = User::create([
            'name' => 'Siti Rahayu, M.Kom',
            'username' => 'mentor2',
            'email' => 'siti@smktelkom-pwt.sch.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $mentor2->assignRole($mentorRole);

        // 4. Students per PRD Section 6 examples
        $dewi = User::create([
            'name' => 'Dewi Lestari',
            'username' => '541221001',
            'email' => 'dewi@student.smktelkom-pwt.sch.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $dewi->assignRole($studentRole);
        $dewi->cohorts()->attach([$cohortRpl1->id, $cohortSeleksi->id]);

        $raka = User::create([
            'name' => 'Raka Pratama',
            'username' => '541221002',
            'email' => 'raka@student.smktelkom-pwt.sch.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $raka->assignRole($studentRole);
        $raka->cohorts()->attach([$cohortRpl2->id, $cohortSeleksi->id]);

        $dimas = User::create([
            'name' => 'Dimas Saputra',
            'username' => '541221003',
            'email' => 'dimas@student.smktelkom-pwt.sch.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'must_change_password' => false,
        ]);
        $dimas->assignRole($studentRole);
        $dimas->cohorts()->attach([$cohortRpl1->id]);

        $putri = User::create([
            'name' => 'Putri Ananda',
            'username' => '541221004',
            'email' => 'putri@student.smktelkom-pwt.sch.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'must_change_password' => true, // Demo forced password change
        ]);
        $putri->assignRole($studentRole);
        $putri->cohorts()->attach([$cohortRpl2->id]);

        // 5. Topics
        $topicHtml = Topic::create([
            'name' => 'HTML & Web Semantics',
            'slug' => 'html-semantics',
            'position' => 1,
        ]);

        $topicCss = Topic::create([
            'name' => 'CSS, Flexbox & Grid Layout',
            'slug' => 'css-layout',
            'position' => 2,
        ]);

        $topicJs = Topic::create([
            'name' => 'Modern JavaScript & DOM',
            'slug' => 'javascript-dom',
            'position' => 3,
        ]);

        $topicPhp = Topic::create([
            'name' => 'PHP Backend Architecture',
            'slug' => 'php-backend',
            'position' => 4,
        ]);

        $topicApi = Topic::create([
            'name' => 'REST API, JSON & Database MySQL',
            'slug' => 'rest-api-db',
            'position' => 5,
        ]);

        // 6. Materials
        Material::create([
            'topic_id' => $topicHtml->id,
            'title' => 'Struktur Semantik HTML5 dan Aksesibilitas Web',
            'level' => 1,
            'position' => 1,
            'body_md' => <<<'MD'
Penggunaan tag semantik seperti `<header>`, `<main>`, `<article>`, `<section>`, `<nav>`, dan `<footer>` sangat penting dalam penilaian LKS Web Technology.

### Prinsip Utama
1. **Gunakan elemen semantik** alih-alih `<div>` tanpa makna.
2. Setiap halaman harus memiliki tepat satu tag `<h1>`.
3. Seluruh elemen interaktif harus memiliki label yang dapat dibaca screen reader (`aria-label` atau `<label>`).
4. Atribut `alt` pada tag `<img>` wajib disediakan untuk aksesibilitas visual.
MD,
        ]);

        Material::create([
            'topic_id' => $topicCss->id,
            'title' => 'Mastering CSS Flexbox dan Responsive Grid',
            'level' => 2,
            'position' => 1,
            'body_md' => <<<'MD'
Tata letak modern LKS menuntut tampilan responsif sempurna dari resolusi mobile (375px) hingga desktop (1280px+).

```css
/* Container Flexbox */
.container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

/* Grid Layout 3 Kolom Responsif */
.grid-layout {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}
```
MD,
        ]);

        Material::create([
            'topic_id' => $topicJs->id,
            'title' => 'Manipulasi DOM, Fetch API, dan Event Handling',
            'level' => 3,
            'position' => 1,
            'body_md' => <<<'MD'
Pada LKS Client-Side (Module B), manipulasi DOM secara vanilla JavaScript tanpa library eksternal adalah kemampuan inti yang dinilai.

```javascript
async function loadUserData() {
  try {
    const response = await fetch('/api/v1/students');
    if (!response.ok) throw new Error('Gagal memuat data');
    const data = await response.json();
    renderTable(data);
  } catch (error) {
    showNotification(error.message);
  }
}
```
MD,
        ]);

        Material::create([
            'topic_id' => $topicPhp->id,
            'title' => 'Keamanan Backend: Penanganan SQL Injection, CSRF, dan XSS',
            'level' => 3,
            'position' => 1,
            'body_md' => <<<'MD'
Aspek penilaian keamanan pada Module A mencakup:
- **Prepared Statements** menggunakan PDO atau Eloquent ORM.
- **Validasi input ketat** pada setiap request mutasi.
- **Sanitasi output** untuk mencegah script eksekusi liar (XSS).
- **Token CSRF** pada seluruh request bertipe POST, PUT, dan DELETE.
MD,
        ]);

        // 7. Questions (Bank Soal)
        $questionsData = [
            // HTML
            [
                'topic' => $topicHtml,
                'type' => QuestionType::MultipleChoice,
                'difficulty' => 1,
                'points' => 10,
                'body' => 'Elemen HTML5 manakah yang paling tepat digunakan untuk membungkus konten mandiri seperti postingan blog, artikel berita, atau komentar?',
                'options' => [
                    ['label' => '<article>', 'correct' => true],
                    ['label' => '<section>', 'correct' => false],
                    ['label' => '<aside>', 'correct' => false],
                    ['label' => '<div>', 'correct' => false],
                ],
                'explanation' => '`<article>` digunakan untuk merepresentasikan komposisi konten mandiri yang dapat didistribusikan ulang atau digunakan kembali secara independen.',
            ],
            [
                'topic' => $topicHtml,
                'type' => QuestionType::TrueFalse,
                'difficulty' => 1,
                'points' => 10,
                'body' => 'Tag `<!DOCTYPE html>` bersifat case-sensitive dalam spesifikasi HTML5.',
                'options' => [
                    ['label' => 'Benar', 'correct' => false],
                    ['label' => 'Salah', 'correct' => true],
                ],
                'explanation' => 'Spesifikasi HTML5 menyatakan deklarasi doctype bersifat case-insensitive, sehingga `<!doctype html>` dan `<!DOCTYPE html>` bernilai valid.',
            ],
            [
                'topic' => $topicHtml,
                'type' => QuestionType::ShortAnswer,
                'difficulty' => 1,
                'points' => 10,
                'body' => 'Atribut HTML apa yang digunakan untuk membuka tautan `<a>` di tab peramban baru?',
                'answers' => ['_blank', 'target="_blank"', 'target=_blank'],
                'explanation' => 'Atribut `target="_blank"` memberitahu browser untuk membuka dokumen tertaut dalam konteks penjelajahan baru (tab atau jendela baru).',
            ],

            // CSS
            [
                'topic' => $topicCss,
                'type' => QuestionType::MultipleChoice,
                'difficulty' => 2,
                'points' => 10,
                'body' => 'Dalam CSS Flexbox, properti apa yang mengatur perataan elemen sepanjang sumbu silang (cross-axis)?',
                'options' => [
                    ['label' => 'align-items', 'correct' => true],
                    ['label' => 'justify-content', 'correct' => false],
                    ['label' => 'align-content', 'correct' => false],
                    ['label' => 'flex-direction', 'correct' => false],
                ],
                'explanation' => '`justify-content` mengatur perataan pada sumbu utama (main-axis), sedangkan `align-items` mengatur sumbu silang (cross-axis).',
            ],
            [
                'topic' => $topicCss,
                'type' => QuestionType::MultipleChoice,
                'difficulty' => 2,
                'points' => 10,
                'body' => 'Urutan spesifisitas selector CSS dari yang paling tinggi ke paling rendah adalah...',
                'options' => [
                    ['label' => 'Inline style > ID selector > Class selector > Element selector', 'correct' => true],
                    ['label' => 'ID selector > Inline style > Class selector > Element selector', 'correct' => false],
                    ['label' => 'Class selector > ID selector > Inline style > Element selector', 'correct' => false],
                    ['label' => 'Inline style > Class selector > ID selector > Element selector', 'correct' => false],
                ],
                'explanation' => 'Bobot spesifisitas CSS adalah: Inline style (1,0,0,0) > ID (0,1,0,0) > Class/Pseudo-class/Attribute (0,0,1,0) > Element (0,0,0,1).',
            ],
            [
                'topic' => $topicCss,
                'type' => QuestionType::TrueFalse,
                'difficulty' => 2,
                'points' => 10,
                'body' => 'Properti `box-sizing: border-box` memasukkan nilai padding dan border ke dalam total lebar dan tinggi elemen.',
                'options' => [
                    ['label' => 'Benar', 'correct' => true],
                    ['label' => 'Salah', 'correct' => false],
                ],
                'explanation' => 'Benar. Dengan `border-box`, lebar elemen mencakup konten + padding + border.',
            ],
            [
                'topic' => $topicCss,
                'type' => QuestionType::ShortAnswer,
                'difficulty' => 2,
                'points' => 10,
                'body' => 'Properti CSS apakah yang digunakan untuk mengatur jarak ruang di luar batas tepi (border) sebuah elemen?',
                'answers' => ['margin', 'margin-top', 'margin-bottom', 'margin-left', 'margin-right'],
                'explanation' => '`margin` mengatur jarak luar batas elemen, sedangkan `padding` mengatur jarak di dalam batas elemen.',
            ],

            // JS
            [
                'topic' => $topicJs,
                'type' => QuestionType::MultipleChoice,
                'difficulty' => 3,
                'points' => 15,
                'body' => 'Metode array manakah yang menghasilkan array baru berisi hasil pemanggilan fungsi pada setiap elemen tanpa mengubah array asli?',
                'options' => [
                    ['label' => 'map()', 'correct' => true],
                    ['label' => 'forEach()', 'correct' => false],
                    ['label' => 'filter()', 'correct' => false],
                    ['label' => 'reduce()', 'correct' => false],
                ],
                'explanation' => '`map()` mengembalikan array baru dengan panjang yang sama berdasarkan transformasi elemen, tanpa mengubah array sumber (immutable).',
            ],
            [
                'topic' => $topicJs,
                'type' => QuestionType::TrueFalse,
                'difficulty' => 3,
                'points' => 10,
                'body' => 'Operator `===` di JavaScript membandingkan nilai dan tipe data tanpa melakukan konversi tipe otomatis (type coercion).',
                'options' => [
                    ['label' => 'Benar', 'correct' => true],
                    ['label' => 'Salah', 'correct' => false],
                ],
                'explanation' => 'Benar, `===` adalah strict equality operator yang mengecek kesamaan tipe dan nilai sekaligus.',
            ],
            [
                'topic' => $topicJs,
                'type' => QuestionType::ShortAnswer,
                'difficulty' => 3,
                'points' => 15,
                'body' => 'Metode JavaScript pada objek JSON untuk mengubah string JSON menjadi objek JavaScript adalah...',
                'answers' => ['JSON.parse', 'JSON.parse()', 'parse'],
                'explanation' => '`JSON.parse()` mem-parsing string berformat JSON dan mengonversinya menjadi objek JavaScript.',
            ],

            // PHP
            [
                'topic' => $topicPhp,
                'type' => QuestionType::MultipleChoice,
                'difficulty' => 3,
                'points' => 15,
                'body' => 'Mekanisme terbaik di PHP PDO untuk mencegah kerentanan SQL Injection pada query dinamis adalah...',
                'options' => [
                    ['label' => 'Prepared Statements dengan parameter binding', 'correct' => true],
                    ['label' => 'Menggunakan fungsi addslashes()', 'correct' => false],
                    ['label' => 'Mengenkripsi input dengan MD5', 'correct' => false],
                    ['label' => 'Menggunakan regex preg_replace()', 'correct' => false],
                ],
                'explanation' => 'Prepared statements memisahkan struktur query SQL dari data parameter yang dikirimkan, sehingga data tidak dapat dieksekusi sebagai perintah SQL.',
            ],
            [
                'topic' => $topicPhp,
                'type' => QuestionType::TrueFalse,
                'difficulty' => 2,
                'points' => 10,
                'body' => 'Fungsi `htmlspecialchars()` di PHP digunakan untuk mencegah serangan Cross-Site Scripting (XSS) saat menampilkan data ke HTML.',
                'options' => [
                    ['label' => 'Benar', 'correct' => true],
                    ['label' => 'Salah', 'correct' => false],
                ],
                'explanation' => 'Benar, `htmlspecialchars()` mengonversi karakter khusus seperti `<` dan `>` menjadi entitas HTML `&lt;` dan `&gt;`.',
            ],

            // REST API
            [
                'topic' => $topicApi,
                'type' => QuestionType::MultipleChoice,
                'difficulty' => 3,
                'points' => 15,
                'body' => 'HTTP status code yang paling tepat dikembalikan oleh REST API ketika sebuah entitas baru berhasil dibuat di server adalah...',
                'options' => [
                    ['label' => '201 Created', 'correct' => true],
                    ['label' => '200 OK', 'correct' => false],
                    ['label' => '204 No Content', 'correct' => false],
                    ['label' => '202 Accepted', 'correct' => false],
                ],
                'explanation' => 'Status 201 Created menandakan bahwa request POST berhasil dan menghasilkan resource baru di server.',
            ],
            [
                'topic' => $topicApi,
                'type' => QuestionType::MultipleChoice,
                'difficulty' => 4,
                'points' => 15,
                'body' => 'HTTP Method manakah yang bersifat idempoten (idempotent) menurut standar RFC HTTP?',
                'options' => [
                    ['label' => 'GET, PUT, dan DELETE', 'correct' => true],
                    ['label' => 'POST dan PATCH', 'correct' => false],
                    ['label' => 'POST, PUT, dan DELETE', 'correct' => false],
                    ['label' => 'Hanya GET', 'correct' => false],
                ],
                'explanation' => 'Operasi idempoten adalah operasi yang menghasilkan efek samping server yang sama jika dijalankan sekali maupun berkali-kali. GET, PUT, dan DELETE bersifat idempoten, sedangkan POST tidak.',
            ],
            [
                'topic' => $topicApi,
                'type' => QuestionType::ShortAnswer,
                'difficulty' => 3,
                'points' => 15,
                'body' => 'Header HTTP apakah yang digunakan klien untuk menyatakan format data yang dikirim dalam request body (misal: application/json)?',
                'answers' => ['Content-Type', 'content-type', 'Content-type'],
                'explanation' => 'Header `Content-Type` menunjukkan media type dari isi request body.',
            ],
        ];

        foreach ($questionsData as $qData) {
            $question = Question::create([
                'topic_id' => $qData['topic']->id,
                'type' => $qData['type'],
                'body_md' => $qData['body'],
                'difficulty' => $qData['difficulty'],
                'points' => $qData['points'],
                'explanation_md' => $qData['explanation'],
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $mentor1->id,
                'reviewed_by' => $mentor2->id,
            ]);

            if (isset($qData['options'])) {
                foreach ($qData['options'] as $pos => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label' => $opt['label'],
                        'is_correct' => $opt['correct'],
                        'position' => $pos + 1,
                    ]);
                }
            }

            if (isset($qData['answers'])) {
                foreach ($qData['answers'] as $ansText) {
                    QuestionAnswer::create([
                        'question_id' => $question->id,
                        'text' => $ansText,
                        'normalized' => QuestionAnswer::normalize($ansText),
                    ]);
                }
            }
        }

        // 8. Modules A and B
        $moduleA = Module::create([
            'slug' => 'modul-a-server-side-api',
            'title' => 'Modul A: Server-Side RESTful API Development',
            'track' => ModuleTrack::Server,
            'level' => 3,
            'summary' => 'Membangun layanan REST API dengan otentikasi JWT, validasi input, serta pengelolaan relasi data berbasis PHP & MySQL.',
            'brief_md' => <<<'MD'
# Deskripsi Tugas Modul A

Sebagai backend developer pada seleksi LKS Web Technologies, kamu diminta untuk mengimplementasikan RESTful API untuk sistem peminjaman inventaris sekolah.

## Kebutuhan Fungsional
1. **Otentikasi**:
   - `POST /api/v1/auth/login`: login menggunakan NIS dan password, mengembalikan access token.
   - `POST /api/v1/auth/logout`: membatalkan token yang aktif.
2. **Katalog Barang**:
   - `GET /api/v1/items`: daftar barang dengan pagination, filter kategori, dan pencarian nama.
   - `POST /api/v1/items`: menambah barang baru (khusus peran admin/petugas).
3. **Peminjaman**:
   - `POST /api/v1/loans`: mengajukan peminjaman dengan daftar barang dan tanggal kembali.
   - Stok barang harus berkurang saat peminjaman disetujui dalam transaksi database atomik.
MD,
            'rules_md' => <<<'MD'
## Aturan Teknis
- Seluruh response wajib mengembalikan format JSON standar `{ "status": "success|error", "data": ... }`.
- Gunakan HTTP Status Codes yang sesuai: 200, 201, 400, 401, 403, 404, 422.
- Semua query mutasi wajib diamankan dari SQL Injection.
- Buat file `.zip` berisi kode sumber proyek dan dump database `database.sql`.
MD,
            'duration_minutes' => 180,
            'opens_at' => now()->subDays(2),
            'closes_at' => now()->addDays(20),
            'status' => ModuleStatus::Published,
            'max_attempts_per_day' => 5,
            'version' => 1,
        ]);
        $moduleA->cohorts()->attach([$cohortRpl1->id, $cohortRpl2->id, $cohortSeleksi->id]);

        $moduleB = Module::create([
            'slug' => 'modul-b-client-side-app',
            'title' => 'Modul B: Client-Side Interactive Application',
            'track' => ModuleTrack::Client,
            'level' => 3,
            'summary' => 'Mengembangkan antarmuka web interaktif berbasis HTML, CSS Vanilla, dan JavaScript murni dengan konsumsi REST API.',
            'brief_md' => <<<'MD'
# Deskripsi Tugas Modul B

Kembangkan antarmuka pemantauan seleksi siswa secara langsung (live board) menggunakan Vanilla JavaScript.

## Kebutuhan Tampilan & Interaksi
1. Tampilan adaptif sempurna pada layar mobile (375px) dan desktop (1280px).
2. Membaca data leaderboard dari REST API dan menampilkan daftar siswa dengan filter kelas.
3. Fitur pencarian instan (real-time search) tanpa me-reload peramban.
4. Modal dialog detail performa siswa saat baris tabel diklik.
MD,
            'rules_md' => <<<'MD'
## Aturan Teknis
- Dilarang menggunakan framework CSS seperti Tailwind/Bootstrap (gunakan CSS murni).
- Dilarang menggunakan library JS eksternal (jQuery, React, Vue).
- Kompres seluruh isi folder proyek (`index.html`, folder `css/`, folder `js/`) menjadi satu file `.zip`.
MD,
            'duration_minutes' => 180,
            'opens_at' => now()->subDays(1),
            'closes_at' => now()->addDays(25),
            'status' => ModuleStatus::Published,
            'max_attempts_per_day' => 5,
            'version' => 1,
        ]);
        $moduleB->cohorts()->attach([$cohortRpl1->id, $cohortRpl2->id, $cohortSeleksi->id]);

        // Module Assets
        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'starter-pack-backend-api.zip',
            'path' => 'assets/starter-pack-backend-api.zip',
            'size' => 1024 * 350, // 350 KB
        ]);

        ModuleAsset::create([
            'module_id' => $moduleB->id,
            'label' => 'design-mockups-assets.zip',
            'path' => 'assets/design-mockups-assets.zip',
            'size' => 1024 * 850, // 850 KB
        ]);

        // 9. Announcements
        Announcement::create([
            'title' => 'Selamat Datang di Platform Seleksi LKS Web Technologies 2026',
            'body_md' => <<<'MD'
Seleksi calon delegasi SMK Telkom Purwokerto untuk bidang lomba **Web Technologies** resmi dibuka.

Silakan pelajari modul latihan, selesaikan bank soal latihan harian, dan kirimkan tugas modul sebelum batas waktu yang ditentukan. Pemeringkatan pada papan peringkat diperbarui otomatis berdasarkan akumulasi poin soal dan nilai proyek mentor.
MD,
            'published_at' => now()->subDays(1),
        ]);

        // 10. Simulate some practice attempts for student Dewi & Raka
        $attemptBuilder = new AttemptBuilder();
        $attemptScorer = new AttemptScorer();
        $recomputeJob = new RecomputeLeaderboard();

        // Dewi finishes an attempt
        $dewiAttempt = $attemptBuilder->build($dewi, 5);
        foreach ($dewiAttempt->items as $item) {
            $type = QuestionType::from($item->snapshot['type']);
            if ($type === QuestionType::MultipleChoice || $type === QuestionType::TrueFalse) {
                $correctOpt = collect($item->snapshot['options'])->firstWhere('is_correct', true);
                $item->update(['answer' => ['option_id' => $correctOpt['id']]]);
            } elseif ($type === QuestionType::ShortAnswer) {
                $acc = $item->snapshot['accepted_answers'][0]['text'] ?? 'margin';
                $item->update(['answer' => ['text' => $acc]]);
            }
        }
        $attemptScorer->score($dewiAttempt);

        // Raka finishes an attempt
        $rakaAttempt = $attemptBuilder->build($raka, 5);
        foreach ($rakaAttempt->items as $idx => $item) {
            $type = QuestionType::from($item->snapshot['type']);
            // Raka gets some correct, one wrong
            if ($idx === 0) {
                // wrong answer
                $item->update(['answer' => ['option_id' => 9999]]);
            } elseif ($type === QuestionType::MultipleChoice || $type === QuestionType::TrueFalse) {
                $correctOpt = collect($item->snapshot['options'])->firstWhere('is_correct', true);
                $item->update(['answer' => ['option_id' => $correctOpt['id']]]);
            } else {
                $acc = $item->snapshot['accepted_answers'][0]['text'] ?? 'margin';
                $item->update(['answer' => ['text' => $acc]]);
            }
        }
        $attemptScorer->score($rakaAttempt);

        // Add scored submissions for Dewi and Raka to reflect sample leaderboard in DESIGN_RULES
        $dewi->submissions()->create([
            'module_id' => $moduleA->id,
            'attempt_no' => 1,
            'path' => 'submissions/modul-a/dewi.zip',
            'original_name' => 'dewi-modul-a.zip',
            'size' => 1024 * 512,
            'sha256' => hash('sha256', 'dewi-modul-a'),
            'status' => SubmissionStatus::Graded,
            'manual_score' => 95,
            'feedback_md' => 'Arsitektur controller sangat rapi, autentikasi bekerja dengan baik. Struktur response konsisten.',
            'reviewed_by' => $mentor1->id,
            'reviewed_at' => now(),
        ]);

        $raka->submissions()->create([
            'module_id' => $moduleA->id,
            'attempt_no' => 1,
            'path' => 'submissions/modul-a/raka.zip',
            'original_name' => 'raka-modul-a.zip',
            'size' => 1024 * 480,
            'sha256' => hash('sha256', 'raka-modul-a'),
            'status' => SubmissionStatus::Graded,
            'manual_score' => 90,
            'feedback_md' => 'Implementasi API bagus. Validasi input sudah lengkap.',
            'reviewed_by' => $mentor1->id,
            'reviewed_at' => now(),
        ]);

        $dimas->submissions()->create([
            'module_id' => $moduleA->id,
            'attempt_no' => 1,
            'path' => 'submissions/modul-a/dimas.zip',
            'original_name' => 'dimas-modul-a.zip',
            'size' => 1024 * 610,
            'sha256' => hash('sha256', 'dimas-modul-a'),
            'status' => SubmissionStatus::Received, // Pending review in mentor inbox
        ]);

        // Recompute leaderboard for seeded students
        $recomputeJob->forUser($dewi->id);
        $recomputeJob->forUser($raka->id);
        $recomputeJob->forUser($dimas->id);
    }
}
