<?php

namespace App\Filament\Resources\CommissionRateHistories\Widgets;

use App\Models\CommissionRateHistory;
use Filament\Widgets\ChartWidget;

class CommissionRateChart extends ChartWidget
{
    protected ?string $heading = 'Évolution du taux de commission';

    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';
    protected function getData(): array
    {
        $history = CommissionRateHistory::orderBy('effective_from', 'asc')->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Taux (€/patient)',
                    'data'            => $history->pluck('rate')->toArray(),
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
