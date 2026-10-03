{{-- Desktop navigation link --}}
{{-- Active: 2px brand underline. Hover: text-ink. --}}
@props(['active' => false, 'href'])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'px-3 py-2 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep',
        'text-ink border-b-2 border-brand' => $active,
        'text-ink-muted hover:text-ink border-b-2 border-transparent' => ! $active,
    ]) }}
>
    {{ $slot }}
</a>
