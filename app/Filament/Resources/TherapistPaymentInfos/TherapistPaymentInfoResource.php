<?php

namespace App\Filament\Resources\TherapistPaymentInfos;

use App\Filament\Resources\TherapistPaymentInfos\Pages\CreateTherapistPaymentInfo;
use App\Filament\Resources\TherapistPaymentInfos\Pages\EditTherapistPaymentInfo;
use App\Filament\Resources\TherapistPaymentInfos\Pages\ListTherapistPaymentInfos;
use App\Filament\Resources\TherapistPaymentInfos\Pages\ViewTherapistPaymentInfo;
use App\Filament\Resources\TherapistPaymentInfos\Schemas\TherapistPaymentInfoForm;
use App\Filament\Resources\TherapistPaymentInfos\Schemas\TherapistPaymentInfoInfolist;
use App\Filament\Resources\TherapistPaymentInfos\Tables\TherapistPaymentInfosTable;
use App\Models\TherapistPaymentInfo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TherapistPaymentInfoResource extends Resource
{
    protected static ?string $model = TherapistPaymentInfo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'therapist_name';

    protected static string|null|\UnitEnum $navigationGroup = 'Paie & administratif';

    protected static ?int $navigationSort = 6;


    protected static ?string $navigationLabel = 'Infos bancaires Orthophonistes';
    protected static ?string $modelLabel = 'Infos bancaires Orthophoniste';
    protected static ?string $pluralModelLabel = 'Infos bancaires Orthophonistes';

    public static function form(Schema $schema): Schema
    {
        return TherapistPaymentInfoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TherapistPaymentInfoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TherapistPaymentInfosTable::configure($table);
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
            'index' => ListTherapistPaymentInfos::route('/'),
            'create' => CreateTherapistPaymentInfo::route('/create'),
            'view' => ViewTherapistPaymentInfo::route('/{record}'),
            'edit' => EditTherapistPaymentInfo::route('/{record}/edit'),
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
