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

    protected static string|null|\UnitEnum $navigationGroup = 'Contenu';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Animations';

    protected static ?string $modelLabel = 'Animation';

    protected static ?string $pluralModelLabel = 'Animations';

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
