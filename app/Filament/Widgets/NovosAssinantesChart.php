<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class NovosAssinantesChart extends ChartWidget
{
    protected static ?string $heading = 'Novos Assinantes por Mês';
    protected static ?int $sort = 4;

    // AQUI ESTÁ A CORREÇÃO: Removemos a palavra 'static'
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $data = User::query()
            ->whereNotNull('plano_id')
            ->select(DB::raw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        return [
            'datasets' => [
                [
                    'label' => 'Novos Assinantes',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->map(fn ($item) => Carbon::createFromFormat('Y-m', $item->month)->translatedFormat('M/y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}