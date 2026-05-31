<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.users.form.sections.personal_info'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('filament.users.form.fields.first_name'))
                            ->required()
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label(__('filament.users.form.fields.last_name'))
                            ->required()
                            ->maxLength(100),
                        TextInput::make('email')
                            ->label(__('filament.users.form.fields.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label(__('filament.users.form.fields.phone'))
                            ->tel()
                            ->maxLength(20),
                        Select::make('role')
                            ->label(__('filament.users.form.fields.role'))
                            ->options(UserRole::class)
                            ->default(UserRole::Parent)
                            ->required(),
                    ]),

                Section::make(__('filament.users.form.sections.security'))
                    ->schema([
                        TextInput::make('password')
                            ->label(__('filament.users.form.fields.password'))
                            ->password()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed()
                            ->minLength(8),
                        TextInput::make('password_confirmation')
                            ->label(__('filament.users.form.fields.password_confirmation'))
                            ->password()
                            ->dehydrated(false),
                    ]),

                Section::make(__('filament.users.form.sections.status'))
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('filament.users.form.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
