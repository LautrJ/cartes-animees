<?php

namespace App\Filament\Therapist\Widgets;

use App\Models\CommissionRateHistory;
use Filament\Widgets\Widget;

class CurrentCommissionRateWidget extends Widget
{
    protected string $view = 'filament.therapist.widgets.current-commission-rate-widget';
    protected int|string|array $columnSpan = 1;
    protected static ?int $sort = 1;

    public string $rate = '0.00';
    public string $since = '-';

    public function mount(): void
    {
        $latest = CommissionRateHistory::orderBy('effective_from', 'desc')->first();

        if ($latest) {
            $this->rate  = number_format($latest->rate, 2);
            $this->since = $latest->effective_from->format('d/m/Y');
        }
    }
}
