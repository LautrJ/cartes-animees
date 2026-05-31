<?php

namespace App\Filament\Therapist\Resources\Cards\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.therapist.cards.form.section_general'))
                    ->schema([
                        TextInput::make('name.fr')
                            ->label(__('filament.therapist.cards.form.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('duration')
                            ->label(__('filament.therapist.cards.form.duration'))
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(30)
                            ->helperText(__('filament.therapist.cards.form.duration_helper')),
                    ]),

                Section::make(__('filament.therapist.cards.form.section_media'))
                    ->columns(3)
                    ->schema([
                        FileUpload::make('drawn_animation_path')
                            ->label(__('filament.therapist.cards.form.drawn_animation_path'))
                            ->disk('cards')
                            ->directory('drawn')
                            ->acceptedFileTypes(['image/gif', 'video/mp4'])
                            ->required(),
                        FileUpload::make('real_animation_path')
                            ->label(__('filament.therapist.cards.form.real_animation_path'))
                            ->disk('cards')
                            ->directory('real')
                            ->acceptedFileTypes(['image/gif', 'video/mp4'])
                            ->required(),
                        FileUpload::make('sound_path')
                            ->label(__('filament.therapist.cards.form.sound_path'))
                            ->disk('cards')
                            ->directory('sounds')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3'])
                            ->required(),
                    ]),
            ]);
    }
}
