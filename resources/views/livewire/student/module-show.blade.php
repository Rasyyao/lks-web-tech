{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Module Detail (/modul/{slug}) --}}
{{-- Primary job of the screen: Provide full LKS module brief, rules, downloadable assets, and ZIP submission portal. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, pass, paper, tint --}}
{{-- Type roles: Schibsted Grotesk for text and headings; max-w-[70ch] reading column; JetBrains Mono for filenames and code --}}
{{-- Layout idea: Strict PRD content order: Title/Meta -> Brief -> Rules -> Assets -> Submissions -> Submit form --}}
{{-- What I changed after the "would any app have this?" check: No floating submit floating action bars or side ribbons; reading flow remains uninterrupted and mimics standard national competition instruction booklet. --}}

<div class="max-w-4xl mx-auto space-y-8">
    {{-- Back navigation --}}
    <div>
        <a href="{{ route('modules.index') }}" class="text-xs text-ink-muted hover:text-brand-deep inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
            ← {{ __('modules.list_title') }}
        </a>
    </div>

    {{-- 1. Title and meta --}}
    <header class="space-y-3 pb-6 border-b border-rule">
        <div class="flex flex-wrap items-center gap-2 text-xs text-ink-muted">
            <span class="font-bold text-brand-deep uppercase tracking-wider">{{ $module->track->label() }}</span>
            <span>•</span>
            <span class="tabular-nums">Tingkat {{ $module->level }}</span>
            <span>•</span>
            <span class="tabular-nums">Durasi {{ $module->duration_minutes }} menit</span>
            <span>•</span>
            <span class="tabular-nums">Maks. {{ $module->max_attempts_per_day }} kiriman / hari</span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-ink">
            {{ $module->title }}
        </h1>

        <p class="text-base text-ink-muted">
            {{ $module->summary }}
        </p>

        <div class="flex flex-wrap items-center gap-6 pt-2 text-xs text-ink-muted">
            <div>
                <span class="block">Waktu Dibuka:</span>
                <time class="font-medium text-ink tabular-nums">
                    {{ $module->opens_at?->translatedFormat('d F Y, H:i') ?? 'Terbuka' }}
                </time>
            </div>
            <div>
                <span class="block">Batas Pengumpulan (Tenggat):</span>
                <time class="font-medium text-ink tabular-nums">
                    {{ $module->closes_at?->translatedFormat('d F Y, H:i') ?? 'Tidak terbatas' }}
                </time>
            </div>
            <div>
                <span class="block">Status:</span>
                @if($isOpen)
                    <span class="font-medium text-pass">Menerima Pengumpulan</span>
                @else
                    <span class="font-medium text-brand-deep">Pengumpulan Ditutup</span>
                @endif
            </div>
        </div>
    </header>

    {{-- 2. Brief in reading column (about 70ch wide) --}}
    <section aria-labelledby="brief-heading" class="space-y-3">
        <h2 id="brief-heading" class="text-xl font-bold text-ink">
            {{ __('modules.brief') }}
        </h2>
        <x-panel class="prose max-w-[70ch]">
            {!! Str::markdown($module->brief_md) !!}
        </x-panel>
    </section>

    {{-- 3. Rules --}}
    @if($module->rules_md)
        <section aria-labelledby="rules-heading" class="space-y-3">
            <h2 id="rules-heading" class="text-xl font-bold text-ink">
                {{ __('modules.rules') }}
            </h2>
            <x-panel class="prose max-w-[70ch]">
                {!! Str::markdown($module->rules_md) !!}
            </x-panel>
        </section>
    @endif

    {{-- 4. Assets list --}}
    <section aria-labelledby="assets-heading" class="space-y-3">
        <h2 id="assets-heading" class="text-xl font-bold text-ink">
            {{ __('modules.assets') }}
        </h2>

        @if($module->assets->isEmpty())
            <x-panel class="text-sm text-ink-muted py-4">
                Tidak ada berkas pendukung untuk modul ini.
            </x-panel>
        @else
            <x-panel :padding="false">
                <ul class="divide-y divide-rule" role="list">
                    @foreach($module->assets as $asset)
                        <li class="p-4 flex items-center justify-between gap-4">
                            <div class="space-y-0.5">
                                <span class="text-sm font-medium text-ink block font-mono">
                                    {{ $asset->label }}
                                </span>
                                <span class="text-xs text-ink-muted tabular-nums">
                                    {{ number_format($asset->size / 1024, 1) }} KB
                                </span>
                            </div>
                            <x-button
                                variant="secondary"
                                size="sm"
                                :href="URL::temporarySignedRoute('download.asset', now()->addMinutes(60), ['asset' => $asset->id])"
                            >
                                {{ __('modules.download_asset') }}
                            </x-button>
                        </li>
                    @endforeach
                </ul>
            </x-panel>
        @endif
    </section>

    {{-- 5. Submissions history --}}
    <section aria-labelledby="submissions-heading" class="space-y-3">
        <h2 id="submissions-heading" class="text-xl font-bold text-ink">
            {{ __('modules.submissions') }}
        </h2>

        @if($submissions->isEmpty())
            <x-panel class="text-sm text-ink-muted py-6 text-center">
                {{ __('modules.no_submissions') }}
            </x-panel>
        @else
            <div class="space-y-3">
                @foreach($submissions as $sub)
                    <x-panel class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-rule pb-3">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-ink">
                                        Pengumpulan #{{ $sub->attempt_no }}
                                    </span>
                                    <span class="text-xs font-mono text-ink-muted">
                                        ({{ $sub->original_name }})
                                    </span>
                                </div>
                                <time class="text-xs text-ink-muted tabular-nums">
                                    {{ $sub->created_at->translatedFormat('d M Y, H:i') }}
                                </time>
                            </div>

                            <div class="flex items-center gap-4">
                                <div>
                                    @if($sub->status === \App\Enums\SubmissionStatus::Received)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium bg-paper border border-rule text-ink">
                                            Diterima
                                        </span>
                                    @elseif($sub->status === \App\Enums\SubmissionStatus::Graded)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium bg-pass/10 text-pass">
                                            Sudah dinilai: <strong class="ml-1 tabular-nums">{{ $sub->manual_score }}/100</strong>
                                        </span>
                                    @elseif($sub->status === \App\Enums\SubmissionStatus::Rejected)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-cell text-xs font-medium bg-tint text-brand-deep">
                                            Ditolak
                                        </span>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('download.submission', $sub->id) }}"
                                    class="text-xs text-ink-muted hover:text-brand-deep underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                                >
                                    Unduh arsip
                                </a>
                            </div>
                        </div>

                        {{-- Reviewer feedback if graded --}}
                        @if($sub->feedback_md)
                            <div class="bg-paper p-3 rounded-cell space-y-1">
                                <span class="text-xs font-semibold text-ink-muted block">Catatan Mentor:</span>
                                <div class="text-xs text-ink prose max-w-none">
                                    {!! Str::markdown($sub->feedback_md) !!}
                                </div>
                            </div>
                        @endif
                    </x-panel>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 6. Submit form (only when open) --}}
    <section aria-labelledby="submit-heading" class="space-y-3">
        <h2 id="submit-heading" class="text-xl font-bold text-ink">
            Formulir Pengumpulan Tugas
        </h2>

        @if(! $isOpen)
            <x-panel class="bg-paper border border-rule text-sm text-ink-muted py-4">
                {{ __('modules.not_accepting') }}
            </x-panel>
        @elseif($remainingAttempts <= 0)
            <x-panel class="bg-tint border border-brand-deep/20 text-sm text-brand-deep py-4">
                {{ __('modules.daily_limit_reached', ['limit' => $module->max_attempts_per_day]) }}
            </x-panel>
        @else
            <x-panel class="space-y-4">
                <div class="flex items-center justify-between text-xs text-ink-muted pb-2 border-b border-rule">
                    <span>Sisa kuota kirim hari ini: <strong class="text-ink tabular-nums">{{ $remainingAttempts }} kali</strong></span>
                    <span>Batas ukuran: <strong class="text-ink tabular-nums">Maks. 20 MB (.zip)</strong></span>
                </div>

                <form wire:submit="submitZip" class="space-y-4">
                    <div class="space-y-1">
                        <label for="zipFile" class="block text-sm font-medium text-ink">
                            Pilih Berkas Proyek (.zip)
                            <span class="text-brand-deep" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="file"
                            id="zipFile"
                            wire:model="zipFile"
                            accept=".zip"
                            class="block w-full text-sm text-ink-muted file:mr-4 file:py-2 file:px-4 file:rounded-cell file:border file:border-rule file:text-xs file:font-medium file:bg-paper file:text-ink hover:file:bg-sheet focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep"
                        >
                        <p class="text-xs text-ink-muted">
                            Pastikan berkas berformat .zip dan seluruh file proyek telah dimasukkan ke dalam arsip.
                        </p>
                        @error('zipFile')
                            <p class="text-xs text-brand-deep mt-1" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Upload loading indicator --}}
                    <div wire:loading wire:target="zipFile" class="text-xs text-ink-muted">
                        Mengunggah berkas ke peramban...
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <x-button type="submit" wire:loading.attr="disabled" wire:target="submitZip">
                            <span wire:loading.remove wire:target="submitZip">{{ __('modules.submit_button') }}</span>
                            <span wire:loading wire:target="submitZip">Menyimpan berkas...</span>
                        </x-button>
                    </div>
                </form>
            </x-panel>
        @endif
    </section>
</div>
