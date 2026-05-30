<?php

namespace App\Filament\Therapist\Resources\Series\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextInput::make('name.fr')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description.fr')
                            ->label('Description')
                            ->rows(3),
                    ]),

                Section::make('Médias')
                    ->schema([
                        FileUpload::make('thumbnail_path')
                            ->label('Miniature')
                            ->image()
                            ->nullable(),
                    ]),

                Section::make('Cartes')
                    ->schema([
                        Select::make('cards')
                            ->label('Cartes à inclure')
                            ->relationship('cards', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['fr'] ?? '-')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }
}
