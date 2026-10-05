{{-- Sidebar navigation link --}}
@props(['active' => false, 'href', 'badge' => null])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'group flex items-center justify-between px-3.5 py-2.5 rounded-cell text-sm font-medium transition-all duration-150 focus-visible:outline-2 focus-visible:outline-brand-deep',
        'bg-tint text-brand-deep border-l-[3px] border-l-brand font-semibold shadow-xs' => $active,
        'text-ink-muted hover:text-ink hover:bg-paper border-l-[3px] border-l-transparent' => ! $active,
    ]) }}
>
    <div class="flex items-center gap-3 min-w-0">
        @if(isset($icon))
            <span class="shrink-0 transition-colors {{ $active ? 'text-brand-deep' : 'text-ink-muted group-hover:text-ink' }}">
                {{ $icon }}
            </span>
        @endif
        <span class="truncate">{{ $slot }}</span>
    </div>

    @if($badge)
        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded-cell shrink-0 {{ $active ? 'bg-brand/10 text-brand-deep font-bold' : 'bg-paper text-ink-muted border border-rule' }}">
            {{ $badge }}
        </span>
    @endif
</a>
