<?php

namespace App\Filament\Therapist\Resources\Cards;

use App\Filament\Therapist\Resources\Cards\Pages\CreateCard;
use App\Filament\Therapist\Resources\Cards\Pages\ListCards;
use App\Filament\Therapist\Resources\Cards\Pages\ViewCard;
use App\Filament\Therapist\Resources\Cards\Schemas\CardForm;
use App\Filament\Therapist\Resources\Cards\Schemas\CardInfolist;
use App\Filament\Therapist\Resources\Cards\Tables\CardsTable;
use App\Models\Card;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGif;

    protected static ?string $recordTitleAttribute = 'name->fr';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.therapist.cards.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.therapist.cards.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.therapist.cards.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.therapist.cards.navigation.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return CardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_validated', true)
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCards::route('/'),
            'create' => CreateCard::route('/create'),
            'view' => ViewCard::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
