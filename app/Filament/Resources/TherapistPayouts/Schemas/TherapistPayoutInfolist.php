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
                Section::make(__('filament.therapist_payouts.infolist.sections.therapist'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('therapist.first_name')
                            ->label(__('filament.therapist_payouts.infolist.fields.therapist'))
                            ->getStateUsing(fn ($record) => "{$record->therapist->first_name} {$record->therapist->last_name}"),
                        TextEntry::make('processedBy.first_name')
                            ->label(__('filament.therapist_payouts.infolist.fields.processed_by'))
                            ->getStateUsing(fn ($record) => "{$record->processedBy->first_name} {$record->processedBy->last_name}"),
                    ]),

                Section::make(__('filament.therapist_payouts.infolist.sections.payout_details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label(__('filament.therapist_payouts.infolist.fields.amount'))
                            ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                        TextEntry::make('patient_count')
                            ->label(__('filament.therapist_payouts.infolist.fields.patient_count')),
                        TextEntry::make('period_start')
                            ->label(__('filament.therapist_payouts.infolist.fields.period'))
                            ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y')),
                        TextEntry::make('note')
                            ->label(__('filament.therapist_payouts.infolist.fields.note'))
                            ->placeholder(__('filament.therapist_payouts.infolist.fields.note_placeholder'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.therapist_payouts.infolist.sections.payment_status'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('paid_at')
                            ->label(__('filament.therapist_payouts.infolist.fields.paid'))
                            ->boolean()
                            ->getStateUsing(fn ($record) => ! is_null($record->paid_at)),
                        TextEntry::make('paid_at')
                            ->label(__('filament.therapist_payouts.infolist.fields.paid_at'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder(__('filament.therapist_payouts.infolist.fields.paid_at_placeholder')),
                    ]),

                Section::make(__('filament.therapist_payouts.infolist.sections.dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.therapist_payouts.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.therapist_payouts.infolist.fields.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
