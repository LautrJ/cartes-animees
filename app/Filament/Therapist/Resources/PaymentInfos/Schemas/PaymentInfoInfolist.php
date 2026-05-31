<?php

namespace App\Filament\Therapist\Resources\PaymentInfos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.therapist.payment_infos.infolist.section_bank'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bank_name')
                            ->label(__('filament.therapist.payment_infos.infolist.bank_name')),
                        TextEntry::make('bic')
                            ->label(__('filament.therapist.payment_infos.infolist.bic')),
                        TextEntry::make('iban')
                            ->label(__('filament.therapist.payment_infos.infolist.iban'))
                            ->getStateUsing(fn ($record) => '•••• •••• •••• '.substr($record->iban, -4))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.therapist.payment_infos.infolist.section_dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.therapist.payment_infos.infolist.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.therapist.payment_infos.infolist.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
