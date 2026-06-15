<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Contact;

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

    return response()->json([
        'message' => 'Route protégée par Keycloak'
    ]);

});