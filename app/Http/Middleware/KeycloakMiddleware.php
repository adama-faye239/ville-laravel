<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de validation du token JWT Keycloak
 * 
 * Ce middleware:
 * - Récupère le token JWT en header Authorization
 * - Valide le format du token (3 parties: header.payload.signature)
 * - Décorateur la requête avec les infos du token
 */
class KeycloakMiddleware
{
    public function handle(Request $request, Closure $next): Response
{
    // Récupérer le header Authorization
    $authorization = $request->header('Authorization');

    // dd($authorization);
    $authorization = $request->header('Authorization');

    \Log::info('AUTHORIZATION HEADER', [
    'authorization' => $authorization
     ]);

    if (!$authorization) {
        return response()->json(['error' => 'Token manquant'], 401);
    }

        // Extraire le token (enlever "Bearer ")
        $token = str_replace('Bearer ', '', $authorization);
        $parts = explode('.', $token);

        // Valider le format JWT (3 parties)
        if (count($parts) !== 3) {
            return response()->json(['error' => 'Token invalide'], 401);
        }

        try {
            // Décoder le payload (sans vérifier la signature, Keycloak le fait)
            $payload = json_decode(
                base64_decode(
                    str_replace(['-', '_'], ['+', '/'], $parts[1])
                ),
                true
            );

            if (!$payload) {
                return response()->json(['error' => 'Payload JWT invalide'], 401);
            }

            // Stocker les infos du token dans la requête
            \Log::info('PAYLOAD JWT', [
             'payload' => $payload
        ]);

           \Log::info('USER ID', [
           'user_id' => $payload['sub'] ?? null
         ]);
            $request->attributes->set('keycloak_token', $payload);
            $request->attributes->set('user_id', $payload['sub'] ?? null);
            $request->attributes->set('username', $payload['preferred_username'] ?? null);
            $request->attributes->set('roles', $payload['realm_access']['roles'] ?? []);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur décodage token: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
