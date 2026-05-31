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
                Section::make(__('filament.therapist.series.form.section_general'))
                    ->schema([
                        TextInput::make('name.fr')
                            ->label(__('filament.therapist.series.form.name'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description.fr')
                            ->label(__('filament.therapist.series.form.description'))
                            ->rows(3),
                    ]),

                Section::make(__('filament.therapist.series.form.section_media'))
                    ->schema([
                        FileUpload::make('thumbnail_path')
                            ->label(__('filament.therapist.series.form.thumbnail'))
                            ->image()
                            ->nullable(),
                    ]),

                Section::make(__('filament.therapist.series.form.section_cards'))
                    ->schema([
                        Select::make('cards')
                            ->label(__('filament.therapist.series.form.cards_to_include'))
                            ->relationship('cards', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['fr'] ?? '-')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }
}
