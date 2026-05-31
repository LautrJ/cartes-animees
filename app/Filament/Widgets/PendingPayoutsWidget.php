<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TherapistPayouts\TherapistPayoutResource;
use App\Models\TherapistPayout;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingPayoutsWidget extends BaseWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('filament.widgets.pending_payouts.heading');
    }

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TherapistPayout::query()
                    ->whereNull('paid_at')
                    ->with(['therapist'])
                    ->orderBy('created_at', 'asc')
            )
            ->columns([
                TextColumn::make('therapist.first_name')
                    ->label(__('filament.widgets.pending_payouts.therapist'))
                    ->getStateUsing(fn ($record) => "{$record->therapist->first_name} {$record->therapist->last_name}"),
                TextColumn::make('amount')
                    ->label(__('filament.widgets.pending_payouts.amount'))
                    ->getStateUsing(fn ($record) => number_format($record->amount, 2).' €'),
                TextColumn::make('patient_count')
                    ->label(__('filament.widgets.pending_payouts.patient_count')),
                TextColumn::make('period_start')
                    ->label(__('filament.widgets.pending_payouts.period'))
                    ->getStateUsing(fn ($record) => $record->period_start->format('m/Y')),
            ])
            ->recordUrl(fn ($record) => TherapistPayoutResource::getUrl('view', ['record' => $record]));
    }
}
