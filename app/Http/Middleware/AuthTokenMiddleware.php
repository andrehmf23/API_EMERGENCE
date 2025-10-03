<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

//Manipula a requisição HTTP e verifica o token de autenticação.
class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        //obtem o token do cabeçalho atualizado
        $token = $request->bearerToken();

        //verifica se o token é válido
        if (!$token || !User::where('api_token', $token)->exists()) {
            return response()->json(['message' => 'Não autorizado'], 401);
        }

        //permite que a resição continue
        return $next($request);
    }
}
