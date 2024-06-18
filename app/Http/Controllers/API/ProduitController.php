<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProduitRequest;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class ProduitController extends Controller
{
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
