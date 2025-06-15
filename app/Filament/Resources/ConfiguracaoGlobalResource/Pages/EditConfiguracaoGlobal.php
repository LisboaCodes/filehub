<?php

namespace App\Filament\Resources\ConfiguracaoGlobalResource\Pages;

use App\Filament\Resources\ConfiguracaoGlobalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConfiguracaoGlobal extends EditRecord
{
    protected static string $resource = ConfiguracaoGlobalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
