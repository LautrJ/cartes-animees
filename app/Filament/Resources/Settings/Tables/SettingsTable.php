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
                    ->label(__('filament.settings.table.column_label'))
                    ->searchable(),
                TextColumn::make('value')
                    ->label(__('filament.settings.table.value')),
                TextColumn::make('type')
                    ->label(__('filament.settings.table.type'))
                    ->badge()
                    ->color(fn (SettingType $state) => match ($state) {
                        SettingType::String => 'gray',
                        SettingType::Integer => 'info',
                        SettingType::Float => 'warning',
                        SettingType::Boolean => 'success',
                    }),
                TextColumn::make('description')
                    ->label(__('filament.settings.table.description'))
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
