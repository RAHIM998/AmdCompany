<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommandeRequest;
use App\Models\Commande;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use App\Notifications\StatusCommandeChangeNotification;


class CommandeController extends Controller
{
    //Fonction d'affichage des commandes
    public function index()
    {
        try {
            $user = Auth::user();
            if ($user->isAdmin() || $user->isDeliveryService()){
                $commande = Commande::with('produits')->get();
                return $this->responseJson([$commande], 'Commande retrieved successfully');
            }else{
                return $this->responseJson(null, 'Vous n\'avez pas les droits requis pour accéder aux commandes', 403);
            }
        }catch (Exception $e){
            return $this->responseJson($e->getMessage(),'Erreur',500);
        }
    }

    //Fonction d'ajout de commande
    public function store(CommandeRequest $request)
    {
        try {
            $validated = $request->validated();
            $numeroCommande = 'CMD' . date('ymd') . rand(1000, 9999);

            DB::beginTransaction();

            $totalCommande = 0;
            $prodCom = [];

            foreach ($validated['produits'] as $Product) {
                try {
                    $productSearch = Produit::findOrFail($Product['id']);
                    if ($productSearch->stock < $Product['quantite']) {
                        DB::rollBack();
                        return response()->json(['message' => "Stock insuffisant pour le produit {$productSearch->libelle}"], 400);
                    } else {
                        $prixUnitaire  = $productSearch->prix;
                        $totalCommande += $prixUnitaire * $Product['quantite'];
                        $prodCom[$Product['id']] = [
                            'quantite' => $Product['quantite'],
                            'prixUnitaire' => $prixUnitaire,
                        ];
                    }
                } catch (ModelNotFoundException $e) {
                    DB::rollBack();
                    return $this->searchproduct($e);
                }
            }

            $Commande = Commande::create([
                'user_id' => Auth::id(),
                'numeroCommande' => $numeroCommande,
                'dateCommande' => now(),
                'montant' => $totalCommande,
                'status' => 'confirmation en attente',
            ]);

            foreach ($prodCom as $produitId => $prod) {
                try {
                    $productSave = Produit::findOrFail($produitId);
                    $productSave->decrement('stock', $prod['quantite']);

                    $Commande->produits()->attach($productSave->id, $prod);
                } catch (ModelNotFoundException $e) {
                    DB::rollBack();
                    return $this->searchproduct($e);
                }
            }

            $user = Auth::user();
            $user->notify(new StatusCommandeChangeNotification($user, 'confirmation en attente'));

            DB::commit();


            return response()->json(['message' => 'Commande créée avec succès'], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseJson($e->getMessage(), 'Erreur', 500);
        }
    }

    //Fonction d'affichae des détails d'une commande
    public function show(string $id)
    {
        try {
            $commande = Commande::with('produits')->findOrFail($id);
            return response()->json([$commande], 200);
        } catch (ModelNotFoundException $e) {
            return $this->searchproduct($e);
        } catch (\Exception $e) {
            return $this->responseJson(null, $e->getMessage(), 500);
        }
    }

    //Fonction de modification d'une commande
    public function update(Request $request, string $id)
    {
        $newStatus = $request->input('status');
        $user = Auth::user();

        try {
            $commande = Commande::findOrFail($id);

            if ($commande->Transition($newStatus, $user)) {
                $commande->updateStatus($newStatus);

                return response()->json(['message' => 'Statut de la commande mis à jour avec succès'], 200);
            } else {
                return response()->json(['message' => 'Transition de statut non autorisée'], 403);
            }
        } catch (ModelNotFoundException $e) {
            return $this->searchproduct();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour du statut de la commande'], 500);
        }
    }

    //Fonction de suppression de commande
    public function destroy($id)
    {
        try {
            $commande = Commande::findOrFail($id);
            $commande->delete();
            return response()->json([$commande, 'Commande supprimée avec success'], 204);
        } catch (ModelNotFoundException $e) {
            return $this->searchproduct($e);
        } catch (\Exception $e) {
            return $this->responseJson(null, $e->getMessage(), 500);
        }
    }

}
