{{-- Table wrapper component --}}
{{-- DESIGN_RULES: Plain table, real <th> with scope, numbers tabular-nums right-aligned --}}
<div class="overflow-x-auto border border-rule rounded-panel bg-sheet">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-ink']) }}>
        {{ $slot }}
    </table>
</div>
