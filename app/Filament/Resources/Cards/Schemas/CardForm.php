<?php

namespace App\Filament\Resources\Cards\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.cards.form.sections.general_info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name.fr')
                            ->label(__('filament.cards.form.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('duration')
                            ->label(__('filament.cards.form.fields.duration'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(30)
                            ->helperText(__('filament.cards.form.fields.duration_helper')),
                    ]),

                Section::make(__('filament.cards.form.sections.medias'))
                    ->columns(3)
                    ->schema([
                        FileUpload::make('drawn_animation_path')
                            ->label(__('filament.cards.form.fields.drawn_animation_path'))
                            ->disk('cards')
                            ->directory('drawn')
                            ->acceptedFileTypes(['image/gif', 'video/mp4'])
                            ->required(),
                        FileUpload::make('real_animation_path')
                            ->label(__('filament.cards.form.fields.real_animation_path'))
                            ->disk('cards')
                            ->directory('real')
                            ->acceptedFileTypes(['image/gif', 'video/mp4'])
                            ->required(),
                        FileUpload::make('sound_path')
                            ->label(__('filament.cards.form.fields.sound_path'))
                            ->disk('cards')
                            ->directory('sounds')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3'])
                            ->required(),
                    ]),

                Section::make(__('filament.cards.form.sections.status'))
                    ->schema([
                        Toggle::make('is_validated')
                            ->label(__('filament.cards.form.fields.is_validated'))
                            ->default(true),
                    ]),
            ]);
    }
}
