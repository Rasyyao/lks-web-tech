<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $this->audit('created');
    }
}
