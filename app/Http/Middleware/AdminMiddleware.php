<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de vérification du rôle Admin
 * 
 * Ce middleware:
 * - Vérifie que l'utilisateur a le rôle 'admin'
 * - Dépend du middleware KeycloakMiddleware (doit être en avant dans la pipeline)
 * - Refuse l'accès si l'utilisateur n'est pas admin
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Les rôles ont été définis par KeycloakMiddleware
        $roles = $request->attributes->get('roles', []);

        // Vérifier si l'utilisateur a le rôle 'admin'
        if (!in_array('admin', $roles)) {
            return response()->json([
                'error' => 'Accès refusé. Rôle admin requis.'
            ], 403);
        }

        return $next($request);
    }
}
