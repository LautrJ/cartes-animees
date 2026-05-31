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
                Section::make(__('filament.therapist.profiles.form.section_personal'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('filament.therapist.profiles.form.first_name'))
                            ->required()
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label(__('filament.therapist.profiles.form.last_name'))
                            ->required()
                            ->maxLength(100),
                        TextInput::make('email')
                            ->label(__('filament.therapist.profiles.form.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label(__('filament.therapist.profiles.form.phone'))
                            ->tel()
                            ->maxLength(20),
                    ]),

                Section::make(__('filament.therapist.profiles.form.section_security'))
                    ->schema([
                        TextInput::make('password')
                            ->label(__('filament.therapist.profiles.form.password'))
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed()
                            ->minLength(8),
                        TextInput::make('password_confirmation')
                            ->label(__('filament.therapist.profiles.form.password_confirmation'))
                            ->password()
                            ->dehydrated(false),
                    ]),

                Section::make(__('filament.therapist.profiles.form.section_invitation'))
                    ->schema([
                        TextInput::make('invitation_code')
                            ->label(__('filament.therapist.profiles.form.invitation_code'))
                            ->disabled()
                            ->suffixAction(
                                Action::make('regenerate')
                                    ->label(__('filament.therapist.profiles.form.regenerate_label'))
                                    ->icon(Heroicon::OutlinedArrowPath)
                                    ->requiresConfirmation()
                                    ->modalHeading(__('filament.therapist.profiles.form.regenerate_modal_heading'))
                                    ->modalDescription(__('filament.therapist.profiles.form.regenerate_modal_description'))
                                    ->action(function ($record, Component $livewire) {
                                        $code = Str::upper(Str::random(8));
                                        $record->update(['invitation_code' => $code]);

                                        $livewire->refreshFormData(['invitation_code']);

                                        Notification::make()
                                            ->title(__('filament.therapist.profiles.form.regenerate_notification'))
                                            ->success()
                                            ->send();
                                    })
                            ),
                    ]),
            ]);
    }
}
