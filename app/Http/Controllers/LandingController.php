<?php

namespace App\Http\Controllers;

use App\Enums\ModuleStatus;
use App\Models\Module;
use App\Models\QuestionBankPaper;
use App\Models\RoadmapPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Auth::check()) {
            return redirect()->to(Auth::user()->homeUrl());
        }

        $modules = Module::query()
            ->where('status', ModuleStatus::Published)
            ->orderBy('level')
            ->take(3)
            ->get();

        $totalModulesCount = Module::where('status', ModuleStatus::Published)->count();
        $totalPapersCount = QuestionBankPaper::count();
        $totalLevelsCount = RoadmapPage::where('kind', 'level')->count();

        return view('landing', [
            'modules' => $modules,
            'totalModulesCount' => $totalModulesCount,
            'totalPapersCount' => $totalPapersCount,
            'totalLevelsCount' => $totalLevelsCount,
        ]);
    }
}
