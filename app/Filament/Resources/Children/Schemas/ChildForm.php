<?php

namespace App\Filament\Resources\Children\Schemas;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChildForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.children.form.sections.personal_info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('filament.children.form.fields.first_name'))
                            ->required()
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label(__('filament.children.form.fields.last_name'))
                            ->required()
                            ->maxLength(100),
                        DatePicker::make('birthdate')
                            ->label(__('filament.children.form.fields.birthdate'))
                            ->displayFormat('d/m/Y')
                            ->nullable(),
                        Select::make('parent_id')
                            ->label(__('filament.children.form.fields.parent_id'))
                            ->options(
                                User::where('role', UserRole::Parent)
                                    ->get()
                                    ->mapWithKeys(fn ($u) => [$u->id => "{$u->first_name} {$u->last_name}"])
                            )
                            ->searchable()
                            ->required(),
                    ]),

                Section::make(__('filament.children.form.sections.follow_up'))
                    ->schema([
                        Select::make('therapists')
                            ->label(__('filament.children.form.fields.therapists'))
                            ->relationship(
                                'therapists',
                                'first_name',
                                fn ($query) => $query->where('role', UserRole::Therapist)
                            )
                            ->multiple()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(__('filament.children.form.fields.notes'))
                            ->rows(4)
                            ->nullable(),
                    ]),
            ]);
    }
}
