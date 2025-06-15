<?php

namespace App\Filament\Widgets;

use App\Models\Plano;
use Filament\Widgets\ChartWidget;

class PlanosChart extends ChartWidget
{
    protected static ?string $heading = 'Distribuição de Planos';
    protected static ?string $maxHeight = '300px';
    protected static ?int $sort = 7;
    // Gráfico ocupa a largura total da tela
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Busca todos os planos que têm pelo menos um usuário associado
        $planos = Plano::withCount('users')->having('users_count', '>', 0)->get();

        return [
            'datasets' => [
                [
                    'label' => 'Usuários por Plano',
                    'data' => $planos->pluck('users_count')->toArray(),
                    'backgroundColor' => [
                        '#36A2EB', '#FF6384', '#FFCE56',
                        '#4BC0C0', '#9966FF', '#FF9F40',
                    ],
                ],
            ],
            'labels' => $planos->pluck('nome')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Tipo do gráfico: rosquinha/pizza
    }
}
