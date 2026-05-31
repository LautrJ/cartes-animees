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

        $this->label(__('filament.content_validations.actions.approve.label'))
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->visible(fn ($record) => $record->status === ContentValidationStatus::Pending)
            ->requiresConfirmation()
            ->modalHeading(__('filament.content_validations.actions.approve.modal_heading'))
            ->modalDescription(__('filament.content_validations.actions.approve.modal_description'))
            ->modalSubmitActionLabel(__('filament.content_validations.actions.approve.modal_submit_label'))
            ->action(function ($record) {
                $record->update([
                    'status' => ContentValidationStatus::Approved,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                $record->validatable->update(['is_validated' => true]);

                $contentName = $record->validatable->name['fr'] ?? '';
                $contentTypeKey = class_basename($record->validatable_type) === 'Card' ? 'content_type_card' : 'content_type_series';

                Notification::make()
                    ->title(__('filament.content_validations.actions.approve.notification_approved', [
                        'type' => __("filament.content_validations.{$contentTypeKey}"),
                        'name' => $contentName,
                    ]))
                    ->success()
                    ->sendToDatabase($record->submitter);
            });
    }
}
