<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Enums\SettingType;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Paramètre')
                    ->searchable(),
                TextColumn::make('value')
                    ->label('Valeur'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (SettingType $state) => match ($state) {
                        SettingType::String => 'gray',
                        SettingType::Integer => 'info',
                        SettingType::Float => 'warning',
                        SettingType::Boolean => 'success',
                    }),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->placeholder('-'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
            ]);
    }
}
