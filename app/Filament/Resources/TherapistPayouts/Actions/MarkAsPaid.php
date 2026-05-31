<?php

namespace App\Filament\Resources\TherapistPayouts\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class MarkAsPaid extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'mark_as_paid';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.therapist_payouts.actions.mark_as_paid.label'))
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->visible(fn ($record) => is_null($record->paid_at))
            ->requiresConfirmation()
            ->modalHeading(__('filament.therapist_payouts.actions.mark_as_paid.modal_heading'))
            ->modalDescription(__('filament.therapist_payouts.actions.mark_as_paid.modal_description'))
            ->modalSubmitActionLabel(__('filament.therapist_payouts.actions.mark_as_paid.modal_submit_label'))
            ->action(function ($record) {
                $record->update(['paid_at' => now()]);

                Notification::make()
                    ->title(__('filament.therapist_payouts.actions.mark_as_paid.notification_success'))
                    ->success()
                    ->send();
            });
    }
}
