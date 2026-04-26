<?php

namespace App\Filament\Resources\RateHistories\Widgets;

use App\Models\SubscriptionPriceHistory;
use Filament\Widgets\ChartWidget;

class SubscriptionPriceChart extends ChartWidget
{
    protected ?string $heading = 'Évolution du prix d\'abonnement';
    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $history = SubscriptionPriceHistory::orderBy('effective_from', 'asc')->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Prix (€/mois)',
                    'data'            => $history->pluck('price')->toArray(),
                    'borderColor'     => '#a896d8',
                    'backgroundColor' => 'rgba(193, 175, 228, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.3,
                ],
            ],
            'labels' => $history->map(fn($h) => $h->effective_from->format('d/m/Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
