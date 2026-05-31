<?php

namespace App\Filament\Therapist\Resources\Profiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.therapist.profiles.infolist.section_personal'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')->label(__('filament.therapist.profiles.infolist.first_name')),
                        TextEntry::make('last_name')->label(__('filament.therapist.profiles.infolist.last_name')),
                        TextEntry::make('email')->label(__('filament.therapist.profiles.infolist.email')),
                        TextEntry::make('phone')
                            ->label(__('filament.therapist.profiles.infolist.phone'))
                            ->placeholder(__('filament.therapist.profiles.infolist.phone_placeholder')),
                    ]),

                Section::make(__('filament.therapist.profiles.infolist.section_invitation'))
                    ->schema([
                        TextEntry::make('invitation_code')
                            ->label(__('filament.therapist.profiles.infolist.invitation_code'))
                            ->copyable()
                            ->copyMessage(__('filament.therapist.profiles.infolist.copy_message'))
                            ->fontFamily('mono'),
                    ]),
            ]);
    }
}
