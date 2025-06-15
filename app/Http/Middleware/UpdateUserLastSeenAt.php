<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateUserLastSeenAt
{
    public function handle(Request $request, Closure $next)
    {
        // Se o usuário estiver logado...
        if (Auth::check()) {
            $user = Auth::user();
            // Define uma chave de cache única para este usuário
            $cacheKey = "is-online-{$user->id}";

            // Para evitar sobrecarregar o banco, só atualizamos a cada 1 minuto.
            // O cache guarda a informação por 60 segundos.
            Cache::put($cacheKey, true, 60);

            // Atualiza o 'last_seen_at' no banco apenas se o cache tiver expirado.
            if ($user->last_seen_at < now()->subMinutes(1)) {
                $user->update(['last_seen_at' => now()]);
            }
        }
        return $next($request);
    }
}