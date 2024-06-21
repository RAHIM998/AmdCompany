<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProduitRequest;
use App\Models\Produit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Ramsey\Uuid\Rfc4122\Validator;

class ProduitController extends Controller
{

    //Fonction de recherche de produit
    public function search(Request $request)
    {
        try {
            $produit = Produit::where('libelle', 'LIKE', '%'.$request->search.'%')->orderBy('id', 'desc')->paginate(10);
            return $this->responseJson($produit, 'Produits trouvé !! ');
        }catch (Exception $exception){
            return $this->responseJson((['date' => $exception->getMessage()]));
        }
    }



    //Fonction d'affichage des produits
    public function index()
    {
        try {
            $product = Produit::Paginate(5);
            return $this->responseJson($product, 'Liste des produits');
        }catch (Exception $exception){
            return $this->responseJson(['data' => $exception->getMessage()], 'Erreur !!', 500);
        }
    }

    //Fonction de sauvegarde de produits
    public function store(ProduitRequest $request)
    {
        $user = Auth::user();
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $image = $this->imageToBlob($request->file('image'));
            $validatedData['image'] = $image;
        }

        try {
            if ($user->role === 'admin'){
                $product = Produit::create($validatedData);
                return $this->responseJson([
                    $product
                ], 'Produit sauvegardé avec succès !!');
            }else {
                return $this->responseJson(null, 'Vous n\'avez pas les droits requis pour effectuer cette action !!', 405);
            }
        }catch (Exception $exception){
            return $this->responseJson(['date' => $exception->getMessage()], 'Erreur !!', 500);
        }
    }

    //Fonction de visualisation des détails
    public function show(string $id)
    {
        try {
            $product = Produit::findOrFail($id);
            if ($product){
                return $this->responseJson([
                    'success' => true,
                    'data' => $product
                ], 'Détails des produits');
            }else{
                return $this->responseJson(null, 'Pas de produit trouvé !!', 404);
            }
        }catch (Exception $exception){
            return $this->responseJson(['data' => $exception->getMessage()], 'Erreur !!', 500);
        }
    }


    //Fonction de modification des produits
    public function update(ProduitRequest $request, string $id)
    {
        try {
            $user = Auth::user();
            $validatedData = $request->validated();
            $productUpdate = Produit::findOrFail($id);
            if ($productUpdate && $user->role === 'admin'){
                $productUpdate->update($validatedData);
                return $this->responseJson([
                    'data' => $productUpdate,
                ], 'Catégorie modifiée avec succès !!');
            }else {
                return $this->responseJson(null, 'Désolé vous n\'avez pas les droits requis !!', 404);
            }
        }catch (Exception $exception){
            return $this->responseJson(['data' => $exception->getMessage()], 'Erreur !!', 500);
        }
    }

    //Fonction de suppression des produits
    public function destroy(string $id)
    {
        try {
            $user = Auth::user();
            $productDelete = Produit::findOrFail($id);

            if ($user->role === 'admin') {
                // Supprimer le produit
                $productDelete->delete();

                return $this->responseJson([
                    'data' => $productDelete
                ], 'Produit supprimé avec succès !!');
            } else {
                return $this->responseJson(null, 'Désolé, vous n\'avez pas les droits requis !!', 403);
            }
        } catch (ModelNotFoundException $e) {
            return $this->responseJson(null, 'Produit non trouvé !!', 404);
        } catch (Exception $e) {
            return $this->responseJson(['error' => $e->getMessage()], 'Erreur lors de la suppression du produit !!', 500);
        }
    }

}
