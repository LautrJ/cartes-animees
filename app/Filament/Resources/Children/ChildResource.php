<?php

namespace App\Filament\Resources\Children;

use App\Filament\Resources\Children\Pages\CreateChild;
use App\Filament\Resources\Children\Pages\EditChild;
use App\Filament\Resources\Children\Pages\ListChildren;
use App\Filament\Resources\Children\Pages\ViewChild;
use App\Filament\Resources\Children\Schemas\ChildForm;
use App\Filament\Resources\Children\Schemas\ChildInfolist;
use App\Filament\Resources\Children\Tables\ChildrenTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChildResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFaceSmile;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static string|null|\UnitEnum $navigationGroup = 'Gérer les utilisateurs';

    protected static ?int $navigationSort = 2;


    protected static ?string $navigationLabel = 'Enfants';
    protected static ?string $modelLabel = 'Enfant';
    protected static ?string $pluralModelLabel = 'Enfants';

    public static function form(Schema $schema): Schema
    {
        return ChildForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChildInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChildrenTable::configure($table);
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
            'index' => ListChildren::route('/'),
            'create' => CreateChild::route('/create'),
            'view' => ViewChild::route('/{record}'),
            'edit' => EditChild::route('/{record}/edit'),
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
