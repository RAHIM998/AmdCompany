<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commande extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'numeroCommande',
        'dateCommande',
        'montant',
        'status'
    ];

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class, 'produit_commandes')->withPivot('quantite', 'prixUnitaire');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function retour(): BelongsTo
    {
        return $this->belongsTo(Retour::class);
    }

    public function paiements(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

}
