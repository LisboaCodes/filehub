<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlataformaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $plataformas = config('bots.plataformas', []);

    foreach ($plataformas as $nome => $botIdentifier) {
        // Cria a plataforma se ela não existir, mantendo o status atual se já existir.
        \App\Models\Plataforma::firstOrCreate(
            ['bot_identifier' => $botIdentifier],
            ['nome' => $nome, 'status' => 'Online']
        );
    }
}
}
