<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plano;

class PlanoSeeder extends Seeder
{
    /**
     * Executa os seeds do banco de dados para criar todos os planos.
     */
    public function run(): void
    {
        // --- Definição dos Planos Comerciais (baseado na sua tabela) ---
        $botsComerciaisConfig = [
            'Freepik'         => ['bot' => 'bot_freepik',         'cotas' => ['Básico' => 10, 'Premium' => 20, 'Plus' => 30, 'Elite' => 40]],
            'Designi'         => ['bot' => 'bot_designi',         'cotas' => ['Básico' => 10, 'Premium' => 20, 'Plus' => 45, 'Elite' => 70]],
            'BaixarDesign'    => ['bot' => 'bot_baixardesign',    'cotas' => ['Básico' => 5,  'Premium' => 10, 'Plus' => 25, 'Elite' => 50]],
            'Envato'          => ['bot' => 'bot_envato',          'cotas' => ['Básico' => 5,  'Premium' => 15, 'Plus' => 35, 'Elite' => 60]],
            'AdobeStock'      => ['bot' => 'bot_adobestock',      'cotas' => ['Básico' => null, 'Premium' => 2,  'Plus' => 10, 'Elite' => 15]],
            'Flaticon'        => ['bot' => 'bot_flaticon',        'cotas' => ['Básico' => 5,  'Premium' => 10, 'Plus' => 35, 'Elite' => 50]],
            'Vecteezy'        => ['bot' => 'bot_vecteezy',        'cotas' => ['Básico' => 5,  'Premium' => 10, 'Plus' => 25, 'Elite' => 40]],
            'Designbr'        => ['bot' => 'bot_designbr',        'cotas' => ['Básico' => null, 'Premium' => null, 'Plus' => 5,  'Elite' => 10]],
            'Pixeden'         => ['bot' => 'bot_pixeden',         'cotas' => ['Básico' => 5,  'Premium' => 10, 'Plus' => 25, 'Elite' => 50]],
            'Artlist'         => ['bot' => 'bot_artlist',         'cotas' => ['Básico' => 5,  'Premium' => 10, 'Plus' => 15, 'Elite' => 30]],
            'MockupCloud'     => ['bot' => 'bot_mockupcloud',     'cotas' => ['Básico' => 5,  'Premium' => 10, 'Plus' => 15, 'Elite' => 30]],
            'MotionArray'     => ['bot' => 'bot_motionarray',     'cotas' => ['Básico' => 5,  'Premium' => 10, 'Plus' => 15, 'Elite' => 30]],
            'MotionElements'  => ['bot' => 'bot_motionelements',  'cotas' => ['Básico' => null, 'Premium' => null, 'Plus' => 10, 'Elite' => 20]],
            'Shutterstock'   => ['bot' => 'bot_shutterstock',   'cotas' => ['Básico' => null, 'Premium' => null, 'Plus' => 5,  'Elite' => 10]],
            'Istock'          => ['bot' => 'bot_istock',          'cotas' => ['Básico' => null, 'Premium' => null, 'Plus' => 5,  'Elite' => 10]],
            'EpidemicSound'   => ['bot' => 'bot_epidemicsound',   'cotas' => ['Básico' => null, 'Premium' => null, 'Plus' => 10, 'Elite' => 20]],
            'CreativeFabrica' => ['bot' => 'bot_creativefabrica', 'cotas' => ['Básico' => null, 'Premium' => 5,  'Plus' => 10, 'Elite' => 30]],
            'Lovepik'         => ['bot' => 'bot_lovepik',         'cotas' => ['Básico' => null, 'Premium' => 10, 'Plus' => 20, 'Elite' => 40]],
            'IconScout'       => ['bot' => 'bot_iconscout',       'cotas' => ['Básico' => null, 'Premium' => 10, 'Plus' => 15, 'Elite' => 20]],
            'RawPixel'        => ['bot' => 'bot_rawpixel',        'cotas' => ['Básico' => null, 'Premium' => 10, 'Plus' => 15, 'Elite' => 30]],
            'StoryBlocks'     => ['bot' => 'bot_storyblocks',     'cotas' => ['Básico' => null, 'Premium' => 10, 'Plus' => 20, 'Elite' => 40]],
            'Deezy'           => ['bot' => 'bot_deezy',           'cotas' => ['Básico' => null, 'Premium' => 15, 'Plus' => 25, 'Elite' => 40]],
        ];
        
        // --- Definição dos Planos de Sistema ---
        $allPlatforms = config('bots.plataformas', []);

        // Lista completa de TODOS os planos que queremos no banco
        $planos = [
            ['codigo' => 1, 'nome' => 'Básico', 'preco' => 39.90],
            ['codigo' => 2, 'nome' => 'Premium', 'preco' => 59.90],
            ['codigo' => 3, 'nome' => 'Plus', 'preco' => 99.90],
            ['codigo' => 4, 'nome' => 'Elite', 'preco' => 159.90],
            ['codigo' => 7, 'nome' => 'Colaborador', 'preco' => 0.00],
            ['codigo' => 8, 'nome' => 'Gratuito', 'preco' => 0.00],
            ['codigo' => 9, 'nome' => 'Desativado', 'preco' => 0.00],
            ['codigo' => 10, 'nome' => 'Admin', 'preco' => 0.00],
        ];

        // Loop para criar ou atualizar cada plano
        foreach ($planos as $planoData) {
            $configBotsPlano = [];

            // Monta a configuração de bots baseado no nome do plano
            if (in_array($planoData['nome'], ['Básico', 'Premium', 'Plus', 'Elite'])) {
                foreach ($botsComerciaisConfig as $plataforma => $botData) {
                    if (isset($botData['cotas'][$planoData['nome']]) && $botData['cotas'][$planoData['nome']] !== null) {
                        $configBotsPlano[] = ['plataforma' => $plataforma, 'bot' => $botData['bot'], 'cotas' => $botData['cotas'][$planoData['nome']]];
                    }
                }
            } else {
                $cotas = match ($planoData['nome']) {
                    'Colaborador' => 100,
                    'Gratuito' => 2,
                    'Admin' => 1000,
                    default => 0, // Desativado e outros casos
                };
                foreach ($allPlatforms as $platformName => $botName) {
                    $configBotsPlano[] = ['plataforma' => $platformName, 'bot' => $botName, 'cotas' => $cotas];
                }
            }

            // Cria ou atualiza o plano no banco de dados
            Plano::updateOrCreate(
                ['codigo' => $planoData['codigo']],
                [
                    'nome' => $planoData['nome'],
                    'preco' => $planoData['preco'],
                    'configuracao_bots' => json_encode($configBotsPlano)
                ]
            );
        }
    }
}