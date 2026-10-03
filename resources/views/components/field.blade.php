{{-- Form field component --}}
@props([
    'label',
    'id',
    'type' => 'text',
    'error' => null,
    'required' => false,
    'help' => null,
])

<div class="space-y-1">
    <label for="{{ $id }}" class="block text-sm font-medium text-ink">
        {{ $label }}
        @if($required)
            <span class="text-brand-deep" aria-hidden="true">*</span>
        @endif
    </label>

    @if($type === 'textarea')
        <textarea
            id="{{ $id }}"
            {{ $attributes->merge([
                'class' => 'w-full rounded-cell border border-rule px-3 py-2 text-sm text-ink bg-sheet placeholder:text-ink-muted focus:border-brand-deep focus:ring-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep',
            ]) }}
            @if($required) required @endif
        >{{ $slot }}</textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            {{ $attributes->merge([
                'class' => 'w-full rounded-cell border border-rule px-3 py-2 text-sm text-ink bg-sheet placeholder:text-ink-muted focus:border-brand-deep focus:ring-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-deep',
            ]) }}
            @if($required) required @endif
        >
    @endif

    @if($help)
        <p class="text-xs text-ink-muted">{{ $help }}</p>
    @endif

    @if($error)
        <p class="text-xs text-brand-deep" role="alert">{{ $error }}</p>
    @endif
</div>
