<?php

namespace App\Filament\Resources\NotificacaoResource\Pages;

use App\Filament\Resources\NotificacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNotificacao extends ViewRecord
{
    protected static string $resource = NotificacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
