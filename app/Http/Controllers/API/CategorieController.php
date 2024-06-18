<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class CategorieController extends Controller
{
    //Fonction d'affichage des catégories
    public function index()
    {
        try {
            $category = Categorie::paginate(5);
            return $this->responseJson($category, 'Lsite des catégories');

        }catch (Exception $exception){
            return $this->responseJson([$exception->getMessage()], 'Erreur', 500);
        }

    }

    //Fonction d'ajout des catégories
    public function store(CategoryRequest $request)
    {
        try {
            $user = Auth::user();
            $validatedData = $request->validated();

            if ($user->role === 'admin') {
                $category = Categorie::create($validatedData);

                return $this->responseJson([
                    'category' => $category, // Retourner la catégorie créée
                ], 'Catégorie créée avec succès !!');
            } else {
                return $this->responseJson(null, 'Vous n\'avez pas les droits requis pour effectuer cette action !!', 405);
            }
        } catch (Exception $exception) {
            return $this->responseJson([$exception->getMessage()], 'Erreur !!', 500);
        }
    }


    //Fonction d'affichage des détails d'une catégorie
    public function show(string $id)
    {
        try {
            $user = Auth::user();
            if ($user->role === 'admin'){
                $category = Categorie::findOrFail($id);
                if ($category){
                    return $this->responseJson([
                        'success' => true,
                        'data' => $category
                    ], 'Détails de la catégorie');
                }else{
                    return $this->responseJson(null, 'Pas de catégorie trouvé !!', 404);
                }
            }else{
                return $this->responseJson(null, 'Vous n\'avez pas les autorisations recquise !!', 404);
            }
        }catch (Exception $exception){
            return $this->responseJson($exception->getMessage(), 'Erreur interne du serveur !!', 500);
        }
    }

    //Fonction de modification des catégories
    public function update(Request $request, string $id)
    {
        try {
            $user = Auth::user();
            $validateData = $request->validate([
                'nom' => 'required|min:2',
                'description' => 'required|min:2'
            ]);
            $category = Categorie::findOrFail($id);
            if ($category && $user->role === 'admin') {
                $category->update($validateData);
                return $this->responseJson([
                    'data' => $category,
                ], 'Catégorie modifiée avec succès !!');
            } else {
                return $this->responseJson(null, 'Désolé vous n\'avez pas les droits requis !!', 404);
            }
        } catch (Exception $exception) {
            return $this->responseJson($exception->getMessage(), 'Erreur interne du serveur !!', 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = Auth::user();
            if ($user->role === 'admin'){
                return $this->responseJson([
                    'data' => Categorie::destroy($id),
                ], 'Catégorie supprimée avec succès !!');
            }else{
                return $this->responseJson(null, 'Désolé vous n\'avez pas les droits requis !!', 404);
            }
        }catch (Exception $exception){
            return $this->responseJson($exception->getMessage(), 'Erreur interne du serveur !!', 500);
        }
    }
}
