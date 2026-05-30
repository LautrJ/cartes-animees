<?php

namespace App\Filament\Resources\ContentValidations\Actions;

use App\Enums\ContentValidationStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class RejectContentValidation extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'reject';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Rejeter')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->visible(fn ($record) => $record->status === ContentValidationStatus::Pending)
            ->form([
                Textarea::make('rejection_reason')
                    ->label('Motif de rejet')
                    ->required()
                    ->rows(3),
            ])
            ->modalHeading('Rejeter ce contenu')
            ->modalDescription('Veuillez indiquer le motif de rejet.')
            ->modalSubmitActionLabel('Rejeter')
            ->action(function ($record, array $data) {
                $record->update([
                    'status' => ContentValidationStatus::Rejected,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                    'rejection_reason' => $data['rejection_reason'],
                ]);

                $contentName = $record->validatable->name['fr'] ?? '';
                $contentType = class_basename($record->validatable_type) === 'Card' ? 'carte' : 'série';

                Notification::make()
                    ->title("Votre {$contentType} \"{$contentName}\" a été refusée.")
                    ->body($data['rejection_reason'])
                    ->danger()
                    ->sendToDatabase($record->submitter);
            });
    }
}
