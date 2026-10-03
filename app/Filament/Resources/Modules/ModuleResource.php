<?php

namespace App\Filament\Resources\Modules;

use App\Filament\Resources\Modules\Pages\CreateModule;
use App\Filament\Resources\Modules\Pages\EditModule;
use App\Filament\Resources\Modules\Pages\ListModules;
use App\Filament\Resources\Modules\RelationManagers\AssetsRelationManager;
use App\Filament\Resources\Modules\Schemas\ModuleForm;
use App\Filament\Resources\Modules\Tables\ModulesTable;
use App\Models\Module;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    protected static string|UnitEnum|null $navigationGroup = 'Modul Lomba';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'modul';

    protected static ?string $pluralModelLabel = 'Modul lomba';

    protected static ?string $navigationLabel = 'Modul lomba';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ModuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
            'create' => CreateModule::route('/create'),
            'edit' => EditModule::route('/{record}/edit'),
        ];
    }
}
