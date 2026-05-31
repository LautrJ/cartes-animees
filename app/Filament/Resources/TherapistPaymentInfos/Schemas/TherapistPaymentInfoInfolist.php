<?php

namespace App\Filament\Resources\TherapistPaymentInfos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TherapistPaymentInfoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.therapist_payment_infos.infolist.sections.therapist'))
                    ->schema([
                        TextEntry::make('user.first_name')
                            ->label(__('filament.therapist_payment_infos.infolist.fields.name'))
                            ->getStateUsing(fn ($record) => "{$record->user->first_name} {$record->user->last_name}"),
                        TextEntry::make('user.email')
                            ->label(__('filament.therapist_payment_infos.infolist.fields.email')),
                    ]),

                Section::make(__('filament.therapist_payment_infos.infolist.sections.bank_info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bank_name')
                            ->label(__('filament.therapist_payment_infos.infolist.fields.bank_name')),
                        TextEntry::make('bic')
                            ->label(__('filament.therapist_payment_infos.infolist.fields.bic')),
                        TextEntry::make('iban')
                            ->label(__('filament.therapist_payment_infos.infolist.fields.iban'))
                            ->getStateUsing(fn ($record) => '•••• •••• •••• '.substr($record->iban, -4))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.therapist_payment_infos.infolist.sections.dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.therapist_payment_infos.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.therapist_payment_infos.infolist.fields.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
