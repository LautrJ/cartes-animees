<?php

namespace App\Filament\Resources\Series\Pages;

use App\Filament\Resources\Series\SeriesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSeries extends CreateRecord
{
    protected static string $resource = SeriesResource::class;
    protected $listeners = ['cards-updated' => 'updateCardsData'];
    public string $cardsJson = '[]';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['is_validated'] = true;
        return $data;
    }

    protected function afterCreate(): void
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
