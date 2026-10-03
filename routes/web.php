<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileDownloadController;
use App\Livewire\Shared\LeaderboardPage;
use App\Livewire\Shared\Profile;
use App\Livewire\Student\AnswerSheet;
use App\Livewire\Student\Dashboard;
use App\Livewire\Student\MaterialIndex;
use App\Livewire\Student\MaterialShow;
use App\Livewire\Student\ModuleIndex;
use App\Livewire\Student\ModuleShow;
use App\Livewire\Student\PracticeAttempt as PracticeAttemptPage;
use App\Livewire\Student\PracticeStart;
use App\Livewire\Mentor\SubmissionInbox;
use App\Livewire\Mentor\SubmissionReview;
use Illuminate\Support\Facades\Route;

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

    // Root sends everyone to their own home (student / mentor / admin).
    Route::get('/', fn () => redirect()->to(auth()->user()->homeUrl()));

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

        // Practice
        Route::prefix('latihan')->group(function () {
            Route::get('/', PracticeStart::class)->name('practice.start');
            Route::get('/{attempt}', PracticeAttemptPage::class)
                ->middleware(['attempt.owner', 'attempt.open'])
                ->name('practice.attempt');
            Route::get('/{attempt}/hasil', AnswerSheet::class)
                ->middleware('attempt.owner')
                ->name('practice.result');
        });

        // Materials
        Route::get('/materi', MaterialIndex::class)->name('materials.index');
        Route::get('/materi/{topic:slug}', MaterialShow::class)->name('materials.show');
    });

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
        });

    // Bank Soal LKS (accessible by all authenticated users)
    Route::get('/bank-soal', \App\Livewire\Shared\QuestionBankIndex::class)->name('question-bank.index');

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
