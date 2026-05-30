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
                Section::make('Paramètre')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('Libellé')
                            ->disabled(),
                        TextInput::make('key')
                            ->label('Clé')
                            ->disabled(),
                        Select::make('type')
                            ->label('Type')
                            ->options(SettingType::class)
                            ->disabled(),
                        TextInput::make('value')
                            ->label('Valeur')
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
