<?php
// Em database/seeders/PlanoSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plano;

class PlanoSeeder extends Seeder
{
    public function run(): void
    {
        // Pega a lista de todos os bots do nosso arquivo de configuração
        $allBots = config('bots.plataformas');

        // Prepara a configuração para o plano Gratuito (2 cotas)
        $configGratuito = collect($allBots)->map(function ($botName, $platformName) {
            return ['plataforma' => $platformName, 'bot' => $botName, 'cotas' => 2];
        })->values()->toArray();

        // Prepara a configuração para o plano Admin (1,000,000 cotas)
        $configAdmin = collect($allBots)->map(function ($botName, $platformName) {
            return ['plataforma' => $platformName, 'bot' => $botName, 'cotas' => 1000000];
        })->values()->toArray();

        // Cria ou atualiza os planos no banco
        Plano::updateOrCreate(
            ['codigo' => 0],
            ['nome' => 'Plano Gratuito', 'preco' => 0.00, 'configuracao_bots' => $configGratuito]
        );

        Plano::updateOrCreate(
            ['codigo' => 10],
            ['nome' => 'Plano Admin', 'preco' => 0.00, 'configuracao_bots' => $configAdmin]
        );
    }
}