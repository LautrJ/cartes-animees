<?php

namespace App\Filament\Resources\TherapistPayouts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TherapistPayoutInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Orthophoniste')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('therapist.first_name')
                            ->label('Orthophoniste')
                            ->getStateUsing(fn ($record) => "{$record->therapist->first_name} {$record->therapist->last_name}"),
                        TextEntry::make('processedBy.first_name')
                            ->label('Traité par')
                            ->getStateUsing(fn ($record) => "{$record->processedBy->first_name} {$record->processedBy->last_name}"),
                    ]),

                Section::make('Détails du virement')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Montant')
                            ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                        TextEntry::make('patient_count')
                            ->label('Nombre de patients'),
                        TextEntry::make('period_start')
                            ->label('Période')
                            ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y')),
                        TextEntry::make('note')
                            ->label('Note')
                            ->placeholder('Aucune note')
                            ->columnSpanFull(),
                    ]),

                Section::make('Statut du paiement')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('paid_at')
                            ->label('Payé')
                            ->boolean()
                            ->getStateUsing(fn ($record) => ! is_null($record->paid_at)),
                        TextEntry::make('paid_at')
                            ->label('Payé le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('En attente'),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Mis à jour le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
