<?php

namespace App\Filament\Resources\Cohorts\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Cohorts\CohortResource;
use Filament\Resources\Pages\EditRecord;

class EditCohort extends EditRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = CohortResource::class;

    protected function afterSave(): void
    {
        $this->audit('updated');
    }
}
