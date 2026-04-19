<?php

namespace App\Filament\Widgets;

use App\Enums\ContentValidationStatus;
use App\Filament\Resources\ContentValidations\ContentValidationResource;
use App\Models\ContentValidation;
use App\Models\Card;
use App\Models\Series;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingValidationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Validations en attente';
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
                TextColumn::make('submitter.first_name')
                    ->label('Soumis par')
                    ->getStateUsing(fn($record) => "{$record->submitter->first_name} {$record->submitter->last_name}"),
                TextColumn::make('submitted_at')
                    ->label('Soumis le')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->recordUrl(fn($record) => ContentValidationResource::getUrl('view', ['record' => $record]));
    }
}
