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

    protected static ?string $title = 'Séries';

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
                    ->label('Série')
                    ->searchable(),
                TextColumn::make('pivot.status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed' => 'success',
                        'unlocked' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'completed' => 'Complétée',
                        'unlocked' => 'En cours',
                        default => $state,
                    }),
                TextColumn::make('pivot.unlocked_at')
                    ->label('Débloquée le')
                    ->dateTime('d/m/Y'),
                TextColumn::make('pivot.completed_at')
                    ->label('Complétée le')
                    ->dateTime('d/m/Y')
                    ->placeholder('En cours'),
            ])
            ->filters([])
            ->headerActions([
                Action::make('unlock_series')
                    ->label('Débloquer une série')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->form(function () {
                        $unlockedIds = $this->getOwnerRecord()
                            ->series()
                            ->pluck('series.id')
                            ->toArray();

                        return [
                            Select::make('series_id')
                                ->label('Série à débloquer')
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
                    ->modalHeading('Débloquer une série')
                    ->modalSubmitActionLabel('Débloquer')
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
                            ->title('Série débloquée avec succès')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                Action::make('complete')
                    ->label('Marquer complétée')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->pivot->status === ChildSeriesStatus::Unlocked->value)
                    ->requiresConfirmation()
                    ->modalHeading('Marquer cette série comme complétée ?')
                    ->modalSubmitActionLabel('Confirmer')
                    ->action(function ($record) {
                        $child = $this->getOwnerRecord();

                        $child->series()->updateExistingPivot($record->id, [
                            'status' => ChildSeriesStatus::Completed,
                            'completed_at' => now(),
                        ]);

                        $child->parent->notify(new SeriesCompletedNotification($child, $record));

                        Notification::make()
                            ->title('Série marquée comme complétée')
                            ->success()
                            ->send();
                    }),

                Action::make('detach')
                    ->label('Retirer')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => ! $record->is_base)
                    ->requiresConfirmation()
                    ->modalHeading('Retirer cette série ?')
                    ->modalDescription('L\'enfant n\'aura plus accès à cette série.')
                    ->modalSubmitActionLabel('Retirer')
                    ->action(function ($record) {
                        $this->getOwnerRecord()->series()->detach($record->id);

                        Notification::make()
                            ->title('Série retirée')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
