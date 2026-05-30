<?php

namespace App\Filament\Resources\ContentValidations\Schemas;

use App\Enums\ContentValidationStatus;
use App\Models\Card;
use App\Models\Series;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentValidationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Demande de validation')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('validatable_type')
                            ->label('Type de contenu')
                            ->getStateUsing(fn($record) => match($record->validatable_type) {
                                Card::class   => 'Carte',
                                Series::class => 'Série',
                                default       => $record->validatable_type,
                            }),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn(ContentValidationStatus $state) => match($state) {
                                ContentValidationStatus::Pending  => 'warning',
                                ContentValidationStatus::Approved => 'success',
                                ContentValidationStatus::Rejected => 'danger',
                            }),
                        TextEntry::make('submitter.first_name')
                            ->label('Soumis par')
                            ->getStateUsing(fn($record) => "{$record->submitter->first_name} {$record->submitter->last_name}"),
                        TextEntry::make('submitted_at')
                            ->label('Soumis le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('reviewer.first_name')
                            ->label('Traité par')
                            ->getStateUsing(fn($record) => $record->reviewer
                                ? "{$record->reviewer->first_name} {$record->reviewer->last_name}"
                                : '-'
                            ),
                        TextEntry::make('reviewed_at')
                            ->label('Traité le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('rejection_reason')
                            ->label('Motif de rejet')
                            ->placeholder('Aucun')
                            ->columnSpanFull(),
                    ]),

                Section::make('Détail de la carte')
                    ->columns(2)
                    ->visible(fn($record) => $record->validatable_type === Card::class)
                    ->schema([
                        TextEntry::make('validatable.name')
                            ->label('Nom')
                            ->getStateUsing(fn($record) => $record->validatable?->name['fr'] ?? '-'),
                        TextEntry::make('validatable.creator.first_name')
                            ->label('Créé par')
                            ->getStateUsing(fn($record) => $record->validatable?->creator
                                ? "{$record->validatable->creator->first_name} {$record->validatable->creator->last_name}"
                                : '-'
                            ),
                        TextEntry::make('validatable.drawn_animation_path')
                            ->label('Animation dessinée')
                            ->placeholder('-'),
                        TextEntry::make('validatable.real_animation_path')
                            ->label('Animation réelle')
                            ->placeholder('-'),
                        TextEntry::make('validatable.sound_path')
                            ->label('Son')
                            ->placeholder('-'),
                        TextEntry::make('validatable.duration')
                            ->label('Durée')
                            ->suffix(' sec')
                            ->placeholder('-'),
                    ]),

                Section::make('Détail de la série')
                    ->columns(2)
                    ->visible(fn($record) => $record->validatable_type === Series::class)
                    ->schema([
                        TextEntry::make('validatable.name')
                            ->label('Nom')
                            ->getStateUsing(fn($record) => $record->validatable?->name['fr'] ?? '-'),
                        TextEntry::make('validatable.creator.first_name')
                            ->label('Créé par')
                            ->getStateUsing(fn($record) => $record->validatable?->creator
                                ? "{$record->validatable->creator->first_name} {$record->validatable->creator->last_name}"
                                : '-'
                            ),
                        TextEntry::make('validatable.description')
                            ->label('Description')
                            ->getStateUsing(fn($record) => $record->validatable?->description['fr'] ?? '-')
                            ->columnSpanFull(),
                        IconEntry::make('validatable.is_base')
                            ->label('Série de base')
                            ->boolean(),
                        TextEntry::make('validatable.cards_count')
                            ->label('Nombre de cartes')
                            ->getStateUsing(fn($record) => $record->validatable?->cards()->count() ?? 0),
                    ]),
            ]);
    }
}
