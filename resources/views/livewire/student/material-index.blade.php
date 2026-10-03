{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Materi & Roadmap (/materi) --}}
{{-- Primary job of the screen: Provide learning path across web technologies curriculum with links to materials and practice sets. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, paper --}}
{{-- Type roles: Schibsted Grotesk for topic names and descriptions; tabular-nums for counts and levels --}}
{{-- Layout idea: Vertical sequence of curriculum topics with question counts and direct practice triggers --}}
{{-- What I changed after the "would any app have this?" check: No skill-tree video game nodes or unlock padlocks; clean, accessible syllabus structure. --}}

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-ink">
            {{ __('general.materials') }} & Silabus LKS
        </h1>
        <p class="text-xs text-ink-muted">
            Kurikulum Pembelajaran LKS Web Technologies SMK Telkom Purwokerto
        </p>
    </div>

    @if($topics->isEmpty())
        <x-panel class="text-center py-8 text-sm text-ink-muted">
            Belum ada topik materi yang ditambahkan.
        </x-panel>
    @else
        <div class="space-y-4">
            @foreach($topics as $index => $topic)
                <x-panel class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <span class="font-mono text-lg font-bold text-ink-muted tabular-nums w-8 pt-0.5">
                            {{ sprintf('%02d', $index + 1) }}
                        </span>

                        <div class="space-y-1">
                            <h2 class="text-lg font-bold text-ink hover:text-brand-deep">
                                <a href="{{ route('materials.show', $topic->slug) }}" class="focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
                                    {{ $topic->name }}
                                </a>
                            </h2>

                            <p class="text-xs text-ink-muted">
                                {{ $topic->materials->count() }} modul materi • {{ $topic->questions_count }} soal latihan tersedia
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pl-12 sm:pl-0">
                        <x-button variant="secondary" size="sm" :href="route('materials.show', $topic->slug)">
                            Baca Materi
                        </x-button>
                        <x-button size="sm" :href="route('practice.start', ['topic' => $topic->id])">
                            Latih Soal
                        </x-button>
                    </div>
                </x-panel>
            @endforeach
        </div>
    @endif
</div>
