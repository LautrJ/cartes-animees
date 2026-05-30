<?php

namespace App\Filament\Therapist\Resources\ContentValidations;

use App\Filament\Therapist\Resources\ContentValidations\Pages\ListContentValidations;
use App\Filament\Therapist\Resources\ContentValidations\Pages\ViewContentValidation;
use App\Filament\Therapist\Resources\ContentValidations\Schemas\ContentValidationForm;
use App\Filament\Therapist\Resources\ContentValidations\Schemas\ContentValidationInfolist;
use App\Filament\Therapist\Resources\ContentValidations\Tables\ContentValidationsTable;
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

    protected static string|null|\UnitEnum $navigationGroup = 'Suivi';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Demandes en attente';

    protected static ?string $modelLabel = 'Demande en attente';

    protected static ?string $pluralModelLabel = 'Demandes en attente';

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('submitted_by', auth()->id())
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContentValidations::route('/'),
            'view' => ViewContentValidation::route('/{record}'),
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
