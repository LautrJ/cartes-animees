<?php

namespace App\Filament\Therapist\Resources\ContentValidations\Schemas;

use App\Enums\ContentValidationStatus;
use App\Models\Card;
use App\Models\Series;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentValidationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('filament.therapist.content_validations.infolist.section_request'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('validatable_type')
                            ->label(__('filament.therapist.content_validations.infolist.type'))
                            ->getStateUsing(fn ($record) => match ($record->validatable_type) {
                                Card::class => __('filament.therapist.content_validations.infolist.type_card'),
                                Series::class => __('filament.therapist.content_validations.infolist.type_series'),
                                default => '-',
                            }),
                        TextEntry::make('status')
                            ->label(__('filament.therapist.content_validations.infolist.status'))
                            ->badge()
                            ->color(fn (ContentValidationStatus $state) => match ($state) {
                                ContentValidationStatus::Pending => 'warning',
                                ContentValidationStatus::Approved => 'success',
                                ContentValidationStatus::Rejected => 'danger',
                            }),
                        TextEntry::make('validatable.name')
                            ->label(__('filament.therapist.content_validations.infolist.content'))
                            ->getStateUsing(fn ($record) => $record->validatable?->name['fr'] ?? '-')
                            ->columnSpanFull(),
                        TextEntry::make('submitted_at')
                            ->label(__('filament.therapist.content_validations.infolist.submitted_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('reviewed_at')
                            ->label(__('filament.therapist.content_validations.infolist.reviewed_at'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('rejection_reason')
                            ->label(__('filament.therapist.content_validations.infolist.rejection_reason'))
                            ->placeholder(__('filament.therapist.content_validations.infolist.rejection_reason_placeholder'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
