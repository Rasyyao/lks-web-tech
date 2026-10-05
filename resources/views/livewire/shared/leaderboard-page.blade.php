{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Leaderboard (/peringkat) --}}
{{-- Design Concept: Matches website design system (white sheets, crisp rules, paper KPI boxes, Telkom red accents, and fun Indonesian student tier jokes: "OTW Nasional Ini Mah 🚀", "Ayo Masa Tahun Depan 🔥", "Minimal Usaha Dulu ☕", etc.) --}}

<div wire:poll.30s class="space-y-6">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center gap-1.5 text-xs text-ink-muted">
            <li>
                <a href="{{ auth()->check() && auth()->user()->hasRole('student') ? route('dashboard') : (auth()->user()?->hasRole(['mentor', 'admin']) ? route('mentor.submissions') : route('login')) }}" class="hover:text-brand-deep transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                    Beranda
                </a>
            </li>
            <li aria-hidden="true" class="text-rule">/</li>
            <li class="text-ink font-medium">
                Papan Peringkat
            </li>
        </ol>
    </nav>

    {{-- Header Banner & Filter --}}
    <x-panel class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink">
                        {{ __('leaderboard.title') }}
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-cell font-mono text-xs font-bold bg-tint text-brand-deep border border-brand-deep/20">
                        Seleksi LKS 2026
                    </span>
                </div>
                <p class="text-xs text-ink-muted mt-1 max-w-2xl leading-relaxed">
                    Peringkat akumulasi skor seleksi calon delegasi SMK Telkom Purwokerto bidang Web Technologies berdasarkan hasil tugas modul praktik, bank soal harian, dan keaktifan belajar.
                </p>
            </div>

            {{-- Filter Cohort Dropdown --}}
            <div class="flex items-center gap-2">
                <label for="cohort-filter" class="text-xs font-semibold text-ink-muted shrink-0">
                    Filter Kelas:
                </label>
                <select
                    id="cohort-filter"
                    wire:model.live="cohort"
                    class="rounded-cell border border-rule px-3 py-1.5 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-brand-deep shadow-xs font-medium"
                >
                    <option value="all">{{ __('leaderboard.all_classes') }}</option>
                    @foreach($cohorts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Highlight Current Student Position (if logged in as student) --}}
        @if($currentUserEntry)
            <div class="p-3.5 bg-paper rounded-cell border border-rule flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-brand/10 text-brand-deep font-extrabold text-sm flex items-center justify-center border border-brand/20 shrink-0">
                        #{{ $currentUserRank }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-brand-deep uppercase tracking-wider block">
                            {{ __('leaderboard.my_position') }}
                        </span>
                        <p class="text-xs sm:text-sm font-bold text-ink">
                            Peringkat ke-{{ $currentUserRank }} <span class="font-normal text-ink-muted">dari {{ count($rankedEntries) }} peserta</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-5 sm:border-l sm:border-rule sm:pl-5 text-xs">
                    <div>
                        <span class="text-[10px] text-ink-muted uppercase block">Poin Soal</span>
                        <span class="font-bold text-ink tabular-nums">{{ $currentUserEntry->question_points }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-ink-muted uppercase block">Poin Modul</span>
                        <span class="font-bold text-ink tabular-nums">{{ $currentUserEntry->module_points }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-ink-muted uppercase block">Aktivitas</span>
                        <span class="font-bold text-ink tabular-nums">{{ $currentUserEntry->activity_points ?? 0 }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-brand-deep font-bold uppercase block">{{ __('leaderboard.total') }}</span>
                        <span class="text-lg font-extrabold text-brand-deep tabular-nums">{{ $currentUserEntry->total }}</span>
                    </div>
                </div>
            </div>
        @endif
    </x-panel>

    {{-- TOP 3 PODIUM CARDS (MATCHING SITE CARD GRID STYLE) --}}
    @if(count($topThree) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($topThree as $podium)
                @php
                    $pRank = $podium['rank'];
                    $pEntry = $podium['entry'];
                    $pUser = $pEntry->user;
                    $pIsSelf = $podium['is_current_user'];

                    // Funny student tier jokes & styling matching site palette
                    if ($pRank === 1) {
                        $topBorder = 'border-t-4 border-t-gold';
                        $avatarBg = 'bg-gold/15 text-gold-text border-gold/40';
                        $trophyBadge = '🏆 Juara 1';
                        $trophyBadgeStyle = 'bg-gold/15 text-gold-text border border-gold/30 font-bold';
                        $tierJoke = 'OTW Nasional Ini Mah 🚀';
                        $tierJokeStyle = 'bg-gold/10 text-gold-text border border-gold/30 font-bold';
                    } elseif ($pRank === 2) {
                        $topBorder = 'border-t-4 border-t-brand';
                        $avatarBg = 'bg-tint text-brand-deep border-brand/30';
                        $trophyBadge = '🥈 Juara 2';
                        $trophyBadgeStyle = 'bg-tint text-brand-deep border border-brand/20 font-bold';
                        $tierJoke = 'Ayo Masa Tahun Depan 🔥';
                        $tierJokeStyle = 'bg-tint text-brand-deep border border-brand/20 font-bold';
                    } else {
                        $topBorder = 'border-t-4 border-t-amber-600';
                        $avatarBg = 'bg-amber-100 text-amber-800 border-amber-300';
                        $trophyBadge = '🥉 Juara 3';
                        $trophyBadgeStyle = 'bg-amber-50 text-amber-800 border border-amber-200 font-bold';
                        $tierJoke = 'Minimal Usaha Dulu ☕';
                        $tierJokeStyle = 'bg-amber-50 text-amber-800 border border-amber-200 font-bold';
                    }
                @endphp

                <div class="bg-sheet border border-rule rounded-panel p-5 flex flex-col justify-between space-y-4 shadow-sm hover:border-brand/40 transition-colors {{ $topBorder }} {{ $pIsSelf ? 'ring-2 ring-brand' : '' }}">
                    <div class="space-y-3">
                        {{-- Top Header Row: User Info & Trophy Badge --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full {{ $avatarBg }} font-extrabold text-sm flex items-center justify-center shrink-0 border">
                                    {{ strtoupper(substr($pUser->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base font-bold text-ink truncate flex items-center gap-1.5" title="{{ $pUser->name }}">
                                        <span>{{ $pUser->name }}</span>
                                        @if($pIsSelf)
                                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-cell bg-brand text-white">Kamu</span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-ink-muted truncate font-mono">
                                        {{ $pUser->cohorts->pluck('name')->join(', ') ?: 'Peserta Seleksi' }}
                                    </p>
                                </div>
                            </div>

                            <span class="px-2 py-0.5 rounded-cell font-mono text-[11px] shrink-0 {{ $trophyBadgeStyle }}">
                                {{ $trophyBadge }}
                            </span>
                        </div>

                        {{-- Tier Joke Pill --}}
                        <div class="pt-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-cell text-xs {{ $tierJokeStyle }}">
                                <span>{{ $tierJoke }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- 3 KPI Metric Stat Boxes (Matching Bank Soal Card Style) --}}
                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-rule text-center">
                        <div class="p-2 bg-paper rounded-cell border border-rule">
                            <span class="text-[10px] text-ink-muted uppercase font-semibold block">Soal</span>
                            <span class="text-sm font-bold font-mono text-ink tabular-nums block mt-0.5">
                                {{ $pEntry->question_points }}
                            </span>
                        </div>

                        <div class="p-2 bg-paper rounded-cell border border-rule">
                            <span class="text-[10px] text-ink-muted uppercase font-semibold block">Modul</span>
                            <span class="text-sm font-bold font-mono text-ink tabular-nums block mt-0.5">
                                {{ $pEntry->module_points }}
                            </span>
                        </div>

                        <div class="p-2 bg-paper rounded-cell border border-rule">
                            <span class="text-[10px] text-ink-muted uppercase font-semibold block">Total</span>
                            <span class="text-sm font-bold font-mono text-brand-deep tabular-nums block mt-0.5">
                                {{ $pEntry->total }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- RANKINGS TABLE (CLEAN STANDARD SITE COMPONENT) --}}
    <div class="bg-sheet border border-rule rounded-panel overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-ink">
                <thead class="bg-paper border-b border-rule text-xs font-semibold text-ink-muted">
                    <tr>
                        <th scope="col" class="py-3 px-4 text-center w-16">
                            Peringkat
                        </th>
                        <th scope="col" class="py-3 px-4 min-w-[200px]">
                            Nama Siswa
                        </th>
                        <th scope="col" class="py-3 px-4 hidden md:table-cell">
                            Kelas
                        </th>
                        <th scope="col" class="py-3 px-4 text-right">
                            Poin Soal
                        </th>
                        <th scope="col" class="py-3 px-4 text-right">
                            Poin Modul
                        </th>
                        <th scope="col" class="py-3 px-4 text-right">
                            Aktivitas
                        </th>
                        <th scope="col" class="py-3 px-4 text-right">
                            Total Skor
                        </th>
                        <th scope="col" class="py-3 px-4 text-center w-48">
                            Status Tier
                        </th>
                        @if(auth()->user()->hasAnyRole(['mentor', 'admin']))
                            <th scope="col" class="py-3 px-4 text-center w-20">
                                Aksi
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule">
                    @forelse($rankedEntries as $item)
                        @php
                            $isSelf = $item['is_current_user'];
                            $rank = $item['rank'];
                            $entry = $item['entry'];
                            $user = $entry->user;

                            // Tier Joke Mapping
                            if ($rank === 1) {
                                $tier = [
                                    'name' => 'OTW Nasional Ini Mah 🚀',
                                    'badge' => 'bg-gold/15 text-gold-text border border-gold/30 font-bold',
                                ];
                            } elseif ($rank === 2) {
                                $tier = [
                                    'name' => 'Ayo Masa Tahun Depan 🔥',
                                    'badge' => 'bg-tint text-brand-deep border border-brand/20 font-bold',
                                ];
                            } elseif ($rank === 3) {
                                $tier = [
                                    'name' => 'Minimal Usaha Dulu ☕',
                                    'badge' => 'bg-amber-50 text-amber-800 border border-amber-200 font-bold',
                                ];
                            } elseif ($rank <= 5) {
                                $tier = [
                                    'name' => 'Masih Pemanasan 🏃',
                                    'badge' => 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold',
                                ];
                            } else {
                                $tier = [
                                    'name' => 'AFK di Lobby 🎮',
                                    'badge' => 'bg-paper text-ink-muted border border-rule font-medium',
                                ];
                            }
                        @endphp
                        <tr class="transition-colors {{ $isSelf ? 'bg-tint border-l-4 border-l-brand' : 'hover:bg-paper/50' }}">
                            {{-- Peringkat Number --}}
                            <td class="py-3 px-4 text-center tabular-nums font-medium">
                                @if($rank === 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-cell bg-gold text-gold-text font-bold text-xs" title="Juara 1">
                                        1
                                    </span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-cell bg-paper text-ink border border-rule font-bold text-xs" title="Juara 2">
                                        2
                                    </span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-cell bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs" title="Juara 3">
                                        3
                                    </span>
                                @else
                                    <span class="text-ink-muted text-xs font-mono font-semibold">
                                        {{ $rank }}
                                    </span>
                                @endif
                            </td>

                            {{-- Nama Siswa & Avatar --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-paper text-ink font-bold text-xs flex items-center justify-center border border-rule shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium {{ $isSelf ? 'text-brand-deep font-bold' : 'text-ink' }}">
                                            <span>{{ $user->name }}</span>
                                            @if($isSelf)
                                                <span class="text-xs font-normal text-brand-deep ml-1">(Kamu)</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-ink-muted font-mono md:hidden">
                                            {{ $user->cohorts->pluck('name')->join(', ') ?: '—' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kelas --}}
                            <td class="py-3 px-4 text-ink-muted hidden md:table-cell text-xs font-mono">
                                {{ $user->cohorts->pluck('name')->join(', ') ?: '—' }}
                            </td>

                            {{-- Poin Soal --}}
                            <td class="py-3 px-4 text-right tabular-nums text-ink">
                                {{ $entry->question_points }}
                            </td>

                            {{-- Poin Modul --}}
                            <td class="py-3 px-4 text-right tabular-nums text-ink">
                                {{ $entry->module_points }}
                            </td>

                            {{-- Aktivitas --}}
                            <td class="py-3 px-4 text-right tabular-nums text-brand-deep font-mono">
                                {{ $entry->activity_points ?? 0 }}
                            </td>

                            {{-- Total Skor --}}
                            <td class="py-3 px-4 text-right tabular-nums font-bold text-ink">
                                {{ $entry->total }}
                            </td>

                            {{-- Status Tier Joke Badge --}}
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs {{ $tier['badge'] }}">
                                    {{ $tier['name'] }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            @if(auth()->user()->hasAnyRole(['mentor', 'admin']))
                                <td class="py-3 px-4 text-center">
                                    <a
                                        href="{{ route('mentor.activity.timeline', $entry->user_id) }}"
                                        class="text-xs text-brand-deep hover:underline font-medium"
                                    >
                                        Detail
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasAnyRole(['mentor', 'admin']) ? 9 : 8 }}" class="py-8 text-center text-sm text-ink-muted">
                                {{ __('leaderboard.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
