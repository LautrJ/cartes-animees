<?php

namespace App\Filament\Therapist\Resources\Series\Pages;

use App\Filament\Therapist\Resources\Series\SeriesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSeries extends EditRecord
{
    protected static string $resource = SeriesResource::class;
    protected $listeners = ['cards-updated' => 'updateCardsData'];
    public string $cardsJson = '[]';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->syncCards();
    }

    public function updateCardsData(array $cards): void
    {
        $this->cardsJson = json_encode($cards);
    }

    private function syncCards(): void
    {
        $cardsData = json_decode($this->cardsJson ?? '[]', true);

        if (empty($cardsData)) {
            return;
        }

        $sync = collect($cardsData)->mapWithKeys(fn ($card) => [
            $card['id'] => ['order' => $card['order']],
        ])->toArray();

        $this->record->cards()->sync($sync);
    }
}
