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

        $this->label('Terminer le suivi')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Terminer le suivi ?')
            ->modalDescription('Vous ne pourrez plus accéder à ce patient. Cette action est irréversible.')
            ->modalSubmitActionLabel('Terminer le suivi')
            ->action(function ($record) {
                $record->therapists()->updateExistingPivot(auth()->id(), [
                    'ended_at' => now(),
                ]);

                Notification::make()
                    ->title('Suivi terminé')
                    ->success()
                    ->send();

                return redirect(route('filament.therapist.resources.patients.index'));
            });
    }
}
