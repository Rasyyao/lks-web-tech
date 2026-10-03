<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\AuditLog;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('username')
                    ->label('Username / NIS')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'mentor' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('cohorts.name')
                    ->label('Kelompok')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('last_login_at')
                    ->label('Terakhir masuk')
                    ->since()
                    ->placeholder('Belum pernah')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('roles')
                    ->label('Peran')
                    ->relationship('roles', 'name')
                    ->preload(),
                SelectFilter::make('cohorts')
                    ->label('Kelompok')
                    ->relationship('cohorts', 'name')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Status akun')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('resetPassword')
                    ->label('Reset sandi')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->modalHeading(fn (User $record): string => "Reset kata sandi {$record->name}")
                    ->modalDescription('Pengguna akan diminta mengganti kata sandi ini saat masuk berikutnya.')
                    ->modalSubmitActionLabel('Reset')
                    ->schema([
                        TextInput::make('password')
                            ->label('Kata sandi sementara')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->maxLength(255),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'password' => $data['password'],
                            'must_change_password' => true,
                        ]);

                        AuditLog::record('user.password_reset', $record);

                        Notification::make()
                            ->title('Kata sandi direset')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
