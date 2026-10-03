<?php

namespace App\Filament\Resources\Modules\RelationManagers;

use App\Models\ModuleAsset;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $title = 'Aset unduhan (ZIP, SQL, Postman, dll.)';

    protected static ?string $modelLabel = 'aset';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')
                ->label('Berkas')
                ->disk('private')
                ->directory('assets')
                ->visibility('private')
                ->maxSize(10240)
                ->required()
                // The original file name becomes the download name students see.
                ->storeFileNamesIn('label')
                ->helperText('Maks. 10 MB per berkas. Disimpan privat; siswa mengunduh lewat tautan bertanda tangan.'),
            TextInput::make('label')
                ->label('Nama berkas (tampil saat diunduh)')
                ->maxLength(255)
                ->helperText('Otomatis diisi dari nama berkas yang diunggah.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->label('Berkas')
                    ->searchable(),
                TextColumn::make('size')
                    ->label('Ukuran')
                    ->formatStateUsing(fn (int $state): string => $state >= 1048576
                        ? number_format($state / 1048576, 2).' MB'
                        : number_format($state / 1024, 1).' KB'),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah aset')
                    ->mutateDataUsing(function (array $data): array {
                        $data['label'] ??= basename((string) $data['path']);
                        $data['size'] = Storage::disk('private')->exists($data['path'])
                            ? Storage::disk('private')->size($data['path'])
                            : 0;

                        return $data;
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->after(fn (ModuleAsset $record) => Storage::disk('private')->delete($record->path)),
            ]);
    }
}
