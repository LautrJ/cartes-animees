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

        $this->label(__('filament.therapist.patients.actions.edit_notes.label'))
            ->icon(Heroicon::OutlinedPencilSquare)
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
            });
    }
}
