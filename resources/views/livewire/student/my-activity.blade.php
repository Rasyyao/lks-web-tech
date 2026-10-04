{{-- DESIGN PLAN RECORD --}}
{{-- Screen: My Activity Page (/aktivitas-saya) --}}
<div class="py-6 sm:py-8">
    <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="bg-sheet border border-rule rounded-panel p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                        Aktivitas & Riwayat Belajar Saya
                    </h1>
                    <p class="text-sm text-ink-muted">
                        Pantau kedisiplinan belajar mandiri, cek pemahaman konsep, dan kontribusi aktivitas terhadap peringkat seleksi LKS.
                    </p>
                </div>
                <div>
                    <a href="{{ route('roadmap.index') }}" class="inline-flex items-center px-4 py-2 bg-brand text-white text-xs sm:text-sm font-medium rounded hover:bg-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                        Buka Roadmap Belajar →
                    </a>
                </div>
            </div>
        </div>

        {{-- Metrics Summary Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Total Waktu Aktif</div>
                <div class="text-2xl sm:text-3xl font-bold font-mono text-ink mt-1">
                    {{ round($totalActiveMinutes / 60, 1) }} <span class="text-sm font-sans font-normal text-ink-muted">jam</span>
                </div>
                <div class="text-[11px] text-ink-muted mt-1">{{ $totalActiveMinutes }} menit tercatat aktif</div>
            </div>

            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Cek Pemahaman</div>
                <div class="text-2xl sm:text-3xl font-bold font-mono text-pass mt-1">
                    {{ $checkpointsCount }} <span class="text-sm font-sans font-normal text-ink-muted">dipahami</span>
                </div>
                <div class="text-[11px] text-ink-muted mt-1">dari total 38 konsep kunci</div>
            </div>

            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Latihan Coding</div>
                <div class="text-2xl sm:text-3xl font-bold font-mono text-brand-deep mt-1">
                    {{ $exercisesPassedCount }} <span class="text-sm font-sans font-normal text-ink-muted">lulus</span>
                </div>
                <div class="text-[11px] text-ink-muted mt-1">latihan interaktif di browser</div>
            </div>

            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Poin Aktivitas</div>
                <div class="text-2xl sm:text-3xl font-bold font-mono text-ink mt-1">
                    {{ $leaderboardEntry?->activity_points ?? 0 }} <span class="text-sm font-sans font-normal text-ink-muted">pts</span>
                </div>
                <div class="text-[11px] text-ink-muted mt-1">Total Skor: <strong class="text-ink">{{ $leaderboardEntry?->total ?? 0 }} pts</strong></div>
            </div>
        </div>

        {{-- Breakdown Card --}}
        @if($leaderboardEntry && $leaderboardEntry->breakdown)
            <div class="bg-sheet border border-rule rounded-panel p-5 shadow-sm space-y-3">
                <h2 class="text-base font-bold text-ink border-b border-rule pb-2">
                    Rincian Pembobotan Nilai Peringkat
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-mono">
                    <div class="bg-paper p-3 rounded border border-rule">
                        <div class="text-ink-muted uppercase">Poin Modul & Bank Soal</div>
                        <div class="text-lg font-bold text-ink mt-1">
                            {{ $leaderboardEntry->breakdown['module_score_total'] ?? ($leaderboardEntry->question_points + $leaderboardEntry->module_points) }} pts
                        </div>
                        <div class="text-ink-muted mt-1">Bobot: {{ $leaderboardEntry->breakdown['weight_modules'] ?? 70 }}%</div>
                    </div>

                    <div class="bg-paper p-3 rounded border border-rule">
                        <div class="text-ink-muted uppercase">Poin Aktivitas & Belajar</div>
                        <div class="text-lg font-bold text-brand-deep mt-1">
                            {{ $leaderboardEntry->activity_points }} pts
                        </div>
                        <div class="text-ink-muted mt-1">Bobot: {{ $leaderboardEntry->breakdown['weight_activity'] ?? 30 }}%</div>
                    </div>

                    <div class="bg-paper p-3 rounded border border-rule">
                        <div class="text-ink-muted uppercase">Total Nilai Peringkat</div>
                        <div class="text-lg font-bold text-pass mt-1">
                            {{ $leaderboardEntry->total }} pts
                        </div>
                        <div class="text-ink-muted mt-1">Tercapai: {{ $leaderboardEntry->reached_at?->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Two Columns: Daily Active Log & Timeline --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Daily Active Learning (Last 30 Days) --}}
            <div class="bg-sheet border border-rule rounded-panel p-5 shadow-sm space-y-3">
                <h2 class="text-base font-bold text-ink border-b border-rule pb-2">
                    Keaktifan Harian (30 Hari Terakhir)
                </h2>

                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    @forelse($dailyActivities as $activity)
                        <div class="flex items-center justify-between p-2.5 rounded border border-rule bg-paper text-xs font-mono">
                            <span class="font-medium text-ink">{{ $activity->date->format('d M Y') }}</span>
                            <span class="text-brand-deep font-bold">{{ round($activity->active_seconds / 60) }} menit aktif</span>
                        </div>
                    @empty
                        <div class="text-xs text-ink-muted p-4 text-center">
                            Belum ada riwayat keaktifan belajar yang tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Activity Events Timeline --}}
            <div class="bg-sheet border border-rule rounded-panel p-5 shadow-sm space-y-3">
                <h2 class="text-base font-bold text-ink border-b border-rule pb-2">
                    Log Riwayat Interaksi
                </h2>

                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    @forelse($recentEvents as $event)
                        <div class="p-2.5 rounded border border-rule bg-paper text-xs space-y-1">
                            <div class="flex items-center justify-between text-ink-muted">
                                <span class="font-mono text-[11px] font-bold uppercase text-ink">{{ $event->event_type }}</span>
                                <span class="text-[10px]">{{ $event->occurred_at->diffForHumans() }}</span>
                            </div>
                            @if($event->metadata)
                                <div class="font-mono text-[10px] text-ink-muted truncate">
                                    {{ json_encode($event->metadata) }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-ink-muted p-4 text-center">
                            Belum ada riwayat aktivitas.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
