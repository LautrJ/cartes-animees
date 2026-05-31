<?php

namespace App\Filament\Therapist\Resources\Patients;

use App\Filament\Therapist\Resources\Patients\Pages\ListPatients;
use App\Filament\Therapist\Resources\Patients\Pages\ViewPatient;
use App\Filament\Therapist\Resources\Patients\RelationManagers\SeriesRelationManager;
use App\Filament\Therapist\Resources\Patients\Schemas\PatientForm;
use App\Filament\Therapist\Resources\Patients\Schemas\PatientInfolist;
use App\Filament\Therapist\Resources\Patients\Tables\PatientsTable;
use App\Models\Child;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = Child::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.therapist.patients.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.therapist.patients.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.therapist.patients.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.therapist.patients.plural_model_label');
    }

    public static function form(Schema $schema): Schema
    {
        return PatientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PatientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SeriesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('therapists', function ($query) {
                $query->where('users.id', auth()->id())
                    ->whereNull('child_therapist.ended_at');
            })
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'view' => ViewPatient::route('/{record}'),
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
