<?php

namespace App\Filament\Resources\VolumeRules\Pages;

use App\Filament\Resources\VolumeRules\VolumeRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVolumeRule extends EditRecord
{
    protected static string $resource = VolumeRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
