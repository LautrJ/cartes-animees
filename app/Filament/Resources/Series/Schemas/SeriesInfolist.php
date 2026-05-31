<?php

namespace App\Filament\Resources\Series\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeriesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.series.infolist.sections.general_info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name.fr')
                            ->label(__('filament.series.infolist.fields.name')),
                        TextEntry::make('creator.first_name')
                            ->label(__('filament.series.infolist.fields.creator'))
                            ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('description.fr')
                            ->label(__('filament.series.infolist.fields.description'))
                            ->placeholder(__('filament.series.infolist.fields.description_placeholder'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.series.infolist.sections.settings'))
                    ->columns(3)
                    ->schema([
                        IconEntry::make('is_base')
                            ->label(__('filament.series.infolist.fields.is_base'))
                            ->boolean(),
                        IconEntry::make('is_validated')
                            ->label(__('filament.series.infolist.fields.is_validated'))
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->label(__('filament.series.infolist.fields.is_active'))
                            ->boolean(),
                    ]),

                Section::make(__('filament.series.infolist.sections.dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.series.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.series.infolist.fields.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
