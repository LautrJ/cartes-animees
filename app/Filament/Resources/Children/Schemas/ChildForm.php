<?php

namespace App\Filament\Resources\Children\Schemas;

use App\Models\User;
use App\Enums\UserRole;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChildForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations personnelles')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(100),
                        DatePicker::make('birthdate')
                            ->label('Date de naissance')
                            ->displayFormat('d/m/Y')
                            ->nullable(),
                        Select::make('parent_id')
                            ->label('Parent')
                            ->options(
                                User::where('role', UserRole::Parent)
                                    ->get()
                                    ->mapWithKeys(fn($u) => [$u->id => "{$u->first_name} {$u->last_name}"])
                            )
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->nullable(),
                    ]),
            ]);
    }
}
