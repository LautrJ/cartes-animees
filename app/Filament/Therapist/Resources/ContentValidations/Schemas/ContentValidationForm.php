<?php

namespace App\Filament\Therapist\Resources\ContentValidations\Schemas;

use App\Enums\ContentValidationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContentValidationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('validatable_type')
                    ->required(),
                TextInput::make('validatable_id')
                    ->required()
                    ->numeric(),
                TextInput::make('submitted_by')
                    ->required()
                    ->numeric(),
                TextInput::make('reviewed_by')
                    ->numeric(),
                Select::make('status')
                    ->options(ContentValidationStatus::class)
                    ->default('pending')
                    ->required(),
                Textarea::make('rejection_reason')
                    ->columnSpanFull(),
                DateTimePicker::make('submitted_at')
                    ->required(),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
