<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'commande_id',
        'montant',
        'type'
    ];

    public function commande():HasMany
    {
        return $this->hasMany(Commande::class);
    }
}
