<?php

namespace App\Filament\Resources\Modules\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Resources\Modules\ModuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateModule extends CreateRecord
{
    use RecordsAuditTrail;

    protected static string $resource = ModuleResource::class;

    protected function afterCreate(): void
    {
        $this->audit('created');
    }
}
