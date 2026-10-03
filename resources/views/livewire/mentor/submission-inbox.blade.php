{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Mentor Submission Inbox (/mentor/pengumpulan) --}}
{{-- Primary job of the screen: Review queue for mentors to track submitted ZIP tasks and initiate manual grading. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper, tint --}}
{{-- Type roles: Schibsted Grotesk for headings and names; tabular-nums for scores, dates, and attempt numbers; JetBrains Mono for usernames --}}
{{-- Layout idea: Status summary metrics at top, followed by multi-criteria filter bar and submissions table --}}
{{-- What I changed after the "would any app have this?" check: Avoided colourful kanban boards or complex CRM cards; maintained strict teacher assessment ledger format. --}}

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-ink">
            {{ __('submissions.title') }}
        </h1>
        <p class="text-xs text-ink-muted">
            Pemeriksaan dan Penilaian Proyek Siswa — SMK Telkom Purwokerto
        </p>
    </div>

    {{-- Status Counts Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-sheet border border-rule border-l-[3px] border-l-brand rounded-panel p-4 flex items-center justify-between">
            <div>
                <span class="text-xs text-ink-muted block">{{ __('submissions.waiting_review') }}</span>
                <span class="text-2xl font-bold text-brand-deep tabular-nums">{{ $pendingCount }}</span>
            </div>
            <span class="text-xs font-medium text-brand-deep bg-tint px-2.5 py-1 rounded-cell">Perlu Dinilai</span>
        </div>

        <div class="bg-sheet border border-rule rounded-panel p-4 flex items-center justify-between">
            <div>
                <span class="text-xs text-ink-muted block">{{ __('submissions.status_graded') }}</span>
                <span class="text-2xl font-bold text-pass tabular-nums">{{ $gradedCount }}</span>
            </div>
            <span class="text-xs font-medium text-pass bg-pass/10 px-2.5 py-1 rounded-cell">Selesai</span>
        </div>

        <div class="bg-sheet border border-rule rounded-panel p-4 flex items-center justify-between">
            <div>
                <span class="text-xs text-ink-muted block">Total Pengumpulan</span>
                <span class="text-2xl font-bold text-ink tabular-nums">{{ $totalCount }}</span>
            </div>
            <span class="text-xs text-ink-muted">Arsip</span>
        </div>
    </div>

    {{-- Filters --}}
    <x-panel class="flex flex-wrap items-center gap-4 py-3">
        <div class="flex items-center gap-2">
            <label for="status" class="text-xs font-medium text-ink-muted">Status:</label>
            <select
                id="status"
                wire:model.live="status"
                class="rounded-cell border border-rule px-2.5 py-1 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
            >
                <option value="all">Semua Status</option>
                <option value="received">{{ __('submissions.status_received') }}</option>
                <option value="graded">{{ __('submissions.status_graded') }}</option>
                <option value="rejected">{{ __('submissions.status_rejected') }}</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label for="module_id" class="text-xs font-medium text-ink-muted">Modul:</label>
            <select
                id="module_id"
                wire:model.live="module_id"
                class="rounded-cell border border-rule px-2.5 py-1 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
            >
                <option value="all">Semua Modul</option>
                @foreach($modules as $m)
                    <option value="{{ $m->id }}">{{ $m->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label for="cohort_id" class="text-xs font-medium text-ink-muted">Kelas:</label>
            <select
                id="cohort_id"
                wire:model.live="cohort_id"
                class="rounded-cell border border-rule px-2.5 py-1 text-xs bg-sheet text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
            >
                <option value="all">Semua Kelas</option>
                @foreach($cohorts as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </x-panel>

    {{-- Submissions Table --}}
    <x-table>
        <thead class="bg-paper border-b border-rule">
            <tr>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted">
                    {{ __('submissions.student') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted">
                    {{ __('submissions.module') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-center w-20">
                    Percobaan
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted">
                    {{ __('submissions.submitted_at') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-center">
                    Status
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-right">
                    {{ __('submissions.score') }}
                </th>
                <th scope="col" class="py-3 px-4 text-xs font-semibold text-ink-muted text-right">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rule">
            @forelse($submissions as $sub)
                <tr class="hover:bg-paper/50">
                    <td class="py-3 px-4">
                        <span class="font-medium text-ink block">{{ $sub->user->name }}</span>
                        <span class="text-xs text-ink-muted font-mono">{{ $sub->user->username }} • {{ $sub->user->cohorts->pluck('name')->join(', ') ?: '—' }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-ink font-medium block">{{ $sub->module->title }}</span>
                        <span class="text-xs text-ink-muted uppercase">{{ $sub->module->track->label() }}</span>
                    </td>
                    <td class="py-3 px-4 text-center tabular-nums text-xs text-ink-muted">
                        #{{ $sub->attempt_no }}
                    </td>
                    <td class="py-3 px-4 text-xs text-ink-muted tabular-nums">
                        {{ $sub->created_at->translatedFormat('d M Y, H:i') }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($sub->status === \App\Enums\SubmissionStatus::Received)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium bg-tint text-brand-deep">
                                {{ __('submissions.waiting_review') }}
                            </span>
                        @elseif($sub->status === \App\Enums\SubmissionStatus::Graded)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium bg-pass/10 text-pass">
                                {{ __('submissions.status_graded') }}
                            </span>
                        @elseif($sub->status === \App\Enums\SubmissionStatus::Rejected)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium bg-paper text-ink-muted border border-rule">
                                {{ __('submissions.status_rejected') }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right tabular-nums font-bold text-ink">
                        {{ $sub->manual_score !== null ? $sub->manual_score : '—' }}
                    </td>
                    <td class="py-3 px-4 text-right">
                        <a
                            href="{{ route('mentor.submission.review', $sub->id) }}"
                            class="text-xs font-medium text-brand-deep hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        >
                            {{ $sub->status === \App\Enums\SubmissionStatus::Received ? 'Beri Nilai' : 'Detail' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-sm text-ink-muted">
                        {{ __('submissions.empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
</div>
