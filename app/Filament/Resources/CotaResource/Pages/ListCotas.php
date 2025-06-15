<?php
// Em app/Filament/Resources/CotaResource/Pages/ListCotas.php
namespace App\Filament\Resources\CotaResource\Pages;

use App\Filament\Resources\CotaResource;
use App\Models\Cota; // Importe o Model Cota
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB; // Importe o DB Facade

class ListCotas extends ListRecords
{
    protected static string $resource = CotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Ação de Reset Global
            Actions\Action::make('resetarTodasAsCotas')
                ->label('Resetar Todas as Cotas')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Reset Geral')
                ->modalDescription('Tem certeza que deseja resetar as cotas de TODOS os usuários para seus valores originais? Esta ação não pode ser desfeita.')
                ->action(function () {
                    // Query otimizada para resetar todas as cotas de uma vez
                    Cota::query()->update(['total_cotas' => DB::raw('cota_original')]);
                    Notification::make()->title('Todas as cotas foram resetadas!')->success()->send();
                }),
        ];
    }
}