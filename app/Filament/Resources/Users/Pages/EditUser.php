<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action): void {
                    if (Auth::id() === $this->record->getKey()) {
                        Notification::make()
                            ->title('Tidak dapat menghapus akun sendiri')
                            ->danger()
                            ->send();
                        $action->cancel();

                        return;
                    }

                    if ($this->record->hasRole('owner') && User::role('owner')->count() <= 1) {
                        Notification::make()
                            ->title('Minimal satu owner harus ada')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
