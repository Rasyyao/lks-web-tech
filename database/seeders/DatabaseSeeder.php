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
            'title' => 'Server Side Module: PintarMenabung (REST API & Frontend Integration)',
            'track' => ModuleTrack::Server,
            'level' => 3,
            'summary' => 'Membangun sistem backend REST API berbasis Laravel Sanctum (Phase 1) dan aplikasi frontend terintegrasi berbasis Vue/React/Bootstrap 5 & Axios (Phase 2) dengan pengujian otomatis Postman dan Playwright.',
            'brief_md' => <<<'MD'
# Deskripsi Tugas: PintarMenabung (Financial Management Application)

Sebagai peserta seleksi LKS Web Technologies, kamu diminta untuk membangun sistem pengelolaan keuangan pribadi bernama **PintarMenabung** yang terdiri atas **2 Fase Utama**:
1. **Fase 1 - Backend Development (REST API)** menggunakan Framework Laravel dan otentikasi Laravel Sanctum.
2. **Fase 2 - Frontend Web Application** menggunakan Framework JavaScript pilihan (**React** atau **Vue**, atau **Vanilla JS**) dengan styling **Bootstrap 5**, pemanggilan data via **Axios**, dan visualisasi grafik **Chart.js**.

> 💡 **Informasi Basis Data**:
> Kamu **TIDAK PERLU** membuat file migrasi database dari nol! File SQL Dump (`pintar_menabung.sql`) telah disediakan pada tab **Paket Aset & Starter Kit**. Kamu cukup mengimpor dump SQL tersebut ke database lokal kamu (`mysql -u root -p pintar_menabung < pintar_menabung.sql` atau menggunakan SQLite/DB client).

---

## FASE 1: SPESIFIKASI BACKEND REST API

Seluruh endpoint API berada di bawah prefix `/api`. Semua response wajib menyertakan header `Content-Type: application/json` dan format JSON konsisten.

### A. Otentikasi Pengguna (Sanctum)

#### `POST /api/auth/register`
- **Tujuan**: Mendaftarkan pengguna baru ke dalam aplikasi.
- **Request Headers**: `Accept: application/json`
- **Body (JSON)**:
  - `full_name`: string, required
  - `email`: string, required, format email valid, unique
  - `password`: string, required, minimal 6 karakter
- **Response Berhasil (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Registration successful",
    "data": {
      "id": 6,
      "name": "Dedi",
      "email": "dedi@webtech.id",
      "token": "1|wwuOrgoVe4Xyj5dvUmnf6WTPTaA...",
      "created_at": "2025-07-11T16:01:46.000000Z",
      "updated_at": "2025-07-11T16:01:46.000000Z"
    }
  }
  ```
- **Response Validasi Gagal (422 Unprocessable Entity)**:
  ```json
  {
    "status": "error",
    "message": "Invalid field",
    "errors": {
      "name": ["The name field is required."],
      "email": ["The email has already been taken."],
      "password": ["The password field must be at least 6 characters."]
    }
  }
  ```

#### `POST /api/auth/login`
- **Tujuan**: Masuk ke aplikasi menggunakan email dan kata sandi.
- **Request Headers**: `Accept: application/json`
- **Body (JSON)**:
  - `email`: string, required
  - `password`: string, required
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Login successful",
    "data": {
      "id": 1,
      "name": "Budi",
      "email": "budi@webtech.id",
      "token": "2|0Q2TAdua0FOslp1F4cxfkrzQgkp...",
      "created_at": "2025-07-11T16:01:38.000000Z",
      "updated_at": "2025-07-11T16:01:38.000000Z"
    }
  }
  ```
- **Response Gagal (401 Unauthorized)**:
  ```json
  {
    "status": "error",
    "message": "Username or password incorrect"
  }
  ```

#### `POST /api/auth/logout`
- **Tujuan**: Keluar dari aplikasi dan menonaktifkan token perangkat aktif saat ini.
- **Request Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Logout successful"
  }
  ```

---

### B. Mata Uang & Kategori (Currency & Category)

Kategori digunakan untuk mengelompokkan transaksi dengan 2 tipe:
- `INCOME`: Menambah saldo dompet (misal: gaji, bonus, transfer masuk)
- `EXPENSE`: Mengurangi saldo dompet (misal: belanja, makan/minum, tagihan)

#### `GET /api/currencies`
- **Tujuan**: Mengambil seluruh mata uang yang tersedia.
- **Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Response Berhasil (200 OK)**:
  ```json
  {
    "message": "Get all currencies successful",
    "data": {
      "currencies": [
        { "id": 1, "name": "US Dollar", "symbol": "$", "code": "USD" },
        { "id": 2, "name": "Indonesian Rupiah", "symbol": "Rp", "code": "IDR" }
      ]
    }
  }
  ```

#### `GET /api/categories`
- **Tujuan**: Mengambil seluruh kategori transaksi.
- **Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Get all categories successful",
    "data": {
      "categories": [
        {
          "id": 1,
          "name": "Outgoing Transfer",
          "icon": "💸",
          "type": "EXPENSE"
        }
      ]
    }
  }
  ```

---

### C. Pengelolaan Dompet (Wallet)

Saldo dompet (*balance*) dihitung secara otomatis dan akurat dari seluruh transaksi terkait:
$$\text{balance} = \sum(\text{INCOME}) - \sum(\text{EXPENSE})$$

#### `POST /api/wallets`
- **Tujuan**: Menambahkan dompet baru untuk pengguna yang login.
- **Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Body (JSON)**:
  - `name`: string, required
  - `currency_code`: string, required, valid currency_code data
- **Response Berhasil (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Wallet added successful",
    "data": {
      "id": 26,
      "name": "Savings",
      "user_id": 1,
      "currency_code": "IDR"
    }
  }
  ```

#### `PUT /api/wallets/:walletId`
- **Tujuan**: Memperbarui nama dompet milik sendiri.
- **Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Body (JSON)**: `name`: string, required
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Wallet updated successful",
    "data": {
      "id": 1,
      "user_id": 1,
      "name": "Cash IDR",
      "currency_code": "IDR"
    }
  }
  ```
- **Response Not Found (404)**: `{"status": "error", "message": "Not found"}`
- **Response Forbidden (403)**: `{"status": "error", "message": "Forbidden access"}`

#### `DELETE /api/wallets/:walletId`
- **Tujuan**: Menghapus dompet milik sendiri.
- **Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Response Berhasil (200 OK)**: `{"status": "success", "message": "Wallet deleted successful"}`

#### `GET /api/wallets`
- **Tujuan**: Mengambil seluruh daftar dompet milik pengguna dengan saldo (`balance`) terhitung otomatis.
- **Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Get all wallets successful",
    "data": {
      "wallets": [
        {
          "id": 2,
          "user_id": 1,
          "name": "Bank",
          "currency_code": "IDR",
          "balance": 12250000
        }
      ]
    }
  }
  ```

#### `GET /api/wallets/:walletId`
- **Tujuan**: Mengambil rincian dompet spesifik beserta saldo terkini.
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Get detail wallet successful",
    "data": {
      "id": 1,
      "user_id": 1,
      "name": "Cash IDR",
      "currency_code": "IDR",
      "balance": 4865000
    }
  }
  ```

---

### D. Transaksi Keuangan (Transaction)

Setiap transaksi harus terhubung ke dompet (`wallet_id`) dan kategori (`category_id`).

#### `POST /api/transactions`
- **Tujuan**: Menambahkan catatan transaksi baru.
- **Headers**: `Accept: application/json`, `Authorization: Bearer <TOKEN>`
- **Body (JSON)**:
  - `wallet_id`: integer, required, valid wallet_id data
  - `category_id`: integer, required, valid category_id data
  - `amount`: integer, required, greater or equal 1
  - `date`: string, required, date format "Y-m-d" (contoh: "2025-07-31")
  - `note`: string, optional
- **Response Berhasil (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Transaction added successful",
    "data": {
      "id": 842,
      "wallet_id": 1,
      "category_id": 3,
      "amount": 50000,
      "note": "starbucks",
      "date": "2025-07-31"
    }
  }
  ```

#### `DELETE /api/transactions/:transactionId`
- **Tujuan**: Menghapus transaksi (otomatis mengoreksi saldo dompet).
- **Response Berhasil (200 OK)**: `{"status": "success", "message": "Transaction deleted successful"}`

#### `GET /api/transactions`
- **Tujuan**: Mengambil riwayat transaksi pengguna, terurut berdasarkan tanggal transaksi secara descending (`date DESC`).
- **Query Parameters**:
  - `page`: integer, optional, default 1
  - `per_page`: integer, optional, default 25
  - `month`: integer, optional (1 - 12)
  - `year`: integer, optional (misal: 2025)
- **Response Berhasil (200 OK)**:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 842,
        "amount": 50000,
        "note": "starbucks",
        "date": "2025-07-31",
        "wallet": { "id": 1, "name": "Savings IDR", "currency_code": "IDR" },
        "category": { "id": 3, "name": "Food & Drinks", "icon": "🍔", "type": "EXPENSE" }
      }
    ],
    "from": 1,
    "last_page": 170,
    "per_page": 25,
    "to": 25,
    "total": 170
  }
  ```

---

### E. Laporan Keuangan (Financial Report)

#### `GET /api/reports/summary-by-category/expense`
- **Tujuan**: Mengambil ringkasan akumulasi nominal per kategori pengeluaran per bulan.
- **Query Parameters**: `month` (1-12), `year` (misal: 2025)
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Get summary by expense category successful",
    "data": {
      "summary": [
        {
          "category": {
            "id": 13,
            "name": "Groceries",
            "icon": "🛒",
            "color": "#55EFC4",
            "type": "EXPENSE"
          },
          "amount": 50000
        }
      ]
    }
  }
  ```

#### `GET /api/reports/summary-by-category/income`
- **Tujuan**: Mengambil ringkasan pemasukan per kategori per bulan.
- **Response Berhasil (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Get summary by income category successful",
    "data": {
      "summary": [
        {
          "category": {
            "id": 2,
            "name": "Incoming Transfer",
            "icon": "💰",
            "color": "#00B894",
            "type": "INCOME"
          },
          "amount": 1050000
        }
      ]
    }
  }
  ```

---

## FASE 2: PANDUAN PENGEMBANGAN FRONTEND

Aplikasi web frontend dibangun menggunakan template Bootstrap 5 dengan konsumsi API melalui library **Axios**.

### 1. Halaman & Spesifikasi Fitur

1. **Aturan Umum (General)**:
   - Judul dokumen browser (`document.title`) harus mencerminkan halaman yang sedang dibuka (misal: `"Login - PintarMenabung"`, `"Overview - PintarMenabung"`).
   - Saat pengguna berhasil login, tampilkan tombol **Logout** di navbar. Klik logout akan memanggil endpoint logout dan menghapus token.

2. **Halaman Register (`/register` atau `register.html`)**:
   - Form input: Full Name, Email, Password.
   - Tampilkan pesan error spesifik di bawah field jika validasi gagal.
   - Redirect otomatis ke halaman **Overview** setelah registrasi berhasil.
   - Jika pengguna yang sudah login membuka halaman ini, redirect langsung ke Overview.

3. **Halaman Login (`/login` atau `login.html`)**:
   - Form input: Email dan Password.
   - Tampilkan pesan error jika login gagal.
   - Redirect otomatis ke **Overview** jika login berhasil atau jika sudah memiliki sesi login.

4. **Halaman Utama / Overview (`/` atau `index.html`)**:
   - Hanya dapat diakses oleh user yang sudah login.
   - **Daftar Dompet (User Wallets)**: Menampilkan nama dompet dan saldo saat ini dengan format mata uang (misal: `IDR 5,190,000`).
   - **Daftar Transaksi Terbaru (Recent Transactions)**:
     - Format tanggal ramah pembaca (misal: `"Jul 31, 2025"`).
     - Tanggal hanya ditampilkan sekali per kelompok tanggal (sembunyikan jika tanggal sama dengan transaksi sebelumnya).
     - Kategori dengan ikon & warna: **Merah** untuk pengeluaran dengan tanda minus (`- IDR 50,000`), **Hijau** untuk pemasukan dengan tanda plus (`+ IDR 2,500,000`).
   - Tombol **+ Add Wallet** di bawah seksi balance untuk memunculkan modal tambah dompet.
   - Tombol **+ Add Transaction** untuk mencatat transaksi baru.
   - **Hapus Transaksi**: Double-click pada item transaksi untuk menampilkan dialog konfirmasi penghapusan.

5. **Halaman Detail Wallet (`/wallets/:walletId` atau `wallet-detail.html`)**:
   - Menampilkan nama wallet dan saldo kalkulasi terkini.
   - **Edit Nama Dompet**: Klik pada nama dompet, ubah nama, lalu tekan tombol `Enter` untuk menyimpan ke API.
   - **Hapus Dompet**: Klik nama dompet, kosongkan input teks, lalu tekan `Enter` untuk menampilkan dialog konfirmasi penghapusan.
   - **Filter Transaksi**: Filter per bulan (1–12) dan tahun (rentang pilihan 2015 sampai 2030).
   - **Diagram Donat (Chart.js Doughnut)**: Menampilkan proporsi pengeluaran & pemasukan dari endpoint `/api/reports/summary-by-category/*` dengan label kategori di bagian bawah.
   - **Transfer Uang**: Tombol "Transfer Money" dengan dompet asal terisi otomatis sesuai dompet aktif saat ini.
   - Double-click transaksi untuk menghapus item transaksi.

### 2. Pintasan Keyboard (Shortcuts)
| Tombol Shortcut | Fungsi / Aksi |
| :--- | :--- |
| `Alt + W` | Membuka form modal **"Add Wallet"** |
| `Alt + N` | Membuka form modal **"Add Transaction"** |
| `Alt + T` | Membuka form modal **"Transfer Money"** |
| `Esc` | Menutup form modal yang sedang aktif |

### 3. Konfigurasi Axios & Base URL
Atur base URL API di satu lokasi terpusat:
```javascript
const apiUrl = 'http://localhost:8000/api'; // atau via VITE_API_URL
```
Sertakan Bearer token secara otomatis pada setiap request menggunakan Axios Request Interceptor.
MD,
            'rules_md' => <<<'MD'
## Aturan Teknis & Instruksi Pengumpulan LKS

1. **Integritas Endpoint & Struktur Response**:
   - Dilarang membuat endpoint baru di luar spesifikasi resmi.
   - Anda diperbolehkan menambahkan atribut tambahan pada response body jika diperlukan, namun seluruh atribut wajib pada spesifikasi JSON harus ada dan tipe datanya sesuai.
2. **Kepatuhan Status Code HTTP**:
   - `201 Created` untuk pembuatan data baru (Register, Add Wallet, Add Transaction).
   - `200 OK` untuk pembacaan, update, dan delete sukses.
   - `401 Unauthorized` jika unauthenticated atau password/email salah.
   - `403 Forbidden` jika mengakses atau memodifikasi dompet/transaksi milik user lain.
   - `404 Not Found` jika entitas tidak ditemukan.
   - `422 Unprocessable Entity` jika validasi request body gagal.
3. **Format Berkas Pengumpulan (ZIP)**:
   - Buat folder root dengan format `XX_SERVER_MODULE` (di mana `XX` adalah nomor komputer Anda).
   - Susun berkas sesuai fase:
     - `XX_SERVER_MODULE_PHASE_1.zip`: Berisi folder `BACKEND/`, dump database `db-dump.sql`, dan diagram ER `db-diagram.pdf`.
     - `XX_SERVER_MODULE_PHASE_2.zip`: Berisi folder `public/`, `src/`, dan berkas `package.json` (atau berkas HTML/JS starter).
   - **Wajib mengecualikan** folder `vendor/` dan `node_modules/` saat membuat berkas `.zip`.
MD,
            'duration_minutes' => 180,
            'opens_at' => now()->subDays(2),
            'closes_at' => now()->addDays(20),
            'status' => ModuleStatus::Published,
            'max_attempts_per_day' => 5,
            'version' => 2,
        ]);
        $moduleA->cohorts()->attach([$cohortRpl1->id, $cohortRpl2->id, $cohortSeleksi->id]);

        $moduleB = Module::create([
            'slug' => 'modul-b-client-side-app',
            'title' => 'Client Side Module: Interactive Indonesia Map & Route Finder',
            'track' => ModuleTrack::Client,
            'level' => 3,
            'summary' => 'Mengembangkan aplikasi peta interaktif kepulauan Indonesia berbasis SVG & Vanilla JavaScript murni dengan fitur penanda lokasi, relasi transportasi, dan kalkulasi rute tercepat/termurah.',
            'brief_md' => <<<'MD'
# Deskripsi Tugas: Client-Side Interactive Map (Peta Transportasi Indonesia)

Klien membutuhkan aplikasi peta interaktif visual kepulauan Indonesia yang memungkinkan pengguna menambah pinpoint lokasi, menghubungkan antar lokasi dengan moda transportasi, dan mencari rute terbaik berdasarkan durasi atau biaya.

Aplikasi klien akan dinilai secara otomatis menggunakan peramban Google Chrome / Firefox Developer Edition dan pengujian otomatis **Playwright E2E**.

---

## 1. Fitur Utama Aplikasi

1. **Memuat Peta SVG**:
   - Peta Indonesia berbasis SVG dimuat penuh (*full size zoomed-in*) tanpa menyisakan ruang kosong.
2. **Menambahkan Titik Lokasi (Pinpoint)**:
   - Double-click pada peta memunculkan popup input nama lokasi. Menekan tombol Enter menyimpan lokasi.
   - Titik lokasi ditandai dengan ikon pin merah, menampilkan nama lokasi, tombol hubungkan, dan tombol hapus.
   - Seluruh lokasi tersimpan secara persisten (*Local Storage*) sehingga tidak hilang saat halaman di-refresh.
3. **Menghubungkan Lokasi (Connect Locations)**:
   - Klik ikon hubungkan, lalu klik titik tujuan.
   - Masukkan jarak (kilometer) dan pilih moda transportasi:
     - **Kereta Api (Train)**: Garis `#33E339`, Kecepatan 120 km/jam, Biaya Rp500/km.
     - **Bus**: Garis `#A83BE8`, Kecepatan 80 km/jam, Biaya Rp100/km.
     - **Pesawat (Airplane)**: Garis `#000000`, Kecepatan 800 km/jam, Biaya Rp1.000/km.
   - Garis koneksi muncul menghubungkan kedua titik dengan keterangan jarak di tengah garis.
4. **Pan & Zoom Peta**:
   - Zoom-in dan zoom-out menggunakan `CTRL + Scroll` atau `CTRL + (+)` dan `CTRL + (-)`, berpusat pada posisi kursor.
   - Panning peta (geser kiri/kanan) dengan klik tahan dan geser kursor.
5. **Pencarian Rute (Find Route)**:
   - Modal pencarian rute selalu berada di sisi kiri viewport peramban.
   - Menampilkan hingga 10 rute tercepat (*fastest*) atau termurah (*cheapest*).
MD,
            'rules_md' => <<<'MD'
## Aturan Teknis Penilaian Client-Side
1. **Murni Tanpa Framework**:
   - Dilarang menggunakan framework JS (React, Vue, jQuery, Leaflet, Google Maps API). Gunakan Vanilla JavaScript murni.
   - Dilarang menggunakan framework CSS (Tailwind, Bootstrap). Gunakan CSS3 murni.
2. **Format Pengiriman**:
   - Buat folder root `XX_CLIENT_SIDE_MODULE` di mana `XX` adalah nomor peserta.
   - Pastikan aplikasi berjalan normal ketika membuka berkas `index.html` secara langsung.
   - Arsipkan ke dalam berkas `XX_CLIENT_SIDE_MODULE.zip`.
MD,
            'duration_minutes' => 180,
            'opens_at' => now()->subDays(1),
            'closes_at' => now()->addDays(25),
            'status' => ModuleStatus::Published,
            'max_attempts_per_day' => 5,
            'version' => 1,
        ]);
        $moduleB->cohorts()->attach([$cohortRpl1->id, $cohortRpl2->id, $cohortSeleksi->id]);

        // Module Assets for PintarMenabung (Modul A)
        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'pintar-menabung-full-package.zip',
            'path' => 'assets/pintar-menabung-full-package.zip',
            'size' => 1024 * 18,
        ]);

        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'pintar_menabung.sql',
            'path' => 'assets/pintar_menabung.sql',
            'size' => 1024 * 9,
        ]);

        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'PintarMenabung.postman_collection.json',
            'path' => 'assets/PintarMenabung.postman_collection.json',
            'size' => 1024 * 24,
        ]);

        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'PintarMenabung.postman_environment.json',
            'path' => 'assets/PintarMenabung.postman_environment.json',
            'size' => 350,
        ]);

        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'frontend-starter-kit.zip',
            'path' => 'assets/frontend-starter-kit.zip',
            'size' => 1024 * 12,
        ]);

        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'test-suite-modul-a.json',
            'path' => 'assets/test-suite-modul-a.json',
            'size' => 1024 * 12,
        ]);

        ModuleAsset::create([
            'module_id' => $moduleA->id,
            'label' => 'playwright-suite-modul-a.json',
            'path' => 'assets/playwright-suite-modul-a.json',
            'size' => 1024 * 4,
        ]);

        // Module Assets for Client Side (Modul B)
        ModuleAsset::create([
            'module_id' => $moduleB->id,
            'label' => 'design-mockups-assets.zip',
            'path' => 'assets/design-mockups-assets.zip',
            'size' => 1024 * 850,
        ]);

        ModuleAsset::create([
            'module_id' => $moduleB->id,
            'label' => 'playwright-suite-modul-b.json',
            'path' => 'assets/playwright-suite-modul-b.json',
            'size' => 1024 * 8,
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
            if ($idx === 0) {
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

        // Add scored submissions for Dewi and Raka with test results
        $dewi->submissions()->create([
            'module_id' => $moduleA->id,
            'attempt_no' => 1,
            'path' => 'submissions/modul-a/dewi.zip',
            'original_name' => 'dewi-pintar-menabung.zip',
            'size' => 1024 * 512,
            'sha256' => hash('sha256', 'dewi-pintar-menabung'),
            'status' => SubmissionStatus::Graded,
            'manual_score' => 96,
            'test_score' => 100,
            'test_results' => [
                'passed' => 17,
                'total' => 17,
                'percentage' => 100,
                'status' => 'all_passed',
                'details' => [
                    'auth' => '5/5 Passed (20 pts)',
                    'currency_category' => '2/2 Passed (15 pts)',
                    'wallet' => '5/5 Passed (25 pts)',
                    'transactions' => '3/3 Passed (25 pts)',
                    'reports' => '2/2 Passed (15 pts)',
                ],
            ],
            'feedback_md' => 'Implementasi RESTful API PintarMenabung sangat sempurna! Validasi Sanctum lengkap, kalkulasi saldo atomik, dan seluruh skenario pengujian Postman lulus 100%. Integrasi frontend dengan Chart.js dan Axios juga berjalan lancar.',
            'reviewed_by' => $mentor1->id,
            'reviewed_at' => now(),
        ]);

        $raka->submissions()->create([
            'module_id' => $moduleA->id,
            'attempt_no' => 1,
            'path' => 'submissions/modul-a/raka.zip',
            'original_name' => 'raka-pintar-menabung.zip',
            'size' => 1024 * 480,
            'sha256' => hash('sha256', 'raka-pintar-menabung'),
            'status' => SubmissionStatus::Graded,
            'manual_score' => 88,
            'test_score' => 88,
            'test_results' => [
                'passed' => 15,
                'total' => 17,
                'percentage' => 88.2,
                'status' => 'partial_passed',
                'details' => [
                    'auth' => '5/5 Passed (20 pts)',
                    'currency_category' => '2/2 Passed (15 pts)',
                    'wallet' => '4/5 Passed (20 pts)',
                    'transactions' => '3/3 Passed (25 pts)',
                    'reports' => '1/2 Passed (8 pts)',
                ],
            ],
            'feedback_md' => 'API PintarMenabung berjalan baik. Perhatikan pengecekan kepemilikan wallet saat update (C2) yang masih menghasilkan 404 bukan 403 saat diakses pengguna lain, serta filter bulan pada summary income.',
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
