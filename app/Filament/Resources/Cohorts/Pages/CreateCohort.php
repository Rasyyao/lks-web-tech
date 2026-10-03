<?php

namespace App\Filament\Resources\Cohorts\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Resources\Cohorts\CohortResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCohort extends CreateRecord
{
    use RecordsAuditTrail;

    protected static string $resource = CohortResource::class;

    protected function afterCreate(): void
    {
        $this->audit('created');
    }
}
