<?php

namespace App\Filament\Resources\CotaResource\Pages;

use App\Filament\Resources\CotaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCota extends EditRecord
{
    protected static string $resource = CotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
