<?php

namespace App\Filament\Therapist\Resources\Series\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
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

                Section::make(__('filament.series.form.sections.cards'))
                    ->schema([
                        Hidden::make('cards_data')
                            ->default('[]'),
                        ViewField::make('card_picker')
                            ->view('livewire.series-card-picker-wrapper')
                            ->viewData([
                                'seriesId' => $schema->getRecord()?->id,
                            ]),
                    ]),
            ]);
    }
}
