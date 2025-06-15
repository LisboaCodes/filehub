<?php

namespace App\Filament\Widgets;

use App\Models\Plano;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RankingPlanosTable extends BaseWidget
{
    protected static ?string $heading = 'Ranking de Planos Mais Vendidos';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Plano::withCount('users')
            ->orderByDesc('users_count');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nome')
                ->label('Plano')
                ->sortable()
                ->searchable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('preco')
                ->label('Preço')
                ->formatStateUsing(fn ($state) => 'R$ ' . str_replace('.', ',', $state)),

            Tables\Columns\TextColumn::make('users_count')
                ->label('Total de Assinantes')
                ->counts('users')
                ->sortable()
                ->badge()
                ->color('primary'),
        ];
    }
}
