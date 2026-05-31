<?php

namespace App\Filament\Resources\Series\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.series.form.sections.general_info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name.fr')
                            ->label(__('filament.series.form.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description.fr')
                            ->label(__('filament.series.form.fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('created_by')
                            ->label(__('filament.series.form.fields.created_by'))
                            ->options(
                                User::whereIn('role', [UserRole::Admin, UserRole::Therapist])
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => "{$u->first_name} {$u->last_name}"])
                            )
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.series.form.sections.medias'))
                    ->schema([
                        FileUpload::make('thumbnail_path')
                            ->label(__('filament.series.form.fields.thumbnail_path'))
                            ->image()
                            ->nullable(),
                    ]),

                Section::make(__('filament.series.form.sections.settings'))
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_base')
                            ->label(__('filament.series.form.fields.is_base')),
                        Toggle::make('is_validated')
                            ->label(__('filament.series.form.fields.is_validated')),
                        Toggle::make('is_active')
                            ->label(__('filament.series.form.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
