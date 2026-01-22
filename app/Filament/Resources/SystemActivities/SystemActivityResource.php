<?php

namespace App\Filament\Resources\SystemActivities;

use App\Filament\Resources\SystemActivities\Pages\CreateSystemActivity;
use App\Filament\Resources\SystemActivities\Pages\EditSystemActivity;
use App\Filament\Resources\SystemActivities\Pages\ListSystemActivities;
use App\Filament\Resources\SystemActivities\Pages\ViewSystemActivity;
use App\Filament\Resources\SystemActivities\Schemas\SystemActivityForm;
use App\Filament\Resources\SystemActivities\Schemas\SystemActivityInfolist;
use App\Filament\Resources\SystemActivities\Tables\SystemActivitiesTable;
use App\Models\SystemActivity;
use BackedEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SystemActivityResource extends Resource
{
    protected static ?string $model = SystemActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Activity Logs';

    public static function form(Schema $schema): Schema
    {
        return SystemActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SystemActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemActivitiesTable::configure($table);
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
            'index' => ListSystemActivities::route('/'),
            'view' => ViewSystemActivity::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
