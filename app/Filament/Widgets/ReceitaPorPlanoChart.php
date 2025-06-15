<?php

namespace App\Filament\Widgets;

use App\Models\Plano;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class ReceitaPorPlanoChart extends ChartWidget
{
    protected static ?string $heading = 'Receita Estimada por Plano';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $planos = Plano::withCount('users')->get();

        $labels = [];
        $valores = [];

        foreach ($planos as $plano) {
            $labels[] = $plano->nome;

            // trata o valor do plano, remove R$ e converte para float
            $preco = floatval(str_replace(['R$', ',', ' '], ['', '.', ''], $plano->preco ?? 0));

            $valores[] = $preco * $plano->users_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Receita Estimada (R$)',
                    'data' => $valores,
                    'backgroundColor' => [
                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                        '#EC4899', '#14B8A6', '#6366F1', '#84CC16',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
