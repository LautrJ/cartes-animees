<?php

namespace App\Filament\Resources\SubscriptionPriceHistories;

use App\Filament\Resources\SubscriptionPriceHistories\Pages\ListSubscriptionPriceHistories;
use App\Filament\Resources\SubscriptionPriceHistories\Pages\ViewSubscriptionPriceHistory;
use App\Filament\Resources\SubscriptionPriceHistories\Schemas\SubscriptionPriceHistoryForm;
use App\Filament\Resources\SubscriptionPriceHistories\Schemas\SubscriptionPriceHistoryInfolist;
use App\Filament\Resources\SubscriptionPriceHistories\Tables\SubscriptionPriceHistoriesTable;
use App\Filament\Resources\SubscriptionPriceHistories\Widgets\SubscriptionPriceChart;
use App\Models\SubscriptionPriceHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubscriptionPriceHistoryResource extends Resource
{
    protected static ?string $model = SubscriptionPriceHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $recordTitleAttribute = 'stripe_price_id';

    protected static string|null|\UnitEnum $navigationGroup = 'Paie & administratif';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Historique des prix d\'abonnement';

    protected static ?string $modelLabel = 'Historique des prix d\'abonnement';

    protected static ?string $pluralModelLabel = 'Historique des prix d\'abonnement';

    public static function form(Schema $schema): Schema
    {
        return SubscriptionPriceHistoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubscriptionPriceHistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionPriceHistoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionPriceHistories::route('/'),
            'view' => ViewSubscriptionPriceHistory::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            SubscriptionPriceChart::class,
        ];
    }
}
