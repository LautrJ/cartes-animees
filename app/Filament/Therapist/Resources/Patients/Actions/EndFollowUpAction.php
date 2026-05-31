<?php

namespace App\Filament\Therapist\Resources\Patients\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class EndFollowUpAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'end_follow_up';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.therapist.patients.actions.end_follow_up.label'))
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__('filament.therapist.patients.actions.end_follow_up.modal_heading'))
            ->modalDescription(__('filament.therapist.patients.actions.end_follow_up.modal_description'))
            ->modalSubmitActionLabel(__('filament.therapist.patients.actions.end_follow_up.modal_submit'))
            ->action(function ($record) {
                $record->therapists()->updateExistingPivot(auth()->id(), [
                    'ended_at' => now(),
                ]);

                Notification::make()
                    ->title(__('filament.therapist.patients.actions.end_follow_up.notification'))
                    ->success()
                    ->send();

                return redirect(route('filament.therapist.resources.patients.index'));
            });
    }
}
