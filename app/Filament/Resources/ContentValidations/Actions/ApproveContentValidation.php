<?php

namespace App\Filament\Resources\ContentValidations\Actions;

use App\Enums\ContentValidationStatus;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ApproveContentValidation extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Approuver')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->visible(fn ($record) => $record->status === ContentValidationStatus::Pending)
            ->requiresConfirmation()
            ->modalHeading('Approuver ce contenu ?')
            ->modalDescription('Le contenu sera validé et accessible aux orthophonistes.')
            ->modalSubmitActionLabel('Confirmer')
            ->action(function ($record) {
                $record->update([
                    'status' => ContentValidationStatus::Approved,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                $record->validatable->update(['is_validated' => true]);

                $contentName = $record->validatable->name['fr'] ?? '';
                $contentType = class_basename($record->validatable_type) === 'Card' ? 'carte' : 'série';

                Notification::make()
                    ->title("Votre {$contentType} \"{$contentName}\" a été approuvée.")
                    ->success()
                    ->sendToDatabase($record->submitter);
            });
    }
}
