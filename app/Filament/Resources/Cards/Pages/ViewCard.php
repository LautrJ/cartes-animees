<?php

namespace App\Filament\Resources\Cards\Pages;

use App\Filament\Resources\Cards\CardResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCard extends ViewRecord
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make('Modifier'),
            Action::make('preview')
                ->label('Prévisualiser')
                ->icon('heroicon-o-play')
                ->color('success')
                ->modalHeading('Prévisualisation — '.($this->getRecord()->name['fr'] ?? ''))
                ->modalContent(fn () => view('livewire.card-preview-modal', [
                    'cardId' => $this->getRecord()->id,
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fermer'),
        ];
    }

    public function getTitle(): string
    {
        return 'Afficher '.($this->getRecord()->name['fr'] ?? 'Animation');
    }
}
