<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengumuman')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Select::make('cohort_id')
                            ->label('Ditujukan untuk')
                            ->relationship('cohort', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Semua siswa')
                            ->helperText('Kosong = semua siswa melihat pengumuman ini.'),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal terbit')
                            ->seconds(false)
                            ->default(now())
                            ->helperText('Kosongkan untuk menyimpan sebagai draf (tidak tampil ke siswa).'),
                        MarkdownEditor::make('body_md')
                            ->label('Isi (Markdown)'),
                    ]),
            ]);
    }
}
