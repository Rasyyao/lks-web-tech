<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\RoadmapActivityController;
use App\Livewire\Mentor\StudentActivityTimeline;
use App\Livewire\Mentor\SubmissionInbox;
use App\Livewire\Mentor\SubmissionReview;
use App\Livewire\Shared\LeaderboardPage;
use App\Livewire\Shared\Profile;
use App\Livewire\Shared\QuestionBankIndex;
use App\Livewire\Student\AnswerSheet;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\MaterialRoadmap;
use App\Livewire\Student\MaterialShow;
use App\Livewire\Student\ModuleIndex;
use App\Livewire\Student\ModuleShow;
use App\Livewire\Student\MyActivity;
use App\Livewire\Student\PracticeAttempt as PracticeAttemptPage;
use App\Livewire\Student\RoadmapLevel;
use App\Livewire\Student\RoadmapOverview;
use App\Livewire\Student\RoadmapQuiz;
use App\Livewire\Student\RoadmapReference;
use App\Livewire\Student\RoadmapTask;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Landing Routes
|--------------------------------------------------------------------------
*/
Route::get('/', LandingController::class)->name('landing');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'password.fresh'])->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

    // Profile and password change
    Route::get('/profil', Profile::class)->name('profile');
    Route::get('/profil/password', Profile::class)->name('profile.password');

    // Leaderboard (accessible by all authenticated users)
    Route::get('/peringkat', LeaderboardPage::class)
        ->middleware('selection.freeze')
        ->name('leaderboard');

    // Beranda (students see dashboard; mentors and admins are redirected to their home)
    Route::get('/beranda', Dashboard::class)->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Student Routes
    |----------------------------------------------------------------------
    */
    Route::middleware('role:student')->group(function () {
        // Modules
        Route::get('/modul', ModuleIndex::class)->name('modules.index');
        Route::get('/modul/{module:slug}', ModuleShow::class)
            ->middleware('module.visible')
            ->name('modules.show');

        // Practice (Redirect index to Bank Soal)
        Route::prefix('latihan')->group(function () {
            Route::get('/', fn () => redirect()->route('question-bank.index'))->name('practice.start');
            Route::get('/{attempt}', PracticeAttemptPage::class)
                ->middleware(['attempt.owner', 'attempt.open'])
                ->name('practice.attempt');
            Route::get('/{attempt}/hasil', AnswerSheet::class)
                ->middleware('attempt.owner')
                ->name('practice.result');
        });

        // Materials & Topic Bank Soal (Merged into /belajar)
        Route::get('/materi', function () {
            if (request()->has('jalur') && in_array(request('jalur'), ['client', 'server'], true)) {
                return redirect()->route('materials.roadmap', ['track' => request('jalur')]);
            }

            return redirect()->route('roadmap.index', ['tab' => 'silabus']);
        })->name('materials.index');
        Route::get('/materi/roadmap/{track}', MaterialRoadmap::class)->name('materials.roadmap');
        Route::get('/materi/{topic:slug}', MaterialShow::class)->name('materials.show');

        // Interactive Learning Roadmap (Belajar Mandiri)
        Route::get('/belajar', RoadmapOverview::class)->name('roadmap.index');
        Route::get('/belajar/referensi/{slug}', RoadmapReference::class)->name('roadmap.reference');
        Route::get('/belajar/{slug}', RoadmapLevel::class)->name('roadmap.level');
        Route::get('/belajar/{slug}/kuis', RoadmapQuiz::class)->name('roadmap.quiz');
        Route::get('/belajar/{slug}/tugas', RoadmapTask::class)->name('roadmap.task');

        // Student's Activity Dashboard
        Route::get('/aktivitas-saya', MyActivity::class)->name('activity.my');
    });

    /*
    |----------------------------------------------------------------------
    | Activity Tracking & Exercise Attempt Endpoints
    |----------------------------------------------------------------------
    */
    Route::post('/aktivitas/heartbeat', [RoadmapActivityController::class, 'heartbeat'])
        ->middleware('throttle:60,1')
        ->name('activity.heartbeat');
    Route::post('/aktivitas/event', [RoadmapActivityController::class, 'event'])
        ->middleware('throttle:120,1')
        ->name('activity.event');
    Route::post('/aktivitas/section-read', [RoadmapActivityController::class, 'markSectionRead'])
        ->middleware('throttle:60,1')
        ->name('activity.section.read');
    Route::post('/aktivitas/checkpoint-toggle', [RoadmapActivityController::class, 'toggleCheckpoint'])
        ->middleware('throttle:60,1')
        ->name('activity.checkpoint.toggle');
    Route::post('/aktivitas/checkpoint-answer', [RoadmapActivityController::class, 'answerCheckpoint'])
        ->middleware('throttle:60,1')
        ->name('activity.checkpoint.answer');
    Route::post('/belajar/latihan/{exercise}/percobaan', [RoadmapActivityController::class, 'submitExercise'])
        ->middleware('throttle:60,1')
        ->name('roadmap.exercise.submit');
    Route::post('/belajar/exercise/{exercise}/submit', [RoadmapActivityController::class, 'submitExercise'])
        ->middleware('throttle:60,1')
        ->name('roadmap.exercise.submit.alias');

    /*
    |----------------------------------------------------------------------
    | Mentor Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('mentor')
        ->middleware(['role:mentor|admin', 'audit'])
        ->group(function () {
            Route::get('/pengumpulan', SubmissionInbox::class)->name('mentor.submissions');
            Route::get('/pengumpulan/{submission}', SubmissionReview::class)->name('mentor.submission.review');
            Route::get('/aktivitas/{user}', StudentActivityTimeline::class)->name('mentor.activity.timeline');
        });

    // Bank Soal LKS (accessible by all authenticated users)
    Route::get('/bank-soal', QuestionBankIndex::class)->name('question-bank.index');

    /*
    |----------------------------------------------------------------------
    | File Downloads (policy-checked)
    |----------------------------------------------------------------------
    */
    Route::get('/unduh/aset/{asset}', [FileDownloadController::class, 'moduleAsset'])
        ->name('download.asset')
        ->middleware('signed');
    Route::get('/unduh/pengumpulan/{submission}', [FileDownloadController::class, 'submission'])
        ->name('download.submission');
    Route::get('/unduh/bank-soal/{paper}', [FileDownloadController::class, 'questionPaper'])
        ->name('download.question-paper');
});
