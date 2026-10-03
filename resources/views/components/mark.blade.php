{{-- Answer mark component: SVG ✓ or ✗ with word --}}
{{-- DESIGN_RULES section 5: drawn mark (SVG, not emoji) AND a word ("Benar", "Salah") --}}
@props(['correct'])

@if($correct)
    <span class="inline-flex items-center gap-1.5 text-pass font-medium">
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
        </svg>
        <span>{{ __('practice.correct') }}</span>
    </span>
@else
    <span class="inline-flex items-center gap-1.5 text-brand-deep font-medium">
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
        </svg>
        <span>{{ __('practice.wrong') }}</span>
    </span>
@endif
