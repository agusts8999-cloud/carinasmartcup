<?php

namespace App\Filament\Resources\VolumeRules;

use App\Filament\Resources\VolumeRules\Pages\CreateVolumeRule;
use App\Filament\Resources\VolumeRules\Pages\EditVolumeRule;
use App\Filament\Resources\VolumeRules\Pages\ListVolumeRules;
use App\Filament\Resources\VolumeRules\Schemas\VolumeRuleForm;
use App\Filament\Resources\VolumeRules\Tables\VolumeRulesTable;
use App\Models\VolumeRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VolumeRuleResource extends Resource
{
    protected static ?string $model = VolumeRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Pemasaran';

    protected static ?string $modelLabel = 'Aturan Volume';

    protected static ?string $pluralModelLabel = 'Aturan Volume';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VolumeRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VolumeRulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVolumeRules::route('/'),
            'create' => CreateVolumeRule::route('/create'),
            'edit' => EditVolumeRule::route('/{record}/edit'),
        ];
    }
}
