<?php

namespace App\Filament\Therapist\Resources\ContentValidations\Tables;

use App\Enums\ContentValidationStatus;
use App\Models\Card;
use App\Models\Series;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContentValidationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                TextColumn::make('validatable_type')
                    ->label(__('filament.therapist.content_validations.table.type'))
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->validatable_type) {
                        Card::class => __('filament.therapist.content_validations.table.type_card'),
                        Series::class => __('filament.therapist.content_validations.table.type_series'),
                        default => '-',
                    })
                    ->color(fn ($record) => match ($record->validatable_type) {
                        Card::class => 'info',
                        Series::class => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('validatable.name')
                    ->label(__('filament.therapist.content_validations.table.content'))
                    ->getStateUsing(fn ($record) => $record->validatable?->name['fr'] ?? '-'),
                TextColumn::make('status')
                    ->label(__('filament.therapist.content_validations.table.status'))
                    ->badge()
                    ->color(fn (ContentValidationStatus $state) => match ($state) {
                        ContentValidationStatus::Pending => 'warning',
                        ContentValidationStatus::Approved => 'success',
                        ContentValidationStatus::Rejected => 'danger',
                    }),
                TextColumn::make('submitted_at')
                    ->label(__('filament.therapist.content_validations.table.submitted_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->label(__('filament.therapist.content_validations.table.reviewed_at'))
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.therapist.content_validations.table.filter_status'))
                    ->options([
                        'pending' => __('filament.therapist.content_validations.table.filter_status_pending'),
                        'approved' => __('filament.therapist.content_validations.table.filter_status_approved'),
                        'rejected' => __('filament.therapist.content_validations.table.filter_status_rejected'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
