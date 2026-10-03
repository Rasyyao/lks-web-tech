{{-- Button component --}}
{{-- Variants: primary (brand), secondary (outline ink), danger (brand-deep) --}}
@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'size' => 'base',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-cell transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep';

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-sm',
        'base' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    };

    $variantClasses = match($variant) {
        'primary' => 'bg-brand text-sheet hover:bg-brand-deep',
        'secondary' => 'bg-sheet text-ink border border-rule hover:border-ink-muted',
        'danger' => 'bg-brand-deep text-sheet hover:bg-brand',
        'ghost' => 'text-ink-muted hover:text-ink hover:bg-paper',
        default => 'bg-brand text-sheet hover:bg-brand-deep',
    };

    $classes = "$baseClasses $sizeClasses $variantClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
