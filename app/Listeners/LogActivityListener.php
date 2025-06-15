<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class LogActivityListener
{
    public function handle(object $event): void
    {
        // Se o evento for um Login bem-sucedido...
        if ($event instanceof Login) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'event_type' => 'Login',
                'description' => "O usuário '{$event->user->name}' fez login.",
                'properties' => ['ip_address' => request()->ip(), 'user_agent' => request()->userAgent()],
            ]);
        }
        // Se o evento for um Logout...
        elseif ($event instanceof Logout) {
            if ($event->user) { // Garante que havia um usuário logado
                ActivityLog::create([
                    'user_id' => $event->user->id,
                    'event_type' => 'Logout',
                    'description' => "O usuário '{$event->user->name}' fez logout.",
                    'properties' => ['ip_address' => request()->ip()],
                ]);
            }
        }
        // Se o evento for uma tentativa de Login que FALHOU...
        elseif ($event instanceof Failed) {
            $email = $event->credentials['email'] ?? 'N/A';
            ActivityLog::create([
                'event_type' => 'Failed Login',
                'description' => "Tentativa de login falhou para o e-mail: {$email}.",
                'properties' => ['ip_address' => request()->ip(), 'user_agent' => request()->userAgent()],
            ]);
        }
    }
}