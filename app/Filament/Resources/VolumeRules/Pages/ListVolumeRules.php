<?php

namespace App\Filament\Resources\VolumeRules\Pages;

use App\Filament\Resources\VolumeRules\VolumeRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVolumeRules extends ListRecords
{
    protected static string $resource = VolumeRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
