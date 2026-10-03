<?php

namespace App\Filament\Resources\Cohorts;

use App\Filament\Resources\Cohorts\Pages\CreateCohort;
use App\Filament\Resources\Cohorts\Pages\EditCohort;
use App\Filament\Resources\Cohorts\Pages\ListCohorts;
use App\Filament\Resources\Cohorts\Schemas\CohortForm;
use App\Filament\Resources\Cohorts\Tables\CohortsTable;
use App\Models\Cohort;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CohortResource extends Resource
{
    protected static ?string $model = Cohort::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Pengguna';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'kelompok';

    protected static ?string $pluralModelLabel = 'Kelompok';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CohortForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CohortsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCohorts::route('/'),
            'create' => CreateCohort::route('/create'),
            'edit' => EditCohort::route('/{record}/edit'),
        ];
    }
}
