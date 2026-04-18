<?php

namespace App\Filament\Resources\TherapistPayouts;

use App\Filament\Resources\TherapistPayouts\Pages\CreateTherapistPayout;
use App\Filament\Resources\TherapistPayouts\Pages\EditTherapistPayout;
use App\Filament\Resources\TherapistPayouts\Pages\ListTherapistPayouts;
use App\Filament\Resources\TherapistPayouts\Pages\ViewTherapistPayout;
use App\Filament\Resources\TherapistPayouts\Schemas\TherapistPayoutForm;
use App\Filament\Resources\TherapistPayouts\Schemas\TherapistPayoutInfolist;
use App\Filament\Resources\TherapistPayouts\Tables\TherapistPayoutsTable;
use App\Models\TherapistPayout;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TherapistPayoutResource extends Resource
{
    protected static ?string $model = TherapistPayout::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'amount';

    public static function form(Schema $schema): Schema
    {
        return TherapistPayoutForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TherapistPayoutInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TherapistPayoutsTable::configure($table);
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
            'index' => ListTherapistPayouts::route('/'),
            'create' => CreateTherapistPayout::route('/create'),
            'view' => ViewTherapistPayout::route('/{record}'),
            'edit' => EditTherapistPayout::route('/{record}/edit'),
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
