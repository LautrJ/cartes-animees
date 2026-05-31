<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Enums\SettingType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.settings.form.section_title'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label(__('filament.settings.form.label'))
                            ->disabled(),
                        TextInput::make('key')
                            ->label(__('filament.settings.form.key'))
                            ->disabled(),
                        Select::make('type')
                            ->label(__('filament.settings.form.type'))
                            ->options(SettingType::class)
                            ->disabled(),
                        TextInput::make('value')
                            ->label(__('filament.settings.form.value'))
                            ->required(),
                        Textarea::make('description')
                            ->label(__('filament.settings.form.description'))
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
