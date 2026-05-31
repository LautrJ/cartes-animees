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
                Section::make(__('filament.therapist.therapist_payouts.infolist.section_details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('amount')
                            ->label(__('filament.therapist.therapist_payouts.infolist.amount'))
                            ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                        TextEntry::make('commission_rate')
                            ->label(__('filament.therapist.therapist_payouts.infolist.commission_rate'))
                            ->getStateUsing(fn ($record) => $record->commission_rate.__('filament.commission_rate_histories.rate_suffix')),
                        TextEntry::make('patient_count')
                            ->label(__('filament.therapist.therapist_payouts.infolist.patient_count')),
                        TextEntry::make('period_start')
                            ->label(__('filament.therapist.therapist_payouts.infolist.period'))
                            ->getStateUsing(fn ($record) => $record->period_start->format('d/m/Y').' → '.$record->period_end->format('d/m/Y')),
                        TextEntry::make('note')
                            ->label(__('filament.therapist.therapist_payouts.infolist.note'))
                            ->placeholder(__('filament.therapist.therapist_payouts.infolist.no_note'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.therapist.therapist_payouts.infolist.section_status'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('paid_at')
                            ->label(__('filament.therapist.therapist_payouts.infolist.paid'))
                            ->boolean()
                            ->getStateUsing(fn ($record) => ! is_null($record->paid_at)),
                        TextEntry::make('paid_at')
                            ->label(__('filament.therapist.therapist_payouts.infolist.paid_at'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder(__('filament.therapist.therapist_payouts.infolist.pending')),
                    ]),
            ]);
    }
}
