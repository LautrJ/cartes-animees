<?php

namespace App\Filament\Therapist\Resources\TherapistPayouts\Schemas;

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
                Section::make('Détails du virement')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Montant')
                            ->getStateUsing(fn($record) => number_format($record->amount, 2) . ' €'),
                        TextEntry::make('commission_rate')
                            ->label('Taux appliqué')
                            ->getStateUsing(fn($record) => $record->commission_rate . ' €/patient'),
                        TextEntry::make('patient_count')
                            ->label('Nombre de patients'),
                        TextEntry::make('period_start')
                            ->label('Période')
                            ->getStateUsing(fn($record) => $record->period_start->format('d/m/Y') . ' → ' . $record->period_end->format('d/m/Y')),
                        TextEntry::make('note')
                            ->label('Note')
                            ->placeholder('Aucune note')
                            ->columnSpanFull(),
                    ]),

                Section::make('Statut')
                    ->columns(2)
                    ->schema([
                        IconEntry::make('paid_at')
                            ->label('Payé')
                            ->boolean()
                            ->getStateUsing(fn($record) => !is_null($record->paid_at)),
                        TextEntry::make('paid_at')
                            ->label('Payé le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('En attente'),
                    ]),
            ]);
    }
}
