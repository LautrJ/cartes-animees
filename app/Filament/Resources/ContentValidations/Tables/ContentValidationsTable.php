<?php

namespace App\Filament\Resources\ContentValidations\Tables;

use App\Enums\ContentValidationStatus;
use App\Filament\Resources\ContentValidations\Actions\ApproveContentValidation;
use App\Filament\Resources\ContentValidations\Actions\RejectContentValidation;
use App\Models\Card;
use App\Models\Series;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ContentValidationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'asc')
            ->columns([
                TextColumn::make('validatable_type')
                    ->label(__('filament.content_validations.table.columns.validatable_type'))
                    ->badge()
                    ->getStateUsing(fn ($record) => match ($record->validatable_type) {
                        Card::class => __('filament.content_validations.table.columns.validatable_type_card'),
                        Series::class => __('filament.content_validations.table.columns.validatable_type_series'),
                        default => $record->validatable_type,
                    })
                    ->color(fn ($record) => match ($record->validatable_type) {
                        Card::class => 'info',
                        Series::class => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('validatable.name')
                    ->label(__('filament.content_validations.table.columns.content'))
                    ->getStateUsing(fn ($record) => $record->validatable?->name['fr'] ?? '-'),
                TextColumn::make('submitter.first_name')
                    ->label(__('filament.content_validations.table.columns.submitted_by'))
                    ->getStateUsing(fn ($record) => "{$record->submitter->first_name} {$record->submitter->last_name}"),
                TextColumn::make('status')
                    ->label(__('filament.content_validations.table.columns.status'))
                    ->badge()
                    ->color(fn (ContentValidationStatus $state) => match ($state) {
                        ContentValidationStatus::Pending => 'warning',
                        ContentValidationStatus::Approved => 'success',
                        ContentValidationStatus::Rejected => 'danger',
                    }),
                TextColumn::make('submitted_at')
                    ->label(__('filament.content_validations.table.columns.submitted_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->label(__('filament.content_validations.table.columns.reviewed_at'))
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.content_validations.table.filters.status'))
                    ->options([
                        'pending' => __('filament.content_validations.table.filters.status_pending'),
                        'approved' => __('filament.content_validations.table.filters.status_approved'),
                        'rejected' => __('filament.content_validations.table.filters.status_rejected'),
                    ])
                    ->default('pending'),
                SelectFilter::make('validatable_type')
                    ->label(__('filament.content_validations.table.filters.validatable_type'))
                    ->options([
                        Card::class => __('filament.content_validations.table.filters.validatable_type_card'),
                        Series::class => __('filament.content_validations.table.filters.validatable_type_series'),
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ApproveContentValidation::make(),
                RejectContentValidation::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
