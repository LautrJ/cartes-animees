<?php

namespace App\Filament\Therapist\Resources\Patients\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class EditNotesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'edit_notes';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Modifier les notes')
            ->icon(Heroicon::OutlinedPencilSquare)
            ->color('gray')
            ->fillForm(fn ($record) => ['notes' => $record->notes])
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
            });
    }
}
