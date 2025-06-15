<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PlanosChart;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UltimosAssinantesTable;
use Filament\Pages\Dashboard as BaseDashboard;


class Dashboard extends BaseDashboard
{
    // --- Configurações da Página ---
   # protected static ?string $navigationIcon = 'heroicon-o-home'; // Ícone para a página principal
    #protected static ?string $navigationLabel = 'Dashboard';
   # protected static ?string $title = 'Dashboard - Admin';

    // Lista os widgets que devem aparecer nesta página.
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            UltimosAssinantesTable::class,
            PlanosChart::class,
        ];
    }
}