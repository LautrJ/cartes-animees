<?php

namespace App\Filament\Therapist\Resources\ContentValidations\Schemas;

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
            ->columns(1)
            ->components([
                Section::make('Demande')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('validatable_type')
                            ->label('Type')
                            ->getStateUsing(fn($record) => match($record->validatable_type) {
                                Card::class   => 'Carte',
                                Series::class => 'Série',
                                default       => '-',
                            }),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->color(fn(ContentValidationStatus $state) => match($state) {
                                ContentValidationStatus::Pending  => 'warning',
                                ContentValidationStatus::Approved => 'success',
                                ContentValidationStatus::Rejected => 'danger',
                            }),
                        TextEntry::make('validatable.name')
                            ->label('Contenu')
                            ->getStateUsing(fn($record) => $record->validatable?->name['fr'] ?? '-')
                            ->columnSpanFull(),
                        TextEntry::make('submitted_at')
                            ->label('Soumis le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('reviewed_at')
                            ->label('Traité le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('rejection_reason')
                            ->label('Motif de rejet')
                            ->placeholder('Aucun')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
