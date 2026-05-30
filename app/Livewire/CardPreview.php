<?php

namespace App\Livewire;

use App\Models\Card;
use Livewire\Component;

class CardPreview extends Component
{
    public int $cardId;
    public bool $started = false;
    public bool $showReal = false;

    public function mount(int $cardId): void
    {
        $this->cardId = $cardId;
    }

    public function start(): void
    {
        $this->started = true;

        $card = Card::find($this->cardId);
        $duration = ($card->duration ?? 3) * 1000;

        $this->dispatch('startTimer', duration: $duration);
    }

    public function showRealAnimation(): void
    {
        $this->showReal = true;
    }

    public function render()
    {
        return view('livewire.card-preview', [
            'card' => Card::find($this->cardId),
        ]);
    }
}
