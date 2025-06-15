<?php

namespace App\Filament\Resources\ConfiguracaoGlobalResource\Pages;

use App\Filament\Resources\ConfiguracaoGlobalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfiguracaoGlobals extends ListRecords
{
    protected static string $resource = ConfiguracaoGlobalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
