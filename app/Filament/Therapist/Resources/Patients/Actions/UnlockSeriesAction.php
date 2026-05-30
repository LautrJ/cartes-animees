<?php

namespace App\Filament\Therapist\Resources\Patients\Actions;

use App\Enums\ChildSeriesStatus;
use App\Models\Series;
use App\Notifications\SeriesUnlockedNotification;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class UnlockSeriesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'unlock_series';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Débloquer une série')
            ->icon(Heroicon::OutlinedLockOpen)
            ->color('success')
            ->form(function ($record) {
                $unlockedSeriesIds = $record->series()->pluck('series.id')->toArray();

                return [
                    Select::make('series_id')
                        ->label('Série à débloquer')
                        ->options(
                            Series::validated()
                                ->active()
                                ->whereNotIn('id', $unlockedSeriesIds)
                                ->get()
                                ->mapWithKeys(fn ($s) => [$s->id => $s->name['fr'] ?? '-'])
                        )
                        ->required()
                        ->searchable(),
                ];
            })
            ->modalHeading('Débloquer une série')
            ->modalSubmitActionLabel('Débloquer')
            ->action(function ($record, array $data) {
                $series = Series::findOrFail($data['series_id']);

                $record->series()->attach($series->id, [
                    'unlocked_by' => auth()->id(),
                    'status' => ChildSeriesStatus::Unlocked,
                    'unlocked_at' => now(),
                ]);

                $record->parent->notify(new SeriesUnlockedNotification($record, $series));

                Notification::make()
                    ->title('Série débloquée avec succès')
                    ->success()
                    ->send();
            });
    }
}
