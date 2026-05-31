<?php

namespace App\Filament\Resources\ContentValidations\Schemas;

use App\Enums\ContentValidationStatus;
use App\Models\Card;
use App\Models\Series;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentValidationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.content_validations.infolist.sections.validation_request'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('validatable_type')
                            ->label(__('filament.content_validations.infolist.fields.validatable_type'))
                            ->getStateUsing(fn ($record) => match ($record->validatable_type) {
                                Card::class => __('filament.content_validations.infolist.fields.validatable_type_card'),
                                Series::class => __('filament.content_validations.infolist.fields.validatable_type_series'),
                                default => $record->validatable_type,
                            }),
                        TextEntry::make('status')
                            ->label(__('filament.content_validations.infolist.fields.status'))
                            ->badge()
                            ->color(fn (ContentValidationStatus $state) => match ($state) {
                                ContentValidationStatus::Pending => 'warning',
                                ContentValidationStatus::Approved => 'success',
                                ContentValidationStatus::Rejected => 'danger',
                            }),
                        TextEntry::make('submitter.first_name')
                            ->label(__('filament.content_validations.infolist.fields.submitted_by'))
                            ->getStateUsing(fn ($record) => "{$record->submitter->first_name} {$record->submitter->last_name}"),
                        TextEntry::make('submitted_at')
                            ->label(__('filament.content_validations.infolist.fields.submitted_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('reviewer.first_name')
                            ->label(__('filament.content_validations.infolist.fields.reviewed_by'))
                            ->getStateUsing(fn ($record) => $record->reviewer
                                ? "{$record->reviewer->first_name} {$record->reviewer->last_name}"
                                : '-'
                            ),
                        TextEntry::make('reviewed_at')
                            ->label(__('filament.content_validations.infolist.fields.reviewed_at'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('rejection_reason')
                            ->label(__('filament.content_validations.infolist.fields.rejection_reason'))
                            ->placeholder(__('filament.content_validations.infolist.fields.rejection_reason_placeholder'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.content_validations.infolist.sections.card_detail'))
                    ->columns(2)
                    ->visible(fn ($record) => $record->validatable_type === Card::class)
                    ->schema([
                        TextEntry::make('validatable.name')
                            ->label(__('filament.content_validations.infolist.fields.name'))
                            ->getStateUsing(fn ($record) => $record->validatable?->name['fr'] ?? '-'),
                        TextEntry::make('validatable.creator.first_name')
                            ->label(__('filament.content_validations.infolist.fields.created_by'))
                            ->getStateUsing(fn ($record) => $record->validatable?->creator
                                ? "{$record->validatable->creator->first_name} {$record->validatable->creator->last_name}"
                                : '-'
                            ),
                        TextEntry::make('validatable.drawn_animation_path')
                            ->label(__('filament.content_validations.infolist.fields.drawn_animation_path'))
                            ->placeholder('-'),
                        TextEntry::make('validatable.real_animation_path')
                            ->label(__('filament.content_validations.infolist.fields.real_animation_path'))
                            ->placeholder('-'),
                        TextEntry::make('validatable.sound_path')
                            ->label(__('filament.content_validations.infolist.fields.sound_path'))
                            ->placeholder('-'),
                        TextEntry::make('validatable.duration')
                            ->label(__('filament.content_validations.infolist.fields.duration'))
                            ->suffix(__('filament.content_validations.infolist.fields.duration_suffix'))
                            ->placeholder('-'),
                    ]),

                Section::make(__('filament.content_validations.infolist.sections.series_detail'))
                    ->columns(2)
                    ->visible(fn ($record) => $record->validatable_type === Series::class)
                    ->schema([
                        TextEntry::make('validatable.name')
                            ->label(__('filament.content_validations.infolist.fields.name'))
                            ->getStateUsing(fn ($record) => $record->validatable?->name['fr'] ?? '-'),
                        TextEntry::make('validatable.creator.first_name')
                            ->label(__('filament.content_validations.infolist.fields.created_by'))
                            ->getStateUsing(fn ($record) => $record->validatable?->creator
                                ? "{$record->validatable->creator->first_name} {$record->validatable->creator->last_name}"
                                : '-'
                            ),
                        TextEntry::make('validatable.description')
                            ->label(__('filament.content_validations.infolist.fields.description'))
                            ->getStateUsing(fn ($record) => $record->validatable?->description['fr'] ?? '-')
                            ->columnSpanFull(),
                        IconEntry::make('validatable.is_base')
                            ->label(__('filament.content_validations.infolist.fields.is_base'))
                            ->boolean(),
                        TextEntry::make('validatable.cards_count')
                            ->label(__('filament.content_validations.infolist.fields.cards_count'))
                            ->getStateUsing(fn ($record) => $record->validatable?->cards()->count() ?? 0),
                    ]),
            ]);
    }
}
