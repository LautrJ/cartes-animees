<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use STS\FilamentImpersonate\Actions\Impersonate;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(query: function ($query, $search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn(UserRole $state) => match($state) {
                        UserRole::Admin     => 'danger',
                        UserRole::Therapist => 'warning',
                        UserRole::Parent    => 'success',
                    }),
                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->default('-'),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rôle')
                    ->options(UserRole::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Impersonate::make()
                    ->label('')
                    ->redirectTo(fn($record) => match(true) {
                        $record->isTherapist() => '/therapist',
                        $record->isParent()    => '/',
                        default                => '/admin',
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
