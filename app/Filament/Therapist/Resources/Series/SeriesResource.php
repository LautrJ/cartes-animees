<?php

namespace App\Filament\Therapist\Resources\Series;

use App\Filament\Therapist\Resources\Series\Pages\CreateSeries;
use App\Filament\Therapist\Resources\Series\Pages\ListSeries;
use App\Filament\Therapist\Resources\Series\Pages\ViewSeries;
use App\Filament\Therapist\Resources\Series\Schemas\SeriesForm;
use App\Filament\Therapist\Resources\Series\Schemas\SeriesInfolist;
use App\Filament\Therapist\Resources\Series\Tables\SeriesTable;
use App\Models\Series;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name->fr';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.therapist.series.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.therapist.series.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.therapist.series.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.therapist.series.navigation.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return SeriesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SeriesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeriesTable::configure($table);
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
            ->where('is_active', true)
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeries::route('/'),
            'create' => CreateSeries::route('/create'),
            'view' => ViewSeries::route('/{record}'),
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
