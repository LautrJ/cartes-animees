<?php

namespace App\Filament\Therapist\Resources\Patients\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informations personnelles')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('Prénom'),
                        TextEntry::make('last_name')
                            ->label('Nom'),
                        TextEntry::make('birthdate')
                            ->label('Date de naissance')
                            ->date('d/m/Y')
                            ->placeholder('Non renseignée'),
                        TextEntry::make('parent.first_name')
                            ->label('Parent')
                            ->getStateUsing(fn($record) => "{$record->parent->first_name} {$record->parent->last_name}"),
                    ]),

                Section::make('Notes')
                    ->headerActions([
                        Action::make('edit_notes')
                            ->label('')
                            ->icon('heroicon-s-pencil-square')
                            ->color('gray')
                            ->fillForm(fn($record) => ['notes' => $record->notes])
                            ->form([
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(5)
                                    ->placeholder('Ajouter des notes sur ce patient...'),
                            ])
                            ->modalHeading('Modifier les notes')
                            ->modalSubmitActionLabel('Enregistrer')
                            ->action(function ($record, array $data) {
                                $record->update(['notes' => $data['notes']]);

                                Notification::make()
                                    ->title('Notes mises à jour')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('Aucune note')
                            ->columnSpanFull(),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Mis à jour le')
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
