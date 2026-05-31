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

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.therapist.content_validations.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.therapist.content_validations.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.therapist.content_validations.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.therapist.content_validations.navigation.plural_model_label');
    }

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
