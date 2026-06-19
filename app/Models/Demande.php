<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
      protected $fillable = [      //Le $fillable autorise Laravel à enregistrer ces champs avec Demande::create().
        'keycloak_id',
        'nom',
        'email',
        'objet',
        'message'
    ];
}

