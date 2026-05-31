<?php

namespace App\Filament\Therapist\Resources\Cards\Pages;

use App\Filament\Therapist\Resources\Cards\CardResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCard extends ViewRecord
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('preview')
                ->label(__('filament.therapist.cards.pages.view.action_preview_label'))
                ->icon('heroicon-o-play')
                ->color('success')
                ->modalHeading(__('filament.therapist.cards.pages.view.action_preview_modal_heading_prefix').' '.($this->getRecord()->name['fr'] ?? ''))
                ->modalContent(fn () => view('livewire.card-preview-modal', [
                    'cardId' => $this->getRecord()->id,
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('filament.therapist.cards.pages.view.action_preview_modal_cancel_label')),
        ];
    }

    public function getTitle(): string
    {
        return __('filament.therapist.cards.pages.view.title_prefix').' '.($this->getRecord()->name['fr'] ?? __('filament.therapist.cards.pages.view.title_fallback'));
    }
}
