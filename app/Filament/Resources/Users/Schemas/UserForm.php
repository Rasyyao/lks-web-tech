<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->description('Data login siswa, mentor, atau admin.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->label('Username / NIS')
                            ->required()
                            ->alphaDash()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Kata sandi')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->maxLength(255)
                            ->autocomplete('new-password')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            // Blank on edit = keep the current password. The model's
                            // `hashed` cast hashes the value on save.
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (string $operation): ?string => $operation === 'edit'
                                ? 'Kosongkan jika tidak ingin mengubah kata sandi.'
                                : 'Minimal 8 karakter.'),
                    ]),

                Section::make('Peran & kelompok')
                    ->schema([
                        Select::make('roles')
                            ->label('Peran')
                            ->relationship('roles', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => ucfirst($record->name))
                            ->multiple()
                            ->maxItems(1)
                            ->preload()
                            ->required()
                            ->helperText('Admin: akses panel ini. Mentor: review & soal. Student: latihan & modul.'),
                        Select::make('cohorts')
                            ->label('Kelompok (kelas / seleksi)')
                            ->relationship('cohorts', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Akun aktif')
                            ->default(true)
                            // An admin must not lock themselves out.
                            ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false)
                            ->helperText('Akun nonaktif tidak bisa masuk.'),
                        Toggle::make('must_change_password')
                            ->label('Wajib ganti kata sandi saat masuk')
                            ->default(true),
                    ]),
            ]);
    }
}
