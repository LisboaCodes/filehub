<?php

namespace App\Providers;

use App\Models\ConfiguracaoGlobal;
use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Não é necessário alterar aqui.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usamos um try-catch como medida de segurança. Ele evita que comandos
        // do artisan (como migrate) quebrem ao tentar acessar o banco antes
        // das tabelas existirem.
        try {
            // PASSO 1: Registrar os dados como "Singletons".
            // Isso garante que a consulta ao banco de dados para cada item
            // aconteça apenas UMA VEZ por requisição. O resultado fica guardado na memória
            // durante o carregamento daquela página.
            $this->app->singleton('configuracaoGlobal', function () {
                return ConfiguracaoGlobal::first();
            });

            $this->app->singleton('menusNavegacao', function () {
                return Menu::orderBy('ordem', 'asc')->get();
            });

            // PASSO 2: Compartilhar os dados já carregados com todas as views.
            // O View::composer agora não faz novas consultas, ele apenas pega
            // os dados que o singleton já guardou e os disponibiliza.
            View::composer('*', function ($view) {
                $view->with('config', $this->app->make('configuracaoGlobal'));
                $view->with('menus', $this->app->make('menusNavegacao'));
            });

        } catch (\Exception $e) {
            // Em caso de erro (ex: tabela não existe durante a instalação),
            // o sistema simplesmente ignora e continua, sem quebrar.
        }
    }
}