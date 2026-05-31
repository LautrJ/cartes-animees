<?php

namespace App\Filament\Therapist\Resources\CommissionRateHistories;

use App\Filament\Therapist\Resources\CommissionRateHistories\Pages\ListCommissionRateHistories;
use App\Filament\Therapist\Resources\CommissionRateHistories\Pages\ViewCommissionRateHistory;
use App\Filament\Therapist\Resources\CommissionRateHistories\Schemas\CommissionRateHistoryForm;
use App\Filament\Therapist\Resources\CommissionRateHistories\Schemas\CommissionRateHistoryInfolist;
use App\Filament\Therapist\Resources\CommissionRateHistories\Tables\CommissionRateHistoriesTable;
use App\Filament\Therapist\Resources\CommissionRateHistories\Widgets\CommissionRateChart;
use App\Models\CommissionRateHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommissionRateHistoryResource extends Resource
{
    protected static ?string $model = CommissionRateHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $recordTitleAttribute = 'effective_from';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.therapist.commission_rate_histories.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.therapist.commission_rate_histories.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.therapist.commission_rate_histories.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.therapist.commission_rate_histories.navigation.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return CommissionRateHistoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommissionRateHistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommissionRateHistoriesTable::configure($table);
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
            'index' => ListCommissionRateHistories::route('/'),
            'view' => ViewCommissionRateHistory::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            CommissionRateChart::class,
        ];
    }
}
