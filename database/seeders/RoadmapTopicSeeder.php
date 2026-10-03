<?php

namespace Database\Seeders;

use App\Enums\ModuleTrack;
use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Models\Material;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoadmapTopicSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Client-side Topics
        $topicHtml = Topic::updateOrCreate(
            ['slug' => 'html-semantics'],
            [
                'name' => 'HTML5 Foundation & Web Semantics',
                'track' => ModuleTrack::Client,
                'description' => 'Struktur semantik dokumen, form validation native, accessibility (ARIA), SEO meta, dan elemen HTML5 modern.',
                'position' => 1,
            ]
        );

        $topicCss = Topic::updateOrCreate(
            ['slug' => 'css-layout'],
            [
                'name' => 'Modern CSS, Flexbox & Grid Layout',
                'track' => ModuleTrack::Client,
                'description' => 'Sistem tata letak CSS modern, Flexbox, CSS Grid, media queries responsif (mobile-first), dan komponen UI.',
                'position' => 2,
            ]
        );

        $topicJsCore = Topic::updateOrCreate(
            ['slug' => 'javascript-core'],
            [
                'name' => 'Modern JavaScript (ES6+) & Core Logic',
                'track' => ModuleTrack::Client,
                'description' => 'Sintaks ES6+ (arrow functions, destructuring, modules), array methods (map, filter, reduce), promise & async/await.',
                'position' => 3,
            ]
        );

        $topicDom = Topic::updateOrCreate(
            ['slug' => 'javascript-dom'],
            [
                'name' => 'JavaScript DOM Manipulation & Events',
                'track' => ModuleTrack::Client,
                'description' => 'Seleksi dan manipulasi elemen DOM vanilla, dynamic rendering, event bubbling/delegation, serta Web Storage & Fetch API.',
                'position' => 4,
            ]
        );

        // 2. Server-side Topics
        $topicPhp = Topic::updateOrCreate(
            ['slug' => 'php-backend'],
            [
                'name' => 'PHP Fundamentals & OOP Architecture',
                'track' => ModuleTrack::Server,
                'description' => 'Pemrograman PHP 8 modern, Object-Oriented Programming (Class, Interface, Trait), MVC pattern, dan security best practices.',
                'position' => 1,
            ]
        );

        $topicApi = Topic::updateOrCreate(
            ['slug' => 'rest-api-db'],
            [
                'name' => 'REST API Design & MySQL Database',
                'track' => ModuleTrack::Server,
                'description' => 'Perancangan RESTful endpoints (GET/POST/PUT/DELETE), HTTP status code, format JSON terstandar, PDO, dan optimasi query MySQL.',
                'position' => 2,
            ]
        );

        $topicLaravel = Topic::updateOrCreate(
            ['slug' => 'laravel-framework'],
            [
                'name' => 'Laravel Framework & Eloquent ORM',
                'track' => ModuleTrack::Server,
                'description' => 'Routing, Controllers, Form Requests validation, Eloquent relationships & migrations, Middleware, dan Service layer.',
                'position' => 3,
            ]
        );

        $topicVueAxios = Topic::updateOrCreate(
            ['slug' => 'vue-react-axios'],
            [
                'name' => 'Frontend Integration (Vue / React & Axios)',
                'track' => ModuleTrack::Server,
                'description' => 'Konsumsi REST API backend dari frontend (Vue.js / React) menggunakan Axios, CORS handling, authentication token, dan reaktifitas state.',
                'position' => 4,
            ]
        );

        // 3. Materials
        if (! Material::where('topic_id', $topicApi->id)->exists()) {
            Material::create([
                'topic_id' => $topicApi->id,
                'title' => 'Prinsip Perancangan RESTful API dan Struktur Database MySQL',
                'level' => 2,
                'position' => 1,
                'body_md' => <<<'MD'
Perancangan RESTful API yang konsisten merupakan poin krusial dalam penilaian LKS Web Technology Modul Server-Side.

### 1. HTTP Methods & Resource Naming
- `GET /api/v1/doctors`: Mengambil daftar data (plural noun).
- `POST /api/v1/doctors`: Membuat resource dokter baru.
- `GET /api/v1/doctors/{id}`: Mengambil detail satu dokter.
- `PUT /api/v1/doctors/{id}`: Memperbarui seluruh field data dokter.
- `DELETE /api/v1/doctors/{id}`: Menghapus resource dokter.

### 2. Standar Response JSON
Setiap endpoint API harus mengembalikan format payload yang seragam:

```json
{
  "success": true,
  "message": "Dokter berhasil ditambahkan",
  "data": {
    "id": 12,
    "name": "dr. Andi Pratama, Sp.A",
    "specialist": "Anak",
    "created_at": "2026-10-03T10:00:00Z"
  }
}
```
MD,
            ]);
        }

        if (! Material::where('topic_id', $topicLaravel->id)->exists()) {
            Material::create([
                'topic_id' => $topicLaravel->id,
                'title' => 'Arsitektur Laravel: Routing, Form Request, dan Relasi Eloquent',
                'level' => 3,
                'position' => 1,
                'body_md' => <<<'MD'
Pada LKS Web Technology Modul Server-side (Module A), framework **Laravel** adalah fondasi utama untuk membangun backend API yang kokoh dan teruji.

### 1. Resource Routing & Controller
Gunakan controller yang tipis (*skinny controller*) dan pisahkan logika validasi ke Form Request:

```php
// routes/api.php
Route::apiResource('consultations', ConsultationController::class);
```

### 2. Form Request Validation
Pastikan validasi input dilakukan secara ketat sebelum menyentuh database:

```php
public function rules(): array
{
    return [
        'patient_name' => ['required', 'string', 'max:255'],
        'consultation_date' => ['required', 'date', 'after:today'],
        'doctor_id' => ['required', 'exists:doctors,id'],
    ];
}
```

### 3. Eloquent Relationships
Manfaatkan eager loading (`with()`) untuk mencegah masalah N+1 query:

```php
$consultations = Consultation::with(['doctor', 'patient'])
    ->where('status', 'scheduled')
    ->get();
```
MD,
            ]);
        }

        if (! Material::where('topic_id', $topicVueAxios->id)->exists()) {
            Material::create([
                'topic_id' => $topicVueAxios->id,
                'title' => 'Integrasi API Frontend: Vue.js / React dengan Axios Client',
                'level' => 3,
                'position' => 1,
                'body_md' => <<<'MD'
Modul Server-side LKS sering menguji integrasi frontend (SPA menggunakan Vue atau React) yang mengonsumsi endpoint backend REST API via **Axios**.

### 1. Inisialisasi Axios Client & Interceptors
Konfigurasikan base URL dan header Authorization Bearer token secara terpusat:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/v1',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
```

### 2. Fetching Data dan Penanganan Error
Gunakan async/await dan tangani status code spesifik seperti 401 (Unauthenticated) dan 422 (Validation Error):

```javascript
async function fetchDoctors() {
  try {
    const response = await api.get('/doctors');
    return response.data.data;
  } catch (err) {
    if (err.response?.status === 401) {
      window.location.href = '/login';
    }
    throw err;
  }
}
```
MD,
            ]);
        }

        if (! Material::where('topic_id', $topicJsCore->id)->exists()) {
            Material::create([
                'topic_id' => $topicJsCore->id,
                'title' => 'JavaScript Modern (ES6+): Asynchronous, Promises & Array Methods',
                'level' => 2,
                'position' => 1,
                'body_md' => <<<'MD'
Kemampuan logika JavaScript modern tanpa library adalah kunci sukses dalam menyelesaikan Modul Client-side LKS.

### 1. Array Transformation
- `map()`: Mengubah setiap elemen menjadi elemen baru tanpa memutasi array asli.
- `filter()`: Menyaring elemen berdasarkan kondisi boolean.
- `reduce()`: Mengakumulasi array menjadi nilai tunggal.

```javascript
const scores = [85, 92, 78, 96, 88];
const average = scores.reduce((sum, val) => sum + val, 0) / scores.length;
```
MD,
            ]);
        }

        // 4. Questions
        $mentor = User::where('username', 'mentor1')->first() ?? User::first();

        if (! Question::where('topic_id', $topicLaravel->id)->exists()) {
            $q1 = Question::create([
                'topic_id' => $topicLaravel->id,
                'type' => QuestionType::MultipleChoice,
                'body_md' => 'Fitur apakah di Laravel yang digunakan untuk memisahkan aturan validasi request dari controller secara terstruktur?',
                'difficulty' => 3,
                'points' => 15,
                'explanation_md' => 'Form Request di Laravel adalah class khusus yang merangkum aturan validasi (`rules()`) dan otorisasi (`authorize()`) input HTTP.',
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $mentor?->id,
            ]);
            $q1->options()->createMany([
                ['label' => 'Form Request', 'is_correct' => true, 'position' => 1],
                ['label' => 'Middleware', 'is_correct' => false, 'position' => 2],
                ['label' => 'Resource Collection', 'is_correct' => false, 'position' => 3],
                ['label' => 'Eloquent Scope', 'is_correct' => false, 'position' => 4],
            ]);

            $q2 = Question::create([
                'topic_id' => $topicLaravel->id,
                'type' => QuestionType::TrueFalse,
                'difficulty' => 2,
                'points' => 10,
                'body_md' => 'Perintah `Route::apiResource()` di Laravel secara otomatis mendaftarkan route create dan edit untuk rendering form HTML.',
                'explanation_md' => 'Salah. `apiResource()` mengecualikan route `create` dan `edit` karena keduanya hanya ditujukan untuk rendering halaman form HTML, bukan API.',
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $mentor?->id,
            ]);
            $q2->options()->createMany([
                ['label' => 'Benar', 'is_correct' => false, 'position' => 1],
                ['label' => 'Salah', 'is_correct' => true, 'position' => 2],
            ]);

            $q3 = Question::create([
                'topic_id' => $topicLaravel->id,
                'type' => QuestionType::ShortAnswer,
                'difficulty' => 2,
                'points' => 10,
                'body_md' => 'Perintah Artisan apakah yang digunakan untuk membuat Model sekaligus file migration di Laravel?',
                'explanation_md' => 'Opsi flag `-m` atau `--migration` memerintahkan Laravel membuat file migration tabel terkait bersamaan dengan pembuatan Model.',
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $mentor?->id,
            ]);
            $q3->answers()->createMany([
                ['text' => 'php artisan make:model -m', 'normalized' => 'php artisan make:model -m'],
                ['text' => 'make:model -m', 'normalized' => 'make:model -m'],
            ]);
        }

        if (! Question::where('topic_id', $topicVueAxios->id)->exists()) {
            $q4 = Question::create([
                'topic_id' => $topicVueAxios->id,
                'type' => QuestionType::MultipleChoice,
                'body_md' => 'Header HTTP apakah yang digunakan klien Axios untuk mengirimkan token otentikasi Bearer ke backend API?',
                'difficulty' => 3,
                'points' => 15,
                'explanation_md' => 'Standar HTTP Authorization menggunakan format `Authorization: Bearer <token>` untuk otentikasi token.',
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $mentor?->id,
            ]);
            $q4->options()->createMany([
                ['label' => 'Authorization: Bearer <token>', 'is_correct' => true, 'position' => 1],
                ['label' => 'Authentication: Token <token>', 'is_correct' => false, 'position' => 2],
                ['label' => 'X-Auth-Token: <token>', 'is_correct' => false, 'position' => 3],
                ['label' => 'Token: <token>', 'is_correct' => false, 'position' => 4],
            ]);

            $q5 = Question::create([
                'topic_id' => $topicVueAxios->id,
                'type' => QuestionType::TrueFalse,
                'difficulty' => 2,
                'points' => 10,
                'body_md' => 'CORS (Cross-Origin Resource Sharing) harus dikonfigurasi dan diizinkan pada server backend agar request dari frontend SPA berbeda origin tidak diblokir browser.',
                'explanation_md' => 'Benar. Browser menerapkan kebijakan Same-Origin Policy (SOP), sehingga backend harus mengembalikan header CORS seperti `Access-Control-Allow-Origin`.',
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $mentor?->id,
            ]);
            $q5->options()->createMany([
                ['label' => 'Benar', 'is_correct' => true, 'position' => 1],
                ['label' => 'Salah', 'is_correct' => false, 'position' => 2],
            ]);
        }

        if (! Question::where('topic_id', $topicJsCore->id)->exists()) {
            $q6 = Question::create([
                'topic_id' => $topicJsCore->id,
                'type' => QuestionType::MultipleChoice,
                'body_md' => 'Kata kunci apakah di JavaScript ES6 yang digunakan untuk mendeklarasikan variabel yang nilainya tidak dapat di-reassign?',
                'difficulty' => 2,
                'points' => 10,
                'explanation_md' => '`const` menciptakan variabel berlingkup blok (block-scoped) yang nilainya tidak dapat di-reassign setelah inisialisasi.',
                'status' => QuestionStatus::Published,
                'version' => 1,
                'created_by' => $mentor?->id,
            ]);
            $q6->options()->createMany([
                ['label' => 'const', 'is_correct' => true, 'position' => 1],
                ['label' => 'let', 'is_correct' => false, 'position' => 2],
                ['label' => 'var', 'is_correct' => false, 'position' => 3],
                ['label' => 'static', 'is_correct' => false, 'position' => 4],
            ]);
        }
    }
}
