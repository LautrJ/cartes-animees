<?php

namespace App\Filament\Therapist\Resources\Patients\RelationManagers;

use App\Enums\ChildSeriesStatus;
use App\Models\Series;
use App\Notifications\SeriesCompletedNotification;
use App\Notifications\SeriesUnlockedNotification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeriesRelationManager extends RelationManager
{
    protected static string $relationship = 'series';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.therapist.patients.series_relation_manager.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name.fr')
            ->columns([
                TextColumn::make('name.fr')
                    ->label(__('filament.therapist.patients.series_relation_manager.column_serie'))
                    ->searchable(),
                TextColumn::make('pivot.status')
                    ->label(__('filament.therapist.patients.series_relation_manager.column_status'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed' => 'success',
                        'unlocked' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'completed' => __('filament.therapist.patients.series_relation_manager.status_completed'),
                        'unlocked' => __('filament.therapist.patients.series_relation_manager.status_unlocked'),
                        default => $state,
                    }),
                TextColumn::make('pivot.unlocked_at')
                    ->label(__('filament.therapist.patients.series_relation_manager.column_unlocked_at'))
                    ->dateTime('d/m/Y'),
                TextColumn::make('pivot.completed_at')
                    ->label(__('filament.therapist.patients.series_relation_manager.column_completed_at'))
                    ->dateTime('d/m/Y')
                    ->placeholder(__('filament.therapist.patients.series_relation_manager.completed_at_placeholder')),
            ])
            ->filters([])
            ->headerActions([
                Action::make('unlock_series')
                    ->label(__('filament.therapist.patients.series_relation_manager.unlock_label'))
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->form(function () {
                        $unlockedIds = $this->getOwnerRecord()
                            ->series()
                            ->pluck('series.id')
                            ->toArray();

                        return [
                            Select::make('series_id')
                                ->label(__('filament.therapist.patients.series_relation_manager.unlock_series_label'))
                                ->options(
                                    Series::validated()
                                        ->active()
                                        ->whereNotIn('id', $unlockedIds)
                                        ->get()
                                        ->mapWithKeys(fn ($s) => [$s->id => $s->name['fr'] ?? '-'])
                                )
                                ->required()
                                ->searchable(),
                        ];
                    })
                    ->modalHeading(__('filament.therapist.patients.series_relation_manager.unlock_modal_heading'))
                    ->modalSubmitActionLabel(__('filament.therapist.patients.series_relation_manager.unlock_modal_submit'))
                    ->action(function (array $data) {
                        $child = $this->getOwnerRecord();
                        $series = Series::findOrFail($data['series_id']);

                        $child->series()->attach($series->id, [
                            'unlocked_by' => auth()->id(),
                            'status' => ChildSeriesStatus::Unlocked,
                            'unlocked_at' => now(),
                        ]);

                        $child->parent->notify(new SeriesUnlockedNotification($child, $series));

                        Notification::make()
                            ->title(__('filament.therapist.patients.series_relation_manager.unlock_notification'))
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('complete')
                    ->label(__('filament.therapist.patients.series_relation_manager.complete_label'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->pivot->status === ChildSeriesStatus::Unlocked->value)
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.therapist.patients.series_relation_manager.complete_modal_heading'))
                    ->modalSubmitActionLabel(__('filament.therapist.patients.series_relation_manager.complete_modal_submit'))
                    ->action(function ($record) {
                        $child = $this->getOwnerRecord();

                        $child->series()->updateExistingPivot($record->id, [
                            'status' => ChildSeriesStatus::Completed,
                            'completed_at' => now(),
                        ]);

                        $child->parent->notify(new SeriesCompletedNotification($child, $record));

                        Notification::make()
                            ->title(__('filament.therapist.patients.series_relation_manager.complete_notification'))
                            ->success()
                            ->send();
                    }),

                Action::make('detach')
                    ->label(__('filament.therapist.patients.series_relation_manager.detach_label'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => ! $record->is_base)
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.therapist.patients.series_relation_manager.detach_modal_heading'))
                    ->modalDescription(__('filament.therapist.patients.series_relation_manager.detach_modal_description'))
                    ->modalSubmitActionLabel(__('filament.therapist.patients.series_relation_manager.detach_modal_submit'))
                    ->action(function ($record) {
                        $this->getOwnerRecord()->series()->detach($record->id);

                        Notification::make()
                            ->title(__('filament.therapist.patients.series_relation_manager.detach_notification'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
