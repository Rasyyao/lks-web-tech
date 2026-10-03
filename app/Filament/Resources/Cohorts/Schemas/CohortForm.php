<?php

namespace App\Filament\Resources\Cohorts\Schemas;

use App\Enums\CohortType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CohortForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kelompok')
                    ->description('Kelas (mis. XII RPL 1) atau kelompok seleksi LKS.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Jenis')
                            ->options(CohortType::class)
                            ->default(CohortType::ClassGroup)
                            ->required()
                            ->native(false),
                        TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2100)
                            ->default(now()->year)
                            ->required(),
                    ]),

                Section::make('Anggota')
                    ->description('Siswa yang tergabung menentukan modul & pengumuman yang bisa mereka lihat.')
                    ->schema([
                        Select::make('users')
                            ->label('Anggota')
                            ->relationship('users', 'name')
                            ->multiple()
                            ->searchable(['name', 'username'])
                            ->preload(),
                    ]),
            ]);
    }
}
