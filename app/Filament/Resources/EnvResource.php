<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnvResource\Pages\ManageEnv;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class EnvResource extends Resource
{
    // O seu resource não tem um Model associado, e isso está correto
    // para uma página customizada como esta.

    // ===================================================================
    // AQUI ESTÁ A CORREÇÃO - ADICIONE ESTAS 3 LINHAS
    // ===================================================================
    protected static ?string $navigationIcon = 'heroicon-o-key'; // Ícone de chave, bom para variáveis de ambiente
    protected static ?string $navigationGroup = 'Sistema'; // Move para o grupo "Sistema"
    protected static ?int $navigationSort = 101; // Ordem para aparecer depois das "Configurações Globais"

    /**
     * Esta função é ótima para garantir que apenas admins vejam este item de menu.
     * Está perfeita.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->nivel_acesso === 'admin';
    }

    /**
     * O form está vazio pois a lógica do formulário está na sua página customizada (ManageEnv).
     * Está correto.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    /**
     * As páginas estão configuradas para apontar para sua classe ManageEnv.
     * Está perfeito.
     */
    public static function getPages(): array
    {
        return [
            'index' => ManageEnv::route('/'),
        ];
    }
}