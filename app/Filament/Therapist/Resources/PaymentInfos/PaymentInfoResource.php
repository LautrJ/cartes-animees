<?php

namespace App\Filament\Therapist\Resources\PaymentInfos;

use App\Filament\Therapist\Resources\PaymentInfos\Pages\CreatePaymentInfo;
use App\Filament\Therapist\Resources\PaymentInfos\Pages\EditPaymentInfo;
use App\Filament\Therapist\Resources\PaymentInfos\Pages\ViewPaymentInfo;
use App\Filament\Therapist\Resources\PaymentInfos\Schemas\PaymentInfoForm;
use App\Filament\Therapist\Resources\PaymentInfos\Schemas\PaymentInfoInfolist;
use App\Filament\Therapist\Resources\PaymentInfos\Tables\PaymentInfosTable;
use App\Models\TherapistPaymentInfo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentInfoResource extends Resource
{
    protected static ?string $model = TherapistPaymentInfo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'bank_name';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.therapist.payment_infos.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.therapist.payment_infos.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.therapist.payment_infos.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.therapist.payment_infos.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentInfoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentInfosTable::configure($table);
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
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ViewPaymentInfo::route('/'),
            'create' => CreatePaymentInfo::route('/create'),
            'view' => ViewPaymentInfo::route('/{record}'),
            'edit' => EditPaymentInfo::route('/{record}/edit'),
        ];
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        if ($name === 'index' || $name === null) {
            $paymentInfo = TherapistPaymentInfo::where('user_id', auth()->id())->first();

            if ($paymentInfo) {
                return parent::getUrl('view', ['record' => $paymentInfo], $isAbsolute, $panel, $tenant);
            }

            return parent::getUrl('create', [], $isAbsolute, $panel, $tenant);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
