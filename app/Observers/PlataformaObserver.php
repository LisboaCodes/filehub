<?php

namespace App\Observers;

use App\Jobs\SincronizarCotasPorPlataforma; // Importa nosso Job
use App\Models\Cota;
use App\Models\Plataforma;

class PlataformaObserver
{
    /**
     * Lida com o evento "updated" (atualizado) do Model Plataforma.
     * Este método é acionado automaticamente sempre que uma plataforma é salva no painel.
     */
    public function updated(Plataforma $plataforma): void
    {
        // Primeiro, verificamos se o campo que mudou foi especificamente o 'status'.
        if ($plataforma->wasChanged('status')) {

            // LÓGICA PARA QUANDO UM BOT É DESATIVADO
           if ($plataforma->status === 'Desativado') {
                // ↓ apenas zera o total_cotas, preservando cota_original
                Cota::where('bot', $plataforma->bot_identifier)
                    ->update(['total_cotas' => 0]);
            }
            
            // LÓGICA PARA QUANDO UM BOT VOLTA A FICAR ONLINE
            elseif ($plataforma->status === 'Online') {
                // Dispara um job em segundo plano para reatribuir as cotas.
                // Isso garante que o painel não trave se houver muitos usuários para atualizar.
                SincronizarCotasPorPlataforma::dispatch($plataforma);
            }
        }
    }

    // --- Não precisamos de lógica para os outros eventos ---

    public function created(Plataforma $plataforma): void {}
    public function deleted(Plataforma $plataforma): void {}
    public function restored(Plataforma $plataforma): void {}
    public function forceDeleted(Plataforma $plataforma): void {}
}