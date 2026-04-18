<?php

namespace App\Filament\Resources\CommissionRateHistories;

use App\Filament\Resources\CommissionRateHistories\Pages\CreateCommissionRateHistory;
use App\Filament\Resources\CommissionRateHistories\Pages\EditCommissionRateHistory;
use App\Filament\Resources\CommissionRateHistories\Pages\ListCommissionRateHistories;
use App\Filament\Resources\CommissionRateHistories\Pages\ViewCommissionRateHistory;
use App\Filament\Resources\CommissionRateHistories\Schemas\CommissionRateHistoryForm;
use App\Filament\Resources\CommissionRateHistories\Schemas\CommissionRateHistoryInfolist;
use App\Filament\Resources\CommissionRateHistories\Tables\CommissionRateHistoriesTable;
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

    protected static ?string $recordTitleAttribute = 'rate';

    protected static string|null|\UnitEnum $navigationGroup = 'Paie & administratif';

    protected static ?int $navigationSort = 8;


    protected static ?string $navigationLabel = 'Historique des taux de commission';
    protected static ?string $modelLabel = 'Historique des taux de commission';
    protected static ?string $pluralModelLabel = 'Historique des taux de commission';

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
            'create' => CreateCommissionRateHistory::route('/create'),
            'view' => ViewCommissionRateHistory::route('/{record}'),
            'edit' => EditCommissionRateHistory::route('/{record}/edit'),
        ];
    }
}
