<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.users.infolist.sections.personal_info'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')
                            ->label(__('filament.users.infolist.fields.first_name')),
                        TextEntry::make('last_name')
                            ->label(__('filament.users.infolist.fields.last_name')),
                        TextEntry::make('email')
                            ->label(__('filament.users.infolist.fields.email'))
                            ->columnSpanFull(),
                        TextEntry::make('phone')
                            ->label(__('filament.users.infolist.fields.phone'))
                            ->default('-'),
                        TextEntry::make('role')
                            ->label(__('filament.users.infolist.fields.role'))
                            ->badge()
                            ->color(fn (UserRole $state) => match ($state) {
                                UserRole::Admin => 'danger',
                                UserRole::Therapist => 'warning',
                                UserRole::Parent => 'success',
                            }),
                    ]),

                Section::make(__('filament.users.infolist.sections.status'))
                    ->columns(2)
                    ->schema([
                        IconEntry::make('is_active')
                            ->label(__('filament.users.infolist.fields.is_active'))
                            ->boolean(),
                        TextEntry::make('email_verified_at')
                            ->label(__('filament.users.infolist.fields.email_verified_at'))
                            ->dateTime('d/m/Y H:i')
                            ->placeholder(__('filament.users.infolist.fields.email_verified_at_placeholder')),
                    ]),

                Section::make(__('filament.users.infolist.sections.dates'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('filament.users.infolist.fields.created_at'))
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label(__('filament.users.infolist.fields.updated_at'))
                            ->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }
}
