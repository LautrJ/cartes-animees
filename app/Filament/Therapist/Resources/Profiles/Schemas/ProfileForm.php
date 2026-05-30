<?php

namespace App\Filament\Therapist\Resources\Profiles\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Livewire\Component;

class ProfileForm
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
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(20),
                    ]),

                Section::make('Sécurité')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nouveau mot de passe')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed()
                            ->minLength(8),
                        TextInput::make('password_confirmation')
                            ->label('Confirmation')
                            ->password()
                            ->dehydrated(false),
                    ]),

                Section::make('Code d\'invitation')
                    ->schema([
                        TextInput::make('invitation_code')
                            ->label('Code d\'invitation')
                            ->disabled()
                            ->suffixAction(
                                Action::make('regenerate')
                                    ->label('Régénérer')
                                    ->icon(Heroicon::OutlinedArrowPath)
                                    ->requiresConfirmation()
                                    ->modalHeading('Régénérer le code ?')
                                    ->modalDescription('L\'ancien code ne fonctionnera plus.')
                                    ->action(function ($record, Component $livewire) {
                                        $code = Str::upper(Str::random(8));
                                        $record->update(['invitation_code' => $code]);

                                        $livewire->refreshFormData(['invitation_code']);

                                        Notification::make()
                                            ->title('Code régénéré')
                                            ->success()
                                            ->send();
                                    })
                            ),
                    ]),
            ]);
    }
}
