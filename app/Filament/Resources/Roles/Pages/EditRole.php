<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action): void {
                    $protected = ['owner', 'admin', 'warehouse', 'finance', 'cs', 'customer'];
                    if (in_array($this->record->name, $protected, true)) {
                        Notification::make()
                            ->title('Role sistem tidak boleh dihapus')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
