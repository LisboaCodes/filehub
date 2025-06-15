<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Plano;
use App\Models\Plataforma;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        // Query base para assinantes pagantes e ativos
        $activePaidSubscribersQuery = User::where('status', 'ativo')
            ->where('data_expiracao', '>=', now())
            ->whereHas('plano', fn ($query) => $query->where('preco', '>', 0));
        
        // Clona a query para obter o total sem executar a soma ainda
        $totalAssinantesPagantes = (clone $activePaidSubscribersQuery)->count();

        // Calcula a receita mensal com base na query clonada
        $receitaMensalPrevista = (clone $activePaidSubscribersQuery)
            ->join('planos', 'users.plano_id', '=', 'planos.id')
            ->sum('planos.preco');

        // Calcula os novos assinantes do mês
        $novosAssinantesMes = User::where('status', 'ativo')
            ->whereNotNull('plano_id')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        
        // Calcula os cancelados ou expirados no mês
        $canceladosExpiradosMes = User::whereIn('status', ['desativado', 'inadimplente', 'banido'])
            ->whereBetween('data_expiracao', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        // Calcula o Ticket Médio
        $ticketMedio = ($totalAssinantesPagantes > 0) ? ($receitaMensalPrevista / $totalAssinantesPagantes) : 0;

        return [
            Stat::make('Usuários Ativos (Total)', User::where('status', 'ativo')->count())
                ->icon('heroicon-o-users'),

            Stat::make('Assinantes Pagantes', $totalAssinantesPagantes)
                ->icon('heroicon-o-user-group'),

            Stat::make('Receita Mensal Prevista', 'R$ ' . number_format($receitaMensalPrevista, 2, ',', '.'))
                ->description('Baseado em assinantes pagantes ativos')
                ->icon('heroicon-o-chart-bar-square')
                ->color('success'),

            Stat::make('Novos Assinantes (Mês)', $novosAssinantesMes)
                ->icon('heroicon-o-user-plus'),

            Stat::make('Cancel./Expirados (Mês)', $canceladosExpiradosMes)
                ->description('Estimativa de inativos no mês')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            
            Stat::make('Ticket Médio', 'R$ ' . number_format($ticketMedio, 2, ',', '.'))
                ->description('Por assinante pagante ativo')
                ->icon('heroicon-o-receipt-refund'),
        ];
    }
}