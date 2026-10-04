{{-- DESIGN PLAN RECORD --}}
{{-- Screen: Reference & Guide Page (/belajar/referensi/{slug}) --}}
<div class="py-6 sm:py-8">
    <div class="mx-auto max-w-7xl xl:max-w-[1400px] px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Breadcrumbs --}}
        <div class="flex items-center justify-between text-xs text-ink-muted">
            <nav class="flex items-center gap-1.5" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-ink">Beranda</a>
                <span>/</span>
                <a href="{{ route('roadmap.index') }}" class="hover:text-ink">Roadmap Client</a>
                <span>/</span>
                <span class="text-ink font-semibold">{{ $page->title }}</span>
            </nav>

            <a href="{{ route('roadmap.index') }}" class="inline-flex items-center gap-1 text-xs text-brand-deep hover:underline">
                ← Kembali ke Roadmap
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- Main Content --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-sheet border border-rule rounded-panel p-6 shadow-sm space-y-4">
                    <div class="border-b border-rule pb-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider bg-paper text-ink border border-rule mb-2">
                            {{ $page->kind === 'reference' ? 'Referensi Teknis' : 'Panduan Lomba' }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">
                            {{ $page->title }}
                        </h1>
                    </div>

                    @foreach($page->sections as $sec)
                        <div class="prose max-w-none text-ink text-sm sm:text-base leading-relaxed break-words space-y-3 pt-2">
                            {!! $sec->body_html !!}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Sidebar Reference Links --}}
            <div class="space-y-4">
                <div class="bg-sheet border border-rule rounded-panel p-4 shadow-sm sticky top-6 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ink-muted border-b border-rule pb-2">
                        Daftar Panduan & Referensi
                    </h3>
                    <nav class="space-y-1 text-xs">
                        @foreach($allReferences as $ref)
                            <a
                                href="{{ route('roadmap.reference', $ref->slug) }}"
                                class="block py-1.5 px-2 rounded transition-colors {{ $ref->slug === $page->slug ? 'bg-brand/10 text-brand-deep font-semibold' : 'text-ink hover:bg-paper' }}"
                            >
                                {{ $ref->title }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="pt-3 border-t border-rule text-center">
                        <a href="{{ route('roadmap.index') }}" class="inline-flex items-center justify-center w-full px-3 py-1.5 bg-paper text-ink text-xs font-medium rounded border border-rule hover:bg-sheet transition-colors">
                            Buka Tahapan Level →
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
