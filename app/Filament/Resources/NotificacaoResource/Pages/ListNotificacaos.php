<?php

namespace App\Filament\Resources\NotificacaoResource\Pages;

use App\Filament\Resources\NotificacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificacaos extends ListRecords
{
    protected static string $resource = NotificacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
