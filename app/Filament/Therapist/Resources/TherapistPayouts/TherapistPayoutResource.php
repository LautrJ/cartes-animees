<?php

namespace App\Filament\Therapist\Resources\TherapistPayouts;

use App\Filament\Therapist\Resources\TherapistPayouts\Pages\ListTherapistPayouts;
use App\Filament\Therapist\Resources\TherapistPayouts\Pages\ViewTherapistPayout;
use App\Filament\Therapist\Resources\TherapistPayouts\Schemas\TherapistPayoutForm;
use App\Filament\Therapist\Resources\TherapistPayouts\Schemas\TherapistPayoutInfolist;
use App\Filament\Therapist\Resources\TherapistPayouts\Tables\TherapistPayoutsTable;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $recordTitleAttribute = 'amount';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.therapist.therapist_payouts.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.therapist.therapist_payouts.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.therapist.therapist_payouts.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.therapist.therapist_payouts.navigation.plural_model_label');
    }

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('therapist_id', auth()->id())
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTherapistPayouts::route('/'),
            'view' => ViewTherapistPayout::route('/{record}'),
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
