<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Importa nosso Model e Observer para o status da plataforma
use App\Models\Plataforma;
use App\Observers\PlataformaObserver;

// Importa os eventos de autenticação do Laravel
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

// Importa nosso Listener que vai registrar as atividades
use App\Listeners\LogActivityListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Os mapeamentos de observers para a sua aplicação.
     */
    protected $observers = [
        // Conexão que já fizemos: quando uma Plataforma é alterada, o PlataformaObserver é acionado.
        Plataforma::class => [PlataformaObserver::class],
    ];

    /**
     * Os mapeamentos de eventos e listeners para a sua aplicação.
     * AQUI ESTÁ O AJUSTE PRINCIPAL:
     */
    protected $listen = [
        // Quando o evento de Login ocorrer, o LogActivityListener será chamado.
        Login::class => [
            LogActivityListener::class,
        ],

        // O mesmo para o evento de Logout.
        Logout::class => [
            LogActivityListener::class,
        ],

        // E para o evento de tentativa de login que falhou.
        Failed::class => [
            LogActivityListener::class,
        ],
    ];


    /**
     * Registra quaisquer eventos para a sua aplicação.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determina se os eventos e listeners devem ser descobertos automaticamente.
     * Retornar false é bom quando definimos tudo manualmente, como fizemos.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}