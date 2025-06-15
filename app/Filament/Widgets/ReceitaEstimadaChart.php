<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB; // Importante para otimizar a consulta

class ReceitaEstimadaChart extends ChartWidget
{
    protected static ?string $heading = 'Receita Estimada por Mês';
    protected static ?int $sort = 3; // Ordem de exibição no dashboard
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = User::query()
            // AQUI ESTÁ A CORREÇÃO PRINCIPAL:
            // Filtramos por plano_id e usamos a relação para verificar o preço
            ->whereNotNull('plano_id')
            ->whereHas('plano', fn ($query) => $query->where('preco', '>', 0))
            // Agrupamos por mês e ano de criação
            ->select(DB::raw('SUM(planos.preco) as receita, DATE_FORMAT(users.created_at, "%Y-%m") as mes'))
            ->join('planos', 'users.plano_id', '=', 'planos.id')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            // Pegamos os dados dos últimos 6 meses
            ->limit(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Receita (R$)',
                    'data' => $data->pluck('receita')->toArray(),
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(251, 146, 60, 0.2)',
                ],
            ],
            'labels' => $data->map(fn ($item) => Carbon::createFromFormat('Y-m', $item->mes)->translatedFormat('M/y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}