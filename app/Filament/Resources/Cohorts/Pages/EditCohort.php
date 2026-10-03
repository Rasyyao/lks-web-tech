<?php

namespace App\Filament\Resources\Cohorts\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Resources\Cohorts\CohortResource;
use Filament\Resources\Pages\EditRecord;

class EditCohort extends EditRecord
{
    use RecordsAuditTrail;

    protected static string $resource = CohortResource::class;

    protected function afterSave(): void
    {
        $this->audit('updated');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->auditedDeleteAction()
                ->modalDescription('Anggota tidak ikut terhapus, hanya keanggotaan kelompok ini. Modul & pengumuman yang terkait kelompok ini akan kehilangan penugasannya.'),
        ];
    }
}
