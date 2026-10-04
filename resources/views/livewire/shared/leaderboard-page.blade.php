{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Leaderboard (/peringkat) --}}
{{-- Primary job of the screen: Transparent ranking of students based on distinct question points and module scores. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, gold, gold-text, tint, paper --}}
{{-- Type roles: Schibsted Grotesk for headers and student names; tabular-nums for ranks and scores; JetBrains Mono for usernames --}}
{{-- Layout idea: Header with native cohort selector, my current rank summary, followed by a plain responsive table --}}
{{-- What I changed after the "would any app have this?" check: Removed podium illustrations, medal emojis, card grids; kept strictly school-exam score sheet table with gold badge for rank 1 only. --}}

<div wire:poll.30s class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-ink">
                {{ __('leaderboard.title') }}
            </h1>
            <p class="text-xs text-ink-muted">
                {{ __('general.school_name') }} — Seleksi LKS Web Technologies
            </p>
        </div>

        {{-- Scope selector with native select per DESIGN_RULES --}}
        <div class="flex items-center gap-2">
            <label for="cohort-filter" class="text-sm font-medium text-ink shrink-0">
                Filter:
            </label>
            <select
                id="cohort-filter"
                wire:model.live="cohort"
                class="rounded-cell border border-rule px-3 py-1.5 text-sm bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
            >
                <option value="all">{{ __('leaderboard.all_classes') }}</option>
                @foreach($cohorts as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Highlight current student position if logged in as student --}}
    @if($currentUserEntry)
        <div class="bg-tint border-l-[3px] border-brand p-4 rounded-panel flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-xs text-brand-deep font-medium">{{ __('leaderboard.my_position') }}</span>
                <p class="text-sm font-bold text-ink">
                    Peringkat ke-{{ $currentUserRank }} dari {{ count($rankedEntries) }} siswa
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs text-ink-muted block">{{ __('leaderboard.total') }}</span>
                <span class="text-lg font-bold text-ink tabular-nums">{{ $currentUserEntry->total }}</span>
            </div>
        </div>
    @endif

    {{-- Leaderboard Table --}}
    <x-table>
        <thead class="bg-paper border-b border-rule">
            <tr>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-center w-16">
                    {{ __('leaderboard.rank') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted">
                    {{ __('leaderboard.name') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted hidden sm:table-cell">
                    {{ __('leaderboard.class') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-right">
                    {{ __('leaderboard.question_points') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-right">
                    {{ __('leaderboard.module_points') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-right">
                    Aktivitas
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-right">
                    {{ __('leaderboard.total') }}
                </th>
                @if(auth()->user()->hasAnyRole(['mentor', 'admin']))
                    <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-center w-20">
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
                @endphp
                <tr class="{{ $isSelf ? 'bg-tint border-l-[3px] border-brand' : 'hover:bg-paper/50' }}">
                    <td class="py-3 px-4 text-center tabular-nums font-medium">
                        @if($rank === 1)
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-cell bg-gold text-gold-text font-bold text-xs" title="Peringkat 1">
                                1
                            </span>
                        @else
                            <span class="text-ink-muted">{{ $rank }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium {{ $isSelf ? 'text-brand-deep font-bold' : 'text-ink' }}">
                            {{ $entry->user->name }}
                            @if($isSelf)
                                <span class="text-xs font-normal text-brand-deep ml-1">(Kamu)</span>
                            @endif
                        </div>
                        <div class="text-xs text-ink-muted font-mono sm:hidden">
                            {{ $entry->user->cohorts->pluck('name')->join(', ') ?: '—' }}
                        </div>
                    </td>
                    <td class="py-3 px-4 text-ink-muted hidden sm:table-cell text-xs">
                        {{ $entry->user->cohorts->pluck('name')->join(', ') ?: '—' }}
                    </td>
                    <td class="py-3 px-4 text-right tabular-nums text-ink">
                        {{ $entry->question_points }}
                    </td>
                    <td class="py-3 px-4 text-right tabular-nums text-ink">
                        {{ $entry->module_points }}
                    </td>
                    <td class="py-3 px-4 text-right tabular-nums text-brand-deep font-mono">
                        {{ $entry->activity_points ?? 0 }}
                    </td>
                    <td class="py-3 px-4 text-right tabular-nums font-bold text-ink">
                        {{ $entry->total }}
                    </td>
                    @if(auth()->user()->hasAnyRole(['mentor', 'admin']))
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('mentor.activity.timeline', $entry->user_id) }}" class="text-xs text-brand-deep hover:underline font-medium">
                                Detail
                            </a>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasAnyRole(['mentor', 'admin']) ? 8 : 7 }}" class="py-8 text-center text-sm text-ink-muted">
                        {{ __('leaderboard.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
</div>
