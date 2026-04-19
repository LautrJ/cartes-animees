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
                Section::make('Informations bancaires')
                    ->schema([
                        TextInput::make('iban')
                            ->label('IBAN')
                            ->required()
                            ->maxLength(34),
                        TextInput::make('bic')
                            ->label('BIC')
                            ->required()
                            ->maxLength(11),
                        TextInput::make('bank_name')
                            ->label('Nom de la banque')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }
}
