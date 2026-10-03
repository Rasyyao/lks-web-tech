{{-- Panel component: white background, rule border, rounded-panel --}}
@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'bg-sheet border border-rule rounded-panel' . ($padding ? ' p-4 sm:p-6' : '')]) }}>
    {{ $slot }}
</div>
