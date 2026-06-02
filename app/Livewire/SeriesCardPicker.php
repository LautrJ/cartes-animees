<?php

namespace App\Livewire;

use App\Models\Card;
use App\Models\Series;
use Livewire\Component;

class SeriesCardPicker extends Component
{
    public ?int $seriesId = null;
    public string $search = '';
    public array $selectedCards = [];
    public array $availableCards = [];

    public function mount(?int $seriesId = null) {
        $this->seriesId = $seriesId;

        $this->availableCards = Card::where('is_validated', true)
            ->get()
            ->map(fn ($card) => [
                'id' => $card->id,
                'name' => $card->name['fr'] ?? '-',
                'duration' => $card->duration,
                'drawn_animation_path' => $card->drawn_animation_path,
            ])
            ->toArray();

        if ($this->seriesId) {
            $series = Series::find($this->seriesId);
            if ($series) {
                $this->selectedCards = $series->cards()
                    ->orderBy('series_cards.order')
                    ->get()
                    ->map(fn ($card) => [
                        'id'       => $card->id,
                        'name'     => $card->name['fr'] ?? '-',
                        'duration' => $card->duration,
                        'drawn_animation_path' => $card->drawn_animation_path,
                        'order'    => $card->pivot->order,
                    ])
                    ->toArray();

                $selectedIds = array_column($this->selectedCards, 'id');
                $this->availableCards = array_values(
                    array_filter($this->availableCards, fn ($card) => ! in_array($card['id'], $selectedIds))
                );
                $this->dispatch('cards-updated', cards: $this->selectedCards);
            }
        }
    }

    public function addCard(int $cardId): void
    {
        $card = collect($this->availableCards)->firstWhere('id', $cardId);

        if (!$card) {
            return;
        }

        $card['order'] = count($this->selectedCards) + 1;
        $this->selectedCards[] = $card;

        $this->availableCards = array_values(
            array_filter(
                $this->availableCards,
                fn ($card) => $card['id'] !== $cardId
            )
        );

        $this->dispatch('cards-updated', cards: $this->selectedCards);
    }

    public function removeCard(int $cardId): void
    {
        $card = collect($this->selectedCards)->firstWhere('id', $cardId);
        if (!$card) {
            return;
        }

        $this->selectedCards = array_values(
            array_filter(
                $this->selectedCards,
                fn ($card) => $card['id'] !== $cardId
            )
        );

        foreach ($this->selectedCards as $index => &$selected) {
            $selected['order'] = $index + 1;
        }

        unset($card['order']);
        $this->availableCards[] = $card;

        $this->dispatch('cards-updated', cards: $this->selectedCards);
    }

    public function reorder(array $orderedIds): void
    {
        $reordered = [];
        foreach ($orderedIds as $index => $id) {
            $card = collect($this->selectedCards)->firstWhere('id', $id);
            if ($card) {
                $card['order'] = $index + 1;
                $reordered[] = $card;
            }
        }
        $this->selectedCards = $reordered;

        $this->dispatch('cards-updated', cards: $this->selectedCards);
    }

    public function render()
    {
        return view('livewire.series-card-picker');
    }
}
