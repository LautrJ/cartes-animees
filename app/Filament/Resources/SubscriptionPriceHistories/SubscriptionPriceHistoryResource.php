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

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation_groups.payroll');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.subscription_price_histories.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.subscription_price_histories.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.subscription_price_histories.navigation.plural_model_label');
    }

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
