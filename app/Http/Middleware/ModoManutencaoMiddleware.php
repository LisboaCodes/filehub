<?php

namespace App\Http\Middleware;

use App\Models\ConfiguracaoGlobal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ModoManutencaoMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        
        // Busca a configuração do banco de dados
        $config = ConfiguracaoGlobal::first();

        // Se o modo manutenção NÃO estiver ativo, ou se não houver configuração,
        // deixa todo mundo passar normalmente.
        if (!$config || !$config->modo_manutencao) {
            return $next($request);
        }

        // Se chegou até aqui, o modo de manutenção está ATIVO.
        // Agora verificamos se o usuário é um admin.
        // Se for, ele também pode passar.
        if (Auth::check() && Auth::user()->nivel_acesso === 'admin') {
            return $next($request);
        }

        // Se não for admin e o modo manutenção estiver ativo,
        // mostra a página de manutenção customizada.
        return response()->view('manutencao');
    }
}