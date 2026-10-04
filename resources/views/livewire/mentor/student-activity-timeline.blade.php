{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Mentor Student Activity Timeline (/mentor/aktivitas/{user}) --}}
<div class="py-6 sm:py-8">
    <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="bg-sheet border border-rule rounded-panel p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="text-xs uppercase font-mono text-ink-muted">Pemantauan Mentor LKS</div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                        Aktivitas Siswa: {{ $student->name }} ({{ $student->username }})
                    </h1>
                    <p class="text-sm text-ink-muted">
                        Kelas/Rombel: {{ $student->cohorts->pluck('name')->join(', ') ?: '-' }} • Email: {{ $student->email }}
                    </p>
                </div>

                <a href="{{ route('leaderboard') }}" class="inline-flex items-center gap-1 text-xs text-ink-muted hover:text-ink">
                    ← Kembali ke Papan Peringkat
                </a>
            </div>
        </div>

        {{-- Flash message --}}
        @if(session('success'))
            <div class="bg-pass/10 border border-pass/30 text-pass text-xs p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Total Aktif</div>
                <div class="text-2xl font-bold font-mono text-ink mt-1">
                    {{ round($totalActiveMinutes / 60, 1) }} <span class="text-xs font-sans text-ink-muted">jam</span>
                </div>
            </div>

            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Cek Pemahaman</div>
                <div class="text-2xl font-bold font-mono text-pass mt-1">
                    {{ $checkpointsCount }} <span class="text-xs font-sans text-ink-muted">konsep</span>
                </div>
            </div>

            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Latihan Coding</div>
                <div class="text-2xl font-bold font-mono text-brand-deep mt-1">
                    {{ $exercisesPassedCount }} <span class="text-xs font-sans text-ink-muted">lulus</span>
                </div>
            </div>

            <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm">
                <div class="text-xs uppercase font-medium text-ink-muted">Poin Aktivitas</div>
                <div class="text-2xl font-bold font-mono text-ink mt-1">
                    {{ $leaderboardEntry?->activity_points ?? 0 }} <span class="text-xs font-sans text-ink-muted">pts</span>
                </div>
            </div>
        </div>

        {{-- Activity Flags Section --}}
        <div class="bg-sheet border border-rule rounded-panel p-5 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-ink border-b border-rule pb-2 flex items-center justify-between">
                <span>Catatan & Peringatan Aktivitas</span>
                <span class="text-xs font-mono font-normal text-ink-muted">{{ $flags->count() }} catatan</span>
            </h2>

            {{-- Add Flag Form --}}
            <form wire:submit="flagActivity" class="flex gap-2">
                <input
                    type="text"
                    wire:model="flagReason"
                    placeholder="Tulis catatan mentor atau indikasi kejanggalan aktivitas..."
                    class="flex-1 text-xs border border-rule rounded px-3 py-2 focus:ring-1 focus:ring-brand focus:outline-none"
                >
                <button
                    type="submit"
                    class="px-4 py-2 bg-brand text-white text-xs font-medium rounded hover:bg-brand-deep transition-colors"
                >
                    Tambah Catatan
                </button>
            </form>
            @error('flagReason') <span class="text-xs text-brand-deep">{{ $message }}</span> @enderror

            <div class="space-y-2 pt-2">
                @forelse($flags as $flag)
                    <div class="p-3 rounded border {{ $flag->resolved_at ? 'bg-paper border-rule text-ink-muted' : 'bg-tint/30 border-brand-deep/30' }} flex items-start justify-between gap-4 text-xs">
                        <div class="space-y-1">
                            <div class="font-medium text-ink">{{ $flag->reason }}</div>
                            <div class="text-[10px] text-ink-muted">
                                Dibuat: {{ $flag->created_at->format('d M Y H:i') }}
                                @if($flag->resolved_at)
                                    • Diselesaikan oleh {{ $flag->resolver?->name ?? 'Mentor' }} pada {{ $flag->resolved_at->format('d M Y H:i') }}
                                @endif
                            </div>
                        </div>

                        @if(! $flag->resolved_at)
                            <button
                                type="button"
                                wire:click="resolveFlag({{ $flag->id }})"
                                class="text-xs text-pass hover:underline shrink-0"
                            >
                                Tandai Selesai ✓
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="text-xs text-ink-muted text-center py-2">
                        Tidak ada catatan peringatan untuk siswa ini.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Timeline and Daily Logs --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Daily Active Learning --}}
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
                            Belum ada riwayat keaktifan.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Event Logs --}}
            <div class="bg-sheet border border-rule rounded-panel p-5 shadow-sm space-y-3">
                <h2 class="text-base font-bold text-ink border-b border-rule pb-2">
                    Log Interaksi Terbaru
                </h2>

                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    @forelse($events as $event)
                        <div class="p-2.5 rounded border border-rule bg-paper text-xs space-y-1">
                            <div class="flex items-center justify-between text-ink-muted">
                                <span class="font-mono text-[11px] font-bold uppercase text-ink">{{ $event->event_type }}</span>
                                <span class="text-[10px]">{{ $event->occurred_at->format('d/m/Y H:i:s') }}</span>
                            </div>
                            @if($event->metadata)
                                <div class="font-mono text-[10px] text-ink-muted truncate">
                                    {{ json_encode($event->metadata) }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-ink-muted p-4 text-center">
                            Belum ada interaksi.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
