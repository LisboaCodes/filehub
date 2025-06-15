<?php

use App\Models\ConfiguracaoGlobal;
use App\Models\Menu;
use App\Models\Pagina;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rotas mais específicas devem ser declaradas ANTES de rotas dinâmicas "coringa".
|
*/

// Rota Padrão do Laravel (Homepage)
Route::get('/', function () {
    // Esta rota já tem acesso às variáveis $config e $menus do AppServiceProvider.
    return view('welcome');
});

// Rota de Teste (Exemplo de rota específica)
Route::get('/teste', function () {
    // Esta rota funciona porque vem ANTES da rota de páginas dinâmicas.
    $config = ConfiguracaoGlobal::first();
    $menus = Menu::orderBy('ordem', 'asc')->get();

    if (!$config) {
        return 'Nenhuma configuração global encontrada.';
    }
    
    return view('teste', [
        'config' => $config,
        'menus' => $menus
    ]);
});

// Rota para o usuário ver suas notificações
Route::get('/minhas-notificacoes', function () {
    $user = Auth::user();
    
    // Pega todas as notificações do usuário, das mais novas para as mais antigas
    $notificacoes = $user->notificacoes()->latest()->get();
    
    // MARCA COMO LIDAS: Atualiza todas as notificações que ainda não foram lidas
    $user->notificacoes()->wherePivotNull('read_at')->update(['notificacao_user.read_at' => now()]);

    return view('minhas-notificacoes', ['notificacoes' => $notificacoes]);
    
})->middleware('auth')->name('notificacoes.minhas');


// ========================================================================
// ATENÇÃO: Esta rota dinâmica deve ser a ÚLTIMA rota deste arquivo.
// ========================================================================
Route::get('/{pagina:slug}', function (Pagina $pagina) {
    
    // Verifica se a página NÃO está publicada E se o usuário NÃO é um admin logado
    if (!$pagina->is_published && (!Auth::check() || Auth::user()->nivel_acesso !== 'admin')) {
        abort(404); // Mostra erro 404 (Página não encontrada)
    }

    // Se a página estiver publicada (ou se for um admin), envia os dados para a view.
    return view('pagina', ['pagina' => $pagina]);

})->name('pagina.show');