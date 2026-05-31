<?php

namespace App\Filament\Therapist\Resources\PaymentInfos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.therapist.payment_infos.form.section_bank'))
                    ->schema([
                        TextInput::make('iban')
                            ->label(__('filament.therapist.payment_infos.form.iban'))
                            ->required()
                            ->maxLength(34),
                        TextInput::make('bic')
                            ->label(__('filament.therapist.payment_infos.form.bic'))
                            ->required()
                            ->maxLength(11),
                        TextInput::make('bank_name')
                            ->label(__('filament.therapist.payment_infos.form.bank_name'))
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }
}
