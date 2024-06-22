<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommandeRequest;
use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class CommandeController extends Controller
{
    //Fonction d'affichage des commandes
    public function index()
    {
        $commande = Commande::with('produits')->get();

        return $this->responseJson([$commande], 'Commande retrieved successfully');
    }

    //Fonction d'ajout de commande
    public function store(CommandeRequest $request)
    {
        try {
            $validated = $request->validated();

            // Calcul du total de la commande
            $montantTotal = 0;
            $produitDetails = [];

            // Vérification de la disponibilité du produit et calcul du montant total
            foreach ($validated['produits'] as $produit) {
                $prodctType = Produit::findOrFail($produit['id']);
                $prixUnitaire = $prodctType->prix;

                //Vérification de la disponibilité
                if ($prodctType->stock < $produit['quantite']) {
                    return response()->json(['message' => "Stock insuffisant pour le produit {$prodctType->libelle}"], 400);
                }

                //Calcul du montant total
                $montantTotal += $prixUnitaire * $produit['quantite'];

                //chargement des détaisl
                $produitDetails[$produit['id']] = [
                    'quantite' => $produit['quantite'],
                    'prixUnitaire' => $prixUnitaire
                ];
            }

            // Génération du numéro de commande
            $numeroCommande = 'CMD'.date('ymd').rand(1000, 9999);

            DB::transaction(function () use ($validated, $montantTotal, $numeroCommande, $produitDetails) {
                // Création de la commande
                $commande = Commande::create([
                    'user_id' => Auth::id(),
                    'numeroCommande' => $numeroCommande,
                    'dateCommande' => now(),
                    'montant' => $montantTotal,
                    'status' => 'confirmation en attente',
                ]);

                // Mise à jour du stock et insertion des produits dans la table pivot
                foreach ($produitDetails as $produitId => $details) {
                    $produit = Produit::findOrFail($produitId);
                    $produit->decrement('stock', $details['quantite']);
                    $commande->produits()->attach($produitId, $details);
                }
            });

            return response()->json(['message' => 'Commande créée avec succès'], 201);
        }catch (Exception $exception){
            return $this->responseJson([], $exception->getMessage(), 400);
        }
    }

    //Fonction d'affichae des détails d'une commande
    public function show(string $id)
    {
        try {
            $commande = Commande::with('produits')->findOrFail($id);
            return response()->json([$commande], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Commande not found'], 404);
        }
    }

    //Fonction de modification d'une commande
    public function update(CommandeRequest $request, string $id)
    {
        //
    }

    //Fonction de suppression de commande
    public function destroy($id)
    {
        try {
            $commande = Commande::findOrFail($id);
            $commande->delete();
            return response()->json([$commande, 'Commande supprimée avec success'], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Commande non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression de la commande'], 500);
        }
    }

}
