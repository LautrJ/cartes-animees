<?php

namespace App\Filament\Therapist\Resources\Profiles;

use App\Filament\Therapist\Resources\Profiles\Pages\EditProfile;
use App\Filament\Therapist\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Therapist\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Therapist\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Therapist\Resources\Profiles\Schemas\ProfileInfolist;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Mon profil';

    protected static ?string $modelLabel = 'Mon profil';

    protected static ?string $pluralModelLabel = 'Mon profil';

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfileInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        if ($name === 'index' || $name === null) {
            return parent::getUrl('view', ['record' => auth()->id()], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
        }

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfiles::route('/'),
            'view' => ViewProfile::route('/{record}'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
