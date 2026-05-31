<?php

namespace App\Filament\Therapist\Resources\Series\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeriesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.therapist.series.infolist.section_info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name.fr')
                            ->label(__('filament.therapist.series.infolist.name')),
                        TextEntry::make('creator.first_name')
                            ->label(__('filament.therapist.series.infolist.created_by'))
                            ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('description.fr')
                            ->label(__('filament.therapist.series.infolist.description'))
                            ->placeholder(__('filament.therapist.series.infolist.no_description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.therapist.series.infolist.section_settings'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_base')
                            ->label(__('filament.therapist.series.infolist.is_base'))
                            ->boolean(),
                        TextEntry::make('cards_count')
                            ->label(__('filament.therapist.series.infolist.cards_count'))
                            ->getStateUsing(fn ($record) => $record->cards()->count()),
                    ]),

                Section::make(__('filament.therapist.series.infolist.section_dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.therapist.series.infolist.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.therapist.series.infolist.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
