<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;

class DemandesController extends Controller
{
    // GET /api/demandes - Lister toutes les demandes
  public function index()
{
return response()->json(Demande::all());
}

    // POST /api/demandes - Créer une nouvelle demande
  public function store(Request $request)
{
$validated = $request->validate([
'nom' => 'required|string',
'email' => 'required|email',
'objet' => 'required|string',
'message' => 'required|string'
]);

$validated['keycloak_id'] =
    $request->attributes->get('user_id');

$demande = Demande::create($validated);

return response()->json($demande, 201);

}

    // GET /api/demandes/{id} - Récupérer une demande
    public function show($id)
    {
        $demande = Demande::findOrFail($id);
        return response()->json($demande);
    }

    // PUT /api/demandes/{id} - Modifier une demande
    public function update(Request $request, $id)
    {
        $demande = Demande::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string',
            'email' => 'sometimes|email',
            'objet' => 'sometimes|string',
            'message' => 'sometimes|string'
        ]);

        $demande->update($validated);

        return response()->json($demande);
    }

    // DELETE /api/demandes/{id} - Supprimer une demande
    public function destroy($id)
    {
        $demande = Demande::findOrFail($id);
        $demande->delete();

        return response()->json(['message' => 'Demande supprimée avec succès']);
    }
}
