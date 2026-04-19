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
                    ->label('Type')
                    ->badge()
                    ->getStateUsing(fn($record) => match($record->validatable_type) {
                        Card::class   => 'Carte',
                        Series::class => 'Série',
                        default       => '-',
                    })
                    ->color(fn($record) => match($record->validatable_type) {
                        Card::class   => 'info',
                        Series::class => 'warning',
                        default       => 'gray',
                    }),
                TextColumn::make('validatable.name')
                    ->label('Contenu')
                    ->getStateUsing(fn($record) => $record->validatable?->name['fr'] ?? '-'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn(ContentValidationStatus $state) => match($state) {
                        ContentValidationStatus::Pending  => 'warning',
                        ContentValidationStatus::Approved => 'success',
                        ContentValidationStatus::Rejected => 'danger',
                    }),
                TextColumn::make('submitted_at')
                    ->label('Soumis le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->label('Traité le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
