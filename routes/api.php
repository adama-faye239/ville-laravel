<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Contact;
use App\Models\Demande;
use App\Http\Controllers\DemandesController;

Route::get('/contacts', function () {
    return response()->json(Contact::all());
});

Route::post('/contact', function (Request $request) {

    $validated = $request->validate([
        'nom' => 'nullable',
        'email' => 'nullable|email',
        'message' => 'nullable'
    ]);

    Contact::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Contact enregistré avec succès'
    ]);
});

Route::middleware('keycloak')->get('/profile', function (Request $request) {

    try {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return response()->json(['error' => 'Token invalide'], 401);
        }

        $payload = json_decode(
            base64_decode(
                str_replace(['-', '_'], ['+', '/'], $parts[1])
            ),
            true
        );

        return response()->json($payload);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
});

// Endpoint de test pour déboguer les tokens
Route::middleware('keycloak')->get('/test-token', function (Request $request) {
    try {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return response()->json(['error' => 'Token invalide'], 401);
        }
        
        $payload = json_decode(
            base64_decode(
                str_replace(['-', '_'], ['+', '/'], $parts[1])
            ),
            true
        );

        return response()->json([
            'success' => true,
            'message' => 'Token reçu correctement',
            'token' => $token,
            'payload' => $payload,
            'roles' => $payload['realm_access']['roles'] ?? [],
            'is_admin' => in_array('admin', $payload['realm_access']['roles'] ?? [])
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
});

Route::middleware(['keycloak', 'admin'])->get('/admin', function () {

    return response()->json([
        'message' => 'Bienvenue administrateur'
    ]);

});


// Routes pour les demandes (protégées par admin)
Route::middleware(['keycloak'])->group(function () {
    Route::get('/demandes', [DemandesController::class, 'index']);
    Route::post('/demandes', [DemandesController::class, 'store']);
    Route::get('/demandes/{id}', [DemandesController::class, 'show']);
    Route::put('/demandes/{id}', [DemandesController::class, 'update']);
    Route::delete('/demandes/{id}', [DemandesController::class, 'destroy']);
});



