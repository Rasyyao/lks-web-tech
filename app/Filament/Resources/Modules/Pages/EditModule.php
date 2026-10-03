<?php

namespace App\Filament\Resources\Modules\Pages;

use App\Filament\Concerns\RecordsAuditTrail;
use App\Filament\Concerns\StacksFormSections;
use App\Filament\Resources\Modules\ModuleResource;
use App\Models\Module;
use Filament\Resources\Pages\EditRecord;

class EditModule extends EditRecord
{
    use RecordsAuditTrail;
    use StacksFormSections;

    protected static string $resource = ModuleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Module $module */
        $module = $this->getRecord();

        // Bump the version when the brief or rules change so students can tell.
        if (
            (($data['brief_md'] ?? $module->brief_md) !== $module->brief_md)
            || (($data['rules_md'] ?? $module->rules_md) !== $module->rules_md)
        ) {
            $data['version'] = $module->version + 1;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->audit('updated');
    }
}
