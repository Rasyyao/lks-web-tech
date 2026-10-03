<?php

namespace App\Filament\Concerns;

use Filament\Support\Enums\Width;

/**
 * Create/edit pages render every card (section) on its own row, so the form
 * is capped at a comfortable reading width instead of stretching edge to edge.
 */
trait StacksFormSections
{
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::FiveExtraLarge;
    }
}
