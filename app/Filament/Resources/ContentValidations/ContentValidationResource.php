<?php

namespace App\Filament\Resources\ContentValidations;

use App\Filament\Resources\ContentValidations\Pages\CreateContentValidation;
use App\Filament\Resources\ContentValidations\Pages\EditContentValidation;
use App\Filament\Resources\ContentValidations\Pages\ListContentValidations;
use App\Filament\Resources\ContentValidations\Pages\ViewContentValidation;
use App\Filament\Resources\ContentValidations\Schemas\ContentValidationForm;
use App\Filament\Resources\ContentValidations\Schemas\ContentValidationInfolist;
use App\Filament\Resources\ContentValidations\Tables\ContentValidationsTable;
use App\Models\ContentValidation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContentValidationResource extends Resource
{
    protected static ?string $model = ContentValidation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'submitted_at';

    protected static string|null|\UnitEnum $navigationGroup = 'Gérer le contenu';

    protected static ?int $navigationSort = 1;


    protected static ?string $navigationLabel = 'Validation de contenu';
    protected static ?string $modelLabel = 'Validation de contenu';
    protected static ?string $pluralModelLabel = 'Validation de contenu';

    public static function form(Schema $schema): Schema
    {
        return ContentValidationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentValidationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentValidationsTable::configure($table);
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
            'index' => ListContentValidations::route('/'),
            'view' => ViewContentValidation::route('/{record}'),
            'edit' => EditContentValidation::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['validatable', 'submitter', 'reviewer'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
