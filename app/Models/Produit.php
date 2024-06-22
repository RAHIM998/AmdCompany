<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'categorie_id',
        'libelle',
        'prix',
        'stock',
        'image',
        'description',
    ];


    public function categories(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }


    public function commande(): BelongsToMany
    {
        return $this->belongsToMany(Commande::class, 'produit_commandes')->withPivot('quantite', 'prixUnitaire');
    }
}
