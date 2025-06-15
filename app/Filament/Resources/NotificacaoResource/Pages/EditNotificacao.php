<?php

namespace App\Filament\Resources\NotificacaoResource\Pages;

use App\Filament\Resources\NotificacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNotificacao extends EditRecord
{
    protected static string $resource = NotificacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
