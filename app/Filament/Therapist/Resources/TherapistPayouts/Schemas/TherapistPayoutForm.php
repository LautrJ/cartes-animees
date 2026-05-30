<?php

namespace App\Filament\Therapist\Resources\TherapistPayouts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TherapistPayoutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('therapist_id')
                    ->relationship('therapist', 'id')
                    ->required(),
                TextInput::make('processed_by')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('commission_rate')
                    ->required()
                    ->numeric(),
                TextInput::make('patient_count')
                    ->required()
                    ->numeric(),
                DatePicker::make('period_start')
                    ->required(),
                DatePicker::make('period_end')
                    ->required(),
                Textarea::make('note')
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
