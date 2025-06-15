<?php

namespace App\Jobs;

use App\Models\Cota;
use App\Models\Plataforma;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SincronizarCotasPorPlataforma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Plataforma $plataforma)
    {
        // marca para só executar após o commit da transação que mudou o status
        $this->afterCommit();
    }

    public function handle(): void
    {
        $bot = $this->plataforma->bot_identifier;

        // bulk update: restaura total_cotas = cota_original para todas as cotas deste bot
        $updated = Cota::where('bot', $bot)
            ->update([
                'total_cotas' => DB::raw('cota_original'),
            ]);

        Log::info("SincronizarCotasPorPlataforma: restauradas {$updated} cotas para o bot “{$bot}”.");
    }
}
