<?php

namespace App\Filament\Widgets;

use App\Enums\ContentValidationStatus;
use App\Filament\Resources\ContentValidations\ContentValidationResource;
use App\Models\Card;
use App\Models\ContentValidation;
use App\Models\Series;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingValidationsWidget extends BaseWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return __('filament.widgets.pending_validations.heading');
    }

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContentValidation::query()
                    ->where('status', ContentValidationStatus::Pending)
                    ->with(['validatable', 'submitter'])
                    ->orderBy('submitted_at', 'asc')
            )
            ->columns([
                TextColumn::make('validatable_type')
                    ->label(__('filament.widgets.pending_validations.type'))
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->validatable_type) {
                        Card::class => __('filament.widgets.pending_validations.badge_card'),
                        Series::class => __('filament.widgets.pending_validations.badge_series'),
                        default => '-',
                    })
                    ->color(fn ($record) => match ($record->validatable_type) {
                        Card::class => 'info',
                        Series::class => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('validatable.name')
                    ->label(__('filament.widgets.pending_validations.content'))
                    ->getStateUsing(fn ($record) => $record->validatable?->name['fr'] ?? '-'),
                TextColumn::make('submitter.first_name')
                    ->label(__('filament.widgets.pending_validations.submitted_by'))
                    ->getStateUsing(fn ($record) => "{$record->submitter->first_name} {$record->submitter->last_name}"),
                TextColumn::make('submitted_at')
                    ->label(__('filament.widgets.pending_validations.submitted_at'))
                    ->dateTime('d/m/Y H:i'),
            ])
            ->recordUrl(fn ($record) => ContentValidationResource::getUrl('view', ['record' => $record]));
    }
}
