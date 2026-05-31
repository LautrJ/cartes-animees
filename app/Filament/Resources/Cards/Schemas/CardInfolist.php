<?php

namespace App\Filament\Resources\Cards\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CardInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.cards.infolist.sections.info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name.fr')
                            ->label(__('filament.cards.infolist.fields.name')),
                        TextEntry::make('creator.first_name')
                            ->label(__('filament.cards.infolist.fields.creator'))
                            ->getStateUsing(fn ($record) => "{$record->creator->first_name} {$record->creator->last_name}"),
                        TextEntry::make('duration')
                            ->label(__('filament.cards.infolist.fields.duration'))
                            ->suffix(' sec')
                            ->placeholder('-'),
                        TextEntry::make('series_count')
                            ->label(__('filament.cards.infolist.fields.series_count'))
                            ->getStateUsing(fn ($record) => $record->series()->count().' '.__('filament.cards.infolist.fields.series_count_suffix')),
                    ]),

                Section::make(__('filament.cards.infolist.sections.medias'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('drawn_animation_path')
                            ->label(__('filament.cards.infolist.fields.drawn_animation_path'))
                            ->html()
                            ->getStateUsing(fn ($record) => $record->drawn_animation_path
                                ? (str_ends_with($record->drawn_animation_path, '.gif')
                                    ? '<img src="'.asset('storage/cards/'.$record->drawn_animation_path).'" style="max-height:200px; border-radius:8px;">'
                                    : '<video controls style="max-height:200px; border-radius:8px; width:100%">
                                        <source src="'.asset('storage/cards/'.$record->drawn_animation_path).'" type="video/mp4">
                                       </video>')
                                : '<span class="text-gray-400">'.__('filament.cards.infolist.fields.no_file').'</span>'
                            ),

                        TextEntry::make('real_animation_path')
                            ->label(__('filament.cards.infolist.fields.real_animation_path'))
                            ->html()
                            ->getStateUsing(fn ($record) => $record->real_animation_path
                                ? (str_ends_with($record->real_animation_path, '.gif')
                                    ? '<img src="'.asset('storage/cards/'.$record->real_animation_path).'" style="max-height:200px; border-radius:8px;">'
                                    : '<video controls style="max-height:200px; border-radius:8px; width:100%">
                                        <source src="'.asset('storage/cards/'.$record->real_animation_path).'" type="video/mp4">
                                       </video>')
                                : '<span class="text-gray-400">'.__('filament.cards.infolist.fields.no_file').'</span>'
                            ),

                        TextEntry::make('sound_path')
                            ->label(__('filament.cards.infolist.fields.sound_path'))
                            ->html()
                            ->getStateUsing(fn ($record) => $record->sound_path
                                ? '<audio controls style="width:100%">
                                    <source src="'.asset('storage/cards/'.$record->sound_path).'" type="audio/mpeg">
                                   </audio>'
                                : '<span class="text-gray-400">'.__('filament.cards.infolist.fields.no_file').'</span>'
                            ),
                    ]),
            ]);
    }
}
