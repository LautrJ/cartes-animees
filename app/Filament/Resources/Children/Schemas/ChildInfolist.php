<?php

namespace App\Filament\Resources\Children\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.children.infolist.sections.personal_info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')
                            ->label(__('filament.children.infolist.fields.first_name')),
                        TextEntry::make('last_name')
                            ->label(__('filament.children.infolist.fields.last_name')),
                        TextEntry::make('birthdate')
                            ->label(__('filament.children.infolist.fields.birthdate'))
                            ->date('d/m/Y')
                            ->placeholder(__('filament.children.infolist.fields.birthdate_placeholder')),
                        TextEntry::make('parent.first_name')
                            ->label(__('filament.children.infolist.fields.parent'))
                            ->getStateUsing(fn ($record) => "{$record->parent->first_name} {$record->parent->last_name}"),
                    ]),

                Section::make(__('filament.children.infolist.sections.therapists'))
                    ->schema([
                        TextEntry::make('activeTherapists')
                            ->label(__('filament.children.infolist.fields.active_therapists'))
                            ->getStateUsing(fn ($record) => $record->activeTherapists
                                ->map(fn ($t) => "{$t->first_name} {$t->last_name}")
                                ->join(', ') ?: '-'
                            ),
                    ]),

                Section::make(__('filament.children.infolist.sections.notes'))
                    ->schema([
                        TextEntry::make('notes')
                            ->label(__('filament.children.infolist.fields.notes'))
                            ->placeholder(__('filament.children.infolist.fields.notes_placeholder'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.children.infolist.sections.dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.children.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.children.infolist.fields.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
