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

        $this->label('Marquer comme payé')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->visible(fn ($record) => is_null($record->paid_at))
            ->requiresConfirmation()
            ->modalHeading('Confirmer le paiement')
            ->modalDescription('Confirmer que ce virement a bien été effectué ?')
            ->modalSubmitActionLabel('Confirmer')
            ->action(function ($record) {
                $record->update(['paid_at' => now()]);

                Notification::make()
                    ->title('Virement marqué comme payé')
                    ->success()
                    ->send();
            });
    }
}
