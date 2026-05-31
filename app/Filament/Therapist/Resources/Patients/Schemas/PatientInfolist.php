<?php

namespace App\Filament\Therapist\Resources\Patients\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.therapist.patients.infolist.section_personal_info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')
                            ->label(__('filament.therapist.patients.infolist.first_name')),
                        TextEntry::make('last_name')
                            ->label(__('filament.therapist.patients.infolist.last_name')),
                        TextEntry::make('birthdate')
                            ->label(__('filament.therapist.patients.infolist.birthdate'))
                            ->date('d/m/Y')
                            ->placeholder(__('filament.therapist.patients.infolist.birthdate_placeholder')),
                        TextEntry::make('parent.first_name')
                            ->label(__('filament.therapist.patients.infolist.parent'))
                            ->getStateUsing(fn ($record) => "{$record->parent->first_name} {$record->parent->last_name}"),
                    ]),

                Section::make(__('filament.therapist.patients.infolist.section_notes'))
                    ->headerActions([
                        Action::make('edit_notes')
                            ->label('')
                            ->icon('heroicon-s-pencil-square')
                            ->color('gray')
                            ->fillForm(fn ($record) => ['notes' => $record->notes])
                            ->form([
                                Textarea::make('notes')
                                    ->label(__('filament.therapist.patients.actions.edit_notes.notes_label'))
                                    ->rows(5)
                                    ->placeholder(__('filament.therapist.patients.actions.edit_notes.notes_placeholder')),
                            ])
                            ->modalHeading(__('filament.therapist.patients.actions.edit_notes.modal_heading'))
                            ->modalSubmitActionLabel(__('filament.therapist.patients.actions.edit_notes.modal_submit'))
                            ->action(function ($record, array $data) {
                                $record->update(['notes' => $data['notes']]);

                                Notification::make()
                                    ->title(__('filament.therapist.patients.actions.edit_notes.notification'))
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        TextEntry::make('notes')
                            ->label(__('filament.therapist.patients.infolist.notes'))
                            ->placeholder(__('filament.therapist.patients.infolist.notes_placeholder'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.therapist.patients.infolist.section_dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.therapist.patients.infolist.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.therapist.patients.infolist.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
