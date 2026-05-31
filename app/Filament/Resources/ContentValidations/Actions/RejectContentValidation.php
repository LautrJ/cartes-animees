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

        $this->label(__('filament.content_validations.actions.reject.label'))
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->visible(fn ($record) => $record->status === ContentValidationStatus::Pending)
            ->form([
                Textarea::make('rejection_reason')
                    ->label(__('filament.content_validations.actions.reject.field_rejection_reason'))
                    ->required()
                    ->rows(3),
            ])
            ->modalHeading(__('filament.content_validations.actions.reject.modal_heading'))
            ->modalDescription(__('filament.content_validations.actions.reject.modal_description'))
            ->modalSubmitActionLabel(__('filament.content_validations.actions.reject.modal_submit_label'))
            ->action(function ($record, array $data) {
                $record->update([
                    'status' => ContentValidationStatus::Rejected,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                    'rejection_reason' => $data['rejection_reason'],
                ]);

                $contentName = $record->validatable->name['fr'] ?? '';
                $contentTypeKey = class_basename($record->validatable_type) === 'Card' ? 'content_type_card' : 'content_type_series';

                Notification::make()
                    ->title(__('filament.content_validations.actions.reject.notification_rejected', [
                        'type' => __("filament.content_validations.{$contentTypeKey}"),
                        'name' => $contentName,
                    ]))
                    ->body($data['rejection_reason'])
                    ->danger()
                    ->sendToDatabase($record->submitter);
            });
    }
}
