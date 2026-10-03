{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Topic Material Detail (/materi/{slug}) --}}
{{-- Primary job of the screen: Deep learning material on a specific topic with direct practice shortcut. --}}
{{-- Palette used: sheet, rule, ink, ink-muted, brand, brand-deep, paper --}}
{{-- Type roles: Schibsted Grotesk for prose; JetBrains Mono for code blocks; tabular-nums for levels --}}
{{-- Layout idea: Header with breadcrumb and practice button, followed by structured markdown material sections --}}
{{-- What I changed after the "would any app have this?" check: Removed distracting social shares and reaction buttons; kept strict academic textbook clarity. --}}

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('materials.index', ['jalur' => ($topic->track?->value ?? $topic->track) === 'server' ? 'server' : 'client']) }}" class="text-xs text-ink-muted hover:text-brand-deep inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep">
            ← Kembali ke Roadmap {{ ($topic->track?->value ?? $topic->track) === 'server' ? 'Modul Server-Side' : 'Modul Client-Side' }}
        </a>

        <x-button size="sm" :href="route('practice.start', ['topic' => $topic->id])">
            Latih Topik Ini
        </x-button>
    </div>

    <header class="space-y-2 border-b border-rule pb-4">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center text-[10px] font-mono px-2 py-0.5 rounded-cell font-medium {{ ($topic->track?->value ?? $topic->track) === 'server' ? 'bg-brand/10 text-brand-deep' : 'bg-tint text-brand-deep' }}">
                {{ ($topic->track?->value ?? $topic->track) === 'server' ? 'Modul Server-Side' : 'Modul Client-Side' }}
            </span>
            @if($topic->position)
                <span class="text-xs text-ink-muted font-mono">Tahap {{ sprintf('%02d', $topic->position) }}</span>
            @endif
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-ink">
            {{ $topic->name }}
        </h1>
        @if($topic->description)
            <p class="text-sm text-ink-muted">
                {{ $topic->description }}
            </p>
        @else
            <p class="text-xs text-ink-muted">
                Materi Pembelajaran dan Referensi Teknis LKS Web Technologies
            </p>
        @endif
    </header>

    @if($topic->materials->isEmpty())
        <x-panel class="text-center py-8 text-sm text-ink-muted">
            Belum ada rangkuman materi tertulis untuk topik ini. Kamu tetap dapat berlatih melalui bank soal.
            <div class="mt-4">
                <x-button :href="route('practice.start', ['topic' => $topic->id])" size="sm">
                    Mulai Latihan Soal
                </x-button>
            </div>
        </x-panel>
    @else
        <div class="space-y-6">
            @foreach($topic->materials as $mat)
                <x-panel class="space-y-4">
                    <div class="flex items-center justify-between border-b border-rule pb-3">
                        <h2 class="text-lg font-bold text-ink">
                            {{ $mat->title }}
                        </h2>
                        <span class="text-xs font-mono text-ink-muted tabular-nums">
                            Tingkat {{ $mat->level }}
                        </span>
                    </div>

                    <div class="prose max-w-[70ch] text-sm text-ink">
                        {!! Str::markdown($mat->body_md) !!}
                    </div>
                </x-panel>
            @endforeach
        </div>

        <div class="pt-4 border-t border-rule flex items-center justify-between">
            <span class="text-xs text-ink-muted">
                Siap menguji pemahaman materi ini?
            </span>
            <x-button :href="route('practice.start', ['topic' => $topic->id])">
                Mulai Latihan Topik Ini
            </x-button>
        </div>
    @endif
</div>
